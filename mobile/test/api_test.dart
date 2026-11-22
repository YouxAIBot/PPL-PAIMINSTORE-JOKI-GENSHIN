import 'dart:typed_data';
import 'package:dio/dio.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:paiminstore_mobile/core/network/api_error.dart';
import 'package:paiminstore_mobile/features/catalog/data/catalog_repository.dart';
import 'package:paiminstore_mobile/features/order/data/order_repository.dart';
import 'package:paiminstore_mobile/features/payment/data/payment_repository.dart';

class _Adapter implements HttpClientAdapter {
  final int status;
  final String body;
  RequestOptions? last;

  _Adapter(this.status, this.body);

  @override
  Future<ResponseBody> fetch(
    RequestOptions options,
    Stream<Uint8List>? requestStream,
    Future<void>? cancelFuture,
  ) async {
    last = options;
    return ResponseBody.fromString(body, status, headers: {
      Headers.contentTypeHeader: [Headers.jsonContentType],
    });
  }

  @override
  void close({bool force = false}) {}
}

Dio _dio(_Adapter a) =>
    Dio(BaseOptions(baseUrl: 'http://test/api/v1'))..httpClientAdapter = a;

void main() {
  test('katalog API membaca daftar kategori', () async {
    final a = _Adapter(200, '{"data":[{"id":1,"name":"Joki Explore Map","slug":"joki-explore-map"}]}');
    final repo = ApiCatalogRepository(_dio(a));
    final cats = await repo.categories();
    expect(cats.single.slug, 'joki-explore-map');
    expect(a.last!.path, '/categories');
  });

  test('katalog API membaca paket dengan harga bulat', () async {
    final a = _Adapter(200, '{"data":[{"id":5,"name":"Fontaine 100%","tier":null,"description":null,"price":175000,"estimated_hours":60}]}');
    final repo = ApiCatalogRepository(_dio(a));
    final pkgs = await repo.packages('joki-explore-map');
    expect(pkgs.single.price, 175000);
    expect(a.last!.path, '/categories/joki-explore-map/packages');
  });

  test('buat order mengirim field sesuai kontrak', () async {
    final a = _Adapter(201, '{"data":{"order_no":"PMS-2026-001","status":"pending_payment","package":{"id":5,"name":"Fontaine 100%"},"total_amount":175000,"expires_at":"2026-11-23T05:00:00Z"}}');
    final repo = ApiOrderRepository(_dio(a));
    final o = await repo.create(
      packageId: 5,
      uid: '812345678',
      server: 'asia',
      username: 'akun@example.com',
      password: 'rahasia123',
      notes: 'tes',
    );
    expect(o.orderNo, 'PMS-2026-001');
    expect(a.last!.method, 'POST');
    expect(a.last!.path, '/orders');
    final fields = {for (final e in (a.last!.data as FormData).fields) e.key: e.value};
    expect(fields['package_id'], '5');
    expect(fields['game_username'], 'akun@example.com');
    expect(fields['accept_disclaimer'], '1');
  });

  test('daftar order meneruskan filter status', () async {
    final a = _Adapter(200, '{"data":[]}');
    final repo = ApiOrderRepository(_dio(a));
    await repo.list(status: 'pending_payment,queued');
    expect(a.last!.queryParameters['status'], 'pending_payment,queued');
  });

  test('status pembayaran dan link gateway', () async {
    final a1 = _Adapter(201, '{"data":{"redirect_url":"https://snap/x","snap_token":"t"}}');
    final link = await ApiPaymentRepository(_dio(a1)).createGateway('PMS-2026-001');
    expect(link.redirectUrl, 'https://snap/x');
    expect(a1.last!.path, '/orders/PMS-2026-001/payments/gateway');

    final a2 = _Adapter(200, '{"data":{"status":"paid","order_status":"queued"}}');
    expect(await ApiPaymentRepository(_dio(a2)).status('PMS-2026-001'), 'paid');
  });

  test('galat 422 diubah menjadi pesan pertama', () async {
    final a = _Adapter(422, '{"message":"The given data was invalid.","errors":{"uid":["UID harus 9 atau 10 digit"]}}');
    final repo = ApiOrderRepository(_dio(a));
    expect(
      () => repo.detail('X'),
      throwsA(predicate((e) => e.toString().contains('UID harus 9 atau 10 digit'))),
    );
  });

  test('galat tanpa errors memakai message', () {
    final e = DioException(
      requestOptions: RequestOptions(path: '/x'),
      response: Response(
        requestOptions: RequestOptions(path: '/x'),
        statusCode: 403,
        data: {'message': 'Akun Anda dinonaktifkan.'},
      ),
    );
    expect(apiErrorMessage(e), 'Akun Anda dinonaktifkan.');
  });
}
