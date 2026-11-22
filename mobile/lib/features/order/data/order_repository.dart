import 'package:dio/dio.dart';
import '../../../core/network/api_error.dart';
import '../../../core/network/dio_provider.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../catalog/data/catalog_repository.dart';

class Order {
  final String orderNo;
  final String status;
  final String packageName;
  final int total;
  final int? queuePosition;
  final DateTime? expiresAt;

  const Order({
    required this.orderNo,
    required this.status,
    required this.packageName,
    required this.total,
    this.queuePosition,
    this.expiresAt,
  });

  factory Order.fromJson(Map<String, dynamic> j) => Order(
        orderNo: j['order_no'],
        status: j['status'],
        packageName: j['package']?['name'] ?? '-',
        total: (j['total_amount'] as num).toInt(),
        queuePosition: j['queue_position'],
        expiresAt: j['expires_at'] == null
            ? null
            : DateTime.parse(j['expires_at']).toLocal(),
      );

  Order copyWith({String? status, int? queuePosition}) => Order(
        orderNo: orderNo,
        status: status ?? this.status,
        packageName: packageName,
        total: total,
        queuePosition: queuePosition ?? this.queuePosition,
        expiresAt: expiresAt,
      );
}

class QueueInfo {
  final String status;
  final int? position;
  final int ordersAhead;
  final int activeBoosters;
  final int? estimateHours;

  const QueueInfo({
    required this.status,
    this.position,
    this.ordersAhead = 0,
    this.activeBoosters = 0,
    this.estimateHours,
  });

  factory QueueInfo.fromJson(Map<String, dynamic> j) => QueueInfo(
        status: j['status'],
        position: j['position'],
        ordersAhead: j['orders_ahead'] ?? 0,
        activeBoosters: j['active_boosters'] ?? 0,
        estimateHours: j['estimate_hours'],
      );
}

abstract class OrderRepository {
  Future<Order> create({
    required int packageId,
    required String uid,
    required String server,
    required String username,
    required String password,
    String? notes,
  });
  Future<List<Order>> list({String? status});
  Future<Order> detail(String orderNo);
  Future<QueueInfo> queue(String orderNo);
}

class FakeOrderRepository implements OrderRepository {
  final CatalogRepository catalog;
  final _orders = <Order>[];

  FakeOrderRepository(this.catalog);

  @override
  Future<Order> create({
    required int packageId,
    required String uid,
    required String server,
    required String username,
    required String password,
    String? notes,
  }) async {
    await Future.delayed(const Duration(milliseconds: 600));
    final pkg = await catalog.package(packageId);
    final no = 'PMS-${DateTime.now().year}-'
        '${(_orders.length + 1).toString().padLeft(3, '0')}';
    final order = Order(
      orderNo: no,
      status: 'pending_payment',
      packageName: pkg.name,
      total: pkg.price,
      expiresAt: DateTime.now().add(const Duration(hours: 24)),
    );
    _orders.insert(0, order);
    return order;
  }

  @override
  Future<List<Order>> list({String? status}) async {
    await Future.delayed(const Duration(milliseconds: 300));
    return status == null
        ? List.of(_orders)
        : _orders.where((o) => o.status == status).toList();
  }

  @override
  Future<Order> detail(String orderNo) async =>
      _orders.firstWhere((o) => o.orderNo == orderNo);

  @override
  Future<QueueInfo> queue(String orderNo) async {
    await Future.delayed(const Duration(milliseconds: 300));
    final o = _orders.firstWhere((o) => o.orderNo == orderNo);
    if (o.status != 'queued') return QueueInfo(status: o.status);
    final ahead = (o.queuePosition ?? 1) - 1;
    return QueueInfo(
      status: 'queued',
      position: o.queuePosition,
      ordersAhead: ahead,
      activeBoosters: 2,
      estimateHours: (ahead * 12 / 2).ceil(),
    );
  }

  Future<void> markPaid(String orderNo) async {
    final i = _orders.indexWhere((o) => o.orderNo == orderNo);
    if (i < 0 || _orders[i].status != 'pending_payment') return;
    final queued = _orders.where((o) => o.status == 'queued').length;
    _orders[i] = _orders[i].copyWith(status: 'queued', queuePosition: queued + 1);
  }
}

class ApiOrderRepository implements OrderRepository {
  final Dio dio;
  ApiOrderRepository(this.dio);

  @override
  Future<Order> create({
    required int packageId,
    required String uid,
    required String server,
    required String username,
    required String password,
    String? notes,
  }) =>
      guard(() async {
        final form = FormData.fromMap({
          'package_id': packageId,
          'uid': uid,
          'server': server,
          'game_username': username,
          'game_password': password,
          'accept_disclaimer': '1',
          if (notes != null) 'notes': notes,
        });
        final r = await dio.post('/orders', data: form);
        return Order.fromJson(r.data['data']);
      });

  @override
  Future<List<Order>> list({String? status}) => guard(() async {
        final r = await dio.get('/orders',
            queryParameters: {if (status != null) 'status': status});
        return (r.data['data'] as List).map((e) => Order.fromJson(e)).toList();
      });

  @override
  Future<Order> detail(String orderNo) => guard(() async {
        final r = await dio.get('/orders/$orderNo');
        return Order.fromJson(r.data['data']);
      });

  @override
  Future<QueueInfo> queue(String orderNo) => guard(() async {
        final r = await dio.get('/orders/$orderNo/queue');
        return QueueInfo.fromJson(r.data['data']);
      });
}

final orderRepositoryProvider = Provider<OrderRepository>((ref) {
  const useFake = bool.fromEnvironment('USE_FAKE', defaultValue: true);
  return useFake
      ? FakeOrderRepository(ref.read(catalogRepositoryProvider))
      : ApiOrderRepository(ref.read(dioProvider));
});

final orderDetailProvider =
    FutureProvider.autoDispose.family<Order, String>((ref, orderNo) {
  return ref.watch(orderRepositoryProvider).detail(orderNo);
});

final ordersProvider = FutureProvider.autoDispose<List<Order>>((ref) {
  return ref.watch(orderRepositoryProvider).list();
});

final queueProvider =
    FutureProvider.autoDispose.family<QueueInfo, String>((ref, orderNo) {
  return ref.watch(orderRepositoryProvider).queue(orderNo);
});
