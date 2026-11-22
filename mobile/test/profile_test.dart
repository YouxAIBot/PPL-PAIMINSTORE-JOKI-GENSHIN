import 'package:flutter_test/flutter_test.dart';
import 'package:paiminstore_mobile/core/validators.dart';
import 'package:paiminstore_mobile/features/profile/data/profile_repository.dart';

void main() {
  test('validasi email', () {
    expect(validateEmail('a@b.com'), isNull);
    expect(validateEmail('a@b'), isNotNull);
    expect(validateEmail('tanpa-at'), isNotNull);
    expect(validateEmail(null), isNotNull);
  });

  test('validasi password minimal 8 karakter', () {
    expect(validatePassword('12345678'), isNull);
    expect(validatePassword('1234567'), isNotNull);
  });

  test('validasi wajib diisi', () {
    expect(validateRequired('  '), isNotNull);
    expect(validateRequired('x'), isNull);
  });

  test('parsing respons profil dari API', () {
    final p = Profile.fromJson({
      'name': 'Budi',
      'email': 'budi@x.com',
      'email_verified': true,
    });
    expect(p.name, 'Budi');
    expect(p.emailVerified, isTrue);
  });

  test('ganti email membuat email belum terverifikasi', () async {
    final repo = FakeProfileRepository();
    final awal = await repo.me();
    expect(awal.emailVerified, isTrue);
    final baru = await repo.update(name: 'Budi', email: 'baru@x.com');
    expect(baru.emailVerified, isFalse);
    final sama = await repo.update(name: 'Budi 2', email: 'baru@x.com');
    expect(sama.name, 'Budi 2');
  });

  test('ganti password dengan password lama pendek ditolak', () async {
    final repo = FakeProfileRepository();
    expect(
      () => repo.changePassword(current: 'pendek', password: 'passwordbaru1'),
      throwsException,
    );
    await repo.changePassword(current: 'password123', password: 'passwordbaru1');
  });
}
