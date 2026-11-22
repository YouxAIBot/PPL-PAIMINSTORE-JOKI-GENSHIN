import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../catalog/data/catalog_repository.dart';

class Order {
  final String orderNo;
  final String status; // pending_payment, queued, in_progress, completed, cancelled
  final String packageName;
  final int total;
  final int? queuePosition;

  const Order({
    required this.orderNo,
    required this.status,
    required this.packageName,
    required this.total,
    this.queuePosition,
  });

  factory Order.fromJson(Map<String, dynamic> j) => Order(
    orderNo: j['order_no'],
    status: j['status'],
    packageName: j['package']?['name'] ?? '-',
    total: (j['total_amount'] as num).toInt(),
    queuePosition: j['queue_position'],
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
}

// Diisi di Langkah 8.
class ApiOrderRepository implements OrderRepository {
  @override
  Future<Order> create({
    required int packageId,
    required String uid,
    required String server,
    required String username,
    required String password,
    String? notes,
  }) =>
      throw UnimplementedError();
  @override
  Future<List<Order>> list({String? status}) => throw UnimplementedError();
  @override
  Future<Order> detail(String orderNo) => throw UnimplementedError();
}

final orderRepositoryProvider = Provider<OrderRepository>((ref) {
  const useFake = bool.fromEnvironment('USE_FAKE', defaultValue: true);
  return useFake
      ? FakeOrderRepository(ref.read(catalogRepositoryProvider))
      : ApiOrderRepository();
});