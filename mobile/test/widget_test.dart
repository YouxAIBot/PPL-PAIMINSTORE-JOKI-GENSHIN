import 'package:flutter_test/flutter_test.dart';
import 'package:paiminstore_mobile/core/format.dart';
import 'package:paiminstore_mobile/features/catalog/data/catalog_repository.dart';
import 'package:paiminstore_mobile/features/order/data/order_repository.dart';
import 'package:paiminstore_mobile/features/order/presentation/order_form_screen.dart';

void main() {
  test('rupiah memakai pemisah titik', () {
    expect(rupiah(175000), contains('175.000'));
  });

  test('estimasi jam dan hari', () {
    expect(estimasi(12), 'Est. 12 jam');
    expect(estimasi(60), 'Est. ±3 hari');
  });

  test('repository tiruan memuat paket dari kategori', () async {
    final repo = FakeCatalogRepository();
    final pkgs = await repo.packages('joki-explore-map');
    expect(pkgs.length, 8);
    expect(pkgs.first.name, 'Mondstadt 100%');
  });

  test('detail paket Fontaine memiliki harga 175000', () async {
    final p = await FakeCatalogRepository().package(5);
    expect(p.name, 'Fontaine 100%');
    expect(p.price, 175000);
  });

  test('order tiruan memakai nama dan harga paket', () async {
    final repo = FakeOrderRepository(FakeCatalogRepository());
    final o = await repo.create(
      packageId: 5,
      uid: '812345678',
      server: 'asia',
      username: 'akun@example.com',
      password: 'rahasia123',
    );
    expect(o.status, 'pending_payment');
    expect(o.packageName, 'Fontaine 100%');
    expect(o.total, 175000);
    expect(o.orderNo, startsWith('PMS-'));
    expect((await repo.list()).length, 1);
  });

  test('validasi UID harus 9 atau 10 digit', () {
    expect(validateUid('812345678'), isNull);
    expect(validateUid('1234'), isNotNull);
    expect(validateUid('abc123456'), isNotNull);
    expect(validateUid(null), isNotNull);
  });
}
