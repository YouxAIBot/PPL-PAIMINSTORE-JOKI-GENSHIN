import 'package:dio/dio.dart';
import '../../../core/network/api_error.dart';
import '../../../core/network/dio_provider.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../order/data/order_repository.dart';

class GatewayLink {
  final String redirectUrl;
  const GatewayLink(this.redirectUrl);

  factory GatewayLink.fromJson(Map<String, dynamic> j) =>
      GatewayLink(j['redirect_url']);
}

abstract class PaymentRepository {
  Future<GatewayLink> createGateway(String orderNo);

  Future<String> status(String orderNo);
}

class FakePaymentRepository implements PaymentRepository {
  final FakeOrderRepository orders;
  final _started = <String>{};

  FakePaymentRepository(this.orders);

  @override
  Future<GatewayLink> createGateway(String orderNo) async {
    await Future.delayed(const Duration(milliseconds: 400));
    _started.add(orderNo);
    return GatewayLink('fake://snap/$orderNo');
  }

  @override
  Future<String> status(String orderNo) async {
    await Future.delayed(const Duration(milliseconds: 300));
    if (!_started.contains(orderNo)) return 'pending';
    await orders.markPaid(orderNo);
    return 'paid';
  }
}

class ApiPaymentRepository implements PaymentRepository {
  final Dio dio;
  ApiPaymentRepository(this.dio);

  @override
  Future<GatewayLink> createGateway(String orderNo) => guard(() async {
        final r = await dio.post('/orders/$orderNo/payments/gateway');
        return GatewayLink.fromJson(r.data['data']);
      });

  @override
  Future<String> status(String orderNo) => guard(() async {
        final r = await dio.get('/orders/$orderNo/payment');
        return r.data['data']['status'] as String;
      });
}

final paymentRepositoryProvider = Provider<PaymentRepository>((ref) {
  const useFake = bool.fromEnvironment('USE_FAKE', defaultValue: true);
  return useFake
      ? FakePaymentRepository(ref.read(orderRepositoryProvider) as FakeOrderRepository)
      : ApiPaymentRepository(ref.read(dioProvider));
});

Future<bool> waitPaid(
  PaymentRepository repo,
  String orderNo, {
  Duration interval = const Duration(seconds: 3),
  int maxTries = 40,
}) async {
  for (var i = 0; i < maxTries; i++) {
    final s = await repo.status(orderNo);
    if (s == 'paid') return true;
    if (s == 'failed' || s == 'expired') return false;
    await Future.delayed(interval);
  }
  return false;
}
