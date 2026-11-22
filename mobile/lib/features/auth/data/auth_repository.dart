import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/storage/token_storage.dart';

abstract class AuthRepository {
  Future<void> login(String email, String password);
  Future<void> register(String name, String email, String password);
  Future<bool> hasSession();
  Future<void> logout();
}

class FakeAuthRepository implements AuthRepository {
  final _storage = TokenStorage();

  @override
  Future<void> login(String email, String password) async {
    await Future.delayed(const Duration(milliseconds: 500));
    if (password.length < 8) throw Exception('Email atau password salah');
    await _storage.save('fake-token');
  }

  @override
  Future<void> register(String name, String email, String password) async {
    await Future.delayed(const Duration(milliseconds: 500));
  }

  @override
  Future<bool> hasSession() async => (await _storage.read()) != null;

  @override
  Future<void> logout() => _storage.clear();
}

final authRepositoryProvider = Provider<AuthRepository>((ref) {
  return FakeAuthRepository();
});