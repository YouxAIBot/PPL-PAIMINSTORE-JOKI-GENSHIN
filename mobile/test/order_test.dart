import 'package:flutter_test/flutter_test.dart';
import 'package:paiminstore_mobile/features/catalog/data/catalog_repository.dart';
import 'package:paiminstore_mobile/features/order/data/order_repository.dart';
import 'package:paiminstore_mobile/features/order/data/order_status.dart';

Future<Order> _buat(FakeOrderRepository repo, int pkg) => repo.create(
      packageId: pkg,
      uid: '812345678',
      server: 'asia',
      username: 'a',
      password: 'b',
    );

void main() {
  test('tab Menunggu berisi pending_payment dan queued', () {
    expect(matchesTab(1, 'pending_payment'), isTrue);
    expect(matchesTab(1, 'queued'), isTrue);
    expect(matchesTab(1, 'in_progress'), isFalse);
  });

  test('order dibatalkan hanya tampil di tab Semua', () {
    expect(matchesTab(0, 'cancelled'), isTrue);
    for (final t in [1, 2, 3]) {
      expect(matchesTab(t, 'cancelled'), isFalse);
    }
  });

  test('label dan langkah status', () {
    expect(labelOf('queued'), 'Menunggu Antrian');
    expect(stepOf('pending_payment'), 0);
    expect(stepOf('in_progress'), 2);
    expect(stepOf('completed'), 3);
  });

  test('parsing respons order dari API', () {
    final o = Order.fromJson({
      'order_no': 'PMS-2026-089',
      'status': 'pending_payment',
      'package': {'id': 5, 'name': 'Fontaine 100%'},
      'total_amount': 175000,
      'expires_at': '2026-11-23T05:00:00Z',
    });
    expect(o.orderNo, 'PMS-2026-089');
    expect(o.packageName, 'Fontaine 100%');
    expect(o.total, 175000);
    expect(o.expiresAt, isNotNull);
  });

  test('parsing respons antrian', () {
    final q = QueueInfo.fromJson({
      'status': 'queued',
      'position': 4,
      'orders_ahead': 3,
      'active_boosters': 2,
      'estimate_hours': 36,
    });
    expect(q.position, 4);
    expect(q.estimateHours, 36);
  });

  test('antrian tiruan: posisi dan estimasi', () async {
    final repo = FakeOrderRepository(FakeCatalogRepository());
    final a = await _buat(repo, 1);
    final b = await _buat(repo, 2);
    await repo.markPaid(a.orderNo);
    await repo.markPaid(b.orderNo);
    final qa = await repo.queue(a.orderNo);
    final qb = await repo.queue(b.orderNo);
    expect(qa.position, 1);
    expect(qa.estimateHours, 0);
    expect(qb.position, 2);
    expect(qb.ordersAhead, 1);
    expect(qb.estimateHours, 6);
  });
}
