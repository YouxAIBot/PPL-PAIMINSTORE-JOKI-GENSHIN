import 'package:flutter_test/flutter_test.dart';
import 'package:paiminstore_mobile/features/catalog/data/catalog_repository.dart';
import 'package:paiminstore_mobile/features/order/data/order_repository.dart';
import 'package:paiminstore_mobile/features/payment/data/payment_repository.dart';

class _SeqRepo implements PaymentRepository {
  final List<String> seq;
  int i = 0;
  _SeqRepo(this.seq);

  @override
  Future<GatewayLink> createGateway(String orderNo) async =>
      const GatewayLink('x');

  @override
  Future<String> status(String orderNo) async => seq[i++ % seq.length];
}

void main() {
  test('waitPaid true setelah beberapa kali pending', () async {
    final repo = _SeqRepo(['pending', 'pending', 'paid']);
    final ok = await waitPaid(repo, 'X', interval: Duration.zero);
    expect(ok, isTrue);
    expect(repo.i, 3);
  });

  test('waitPaid false bila gagal', () async {
    final repo = _SeqRepo(['pending', 'failed']);
    expect(await waitPaid(repo, 'X', interval: Duration.zero), isFalse);
  });

  test('waitPaid false bila waktu habis', () async {
    final repo = _SeqRepo(['pending']);
    final ok = await waitPaid(repo, 'X', interval: Duration.zero, maxTries: 5);
    expect(ok, isFalse);
    expect(repo.i, 5);
  });

  test('pembayaran tiruan mengubah order menjadi queued', () async {
    final orders = FakeOrderRepository(FakeCatalogRepository());
    final pay = FakePaymentRepository(orders);
    final o = await orders.create(
      packageId: 5,
      uid: '812345678',
      server: 'asia',
      username: 'a',
      password: 'b',
    );
    expect(await pay.status(o.orderNo), 'pending');
    await pay.createGateway(o.orderNo);
    expect(await pay.status(o.orderNo), 'paid');
    final after = await orders.detail(o.orderNo);
    expect(after.status, 'queued');
    expect(after.queuePosition, 1);
  });
}
