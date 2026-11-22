import 'package:flutter_test/flutter_test.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:paiminstore_mobile/features/auth/data/auth_repository.dart';

void main() {
  TestWidgetsFlutterBinding.ensureInitialized();

  setUp(() {
    FlutterSecureStorage.setMockInitialValues({});
  });

  test('login dengan password pendek ditolak', () async {
    final repo = FakeAuthRepository();
    expect(() => repo.login('a@b.com', 'pendek'), throwsException);
  });

  test('login berhasil menyimpan sesi, logout menghapusnya', () async {
    final repo = FakeAuthRepository();
    await repo.login('a@b.com', 'password123');
    expect(await repo.hasSession(), isTrue);
    await repo.logout();
    expect(await repo.hasSession(), isFalse);
  });
}