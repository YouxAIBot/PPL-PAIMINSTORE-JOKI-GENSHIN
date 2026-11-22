import 'package:dio/dio.dart';
import '../../../core/network/api_error.dart';
import '../../../core/network/dio_provider.dart';
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

class ApiAuthRepository implements AuthRepository {
  final Dio dio;
  final TokenStorage _storage;

  ApiAuthRepository(this.dio, [TokenStorage? storage])
      : _storage = storage ?? TokenStorage();

  @override
  Future<void> login(String email, String password) => guard(() async {
        final r = await dio.post('/auth/login',
            data: {'email': email, 'password': password});
        await _storage.save(r.data['data']['token']);
      });

  @override
  Future<void> register(String name, String email, String password) =>
      guard(() async {
        await dio.post('/auth/register', data: {
          'name': name,
          'email': email,
          'password': password,
          'password_confirmation': password,
        });
      });

  @override
  Future<bool> hasSession() async => (await _storage.read()) != null;

  @override
  Future<void> logout() async {
    try {
      await dio.post('/auth/logout');
    } catch (_) {
    } finally {
      await _storage.clear();
    }
  }
}

final authRepositoryProvider = Provider<AuthRepository>((ref) {
  const useFake = bool.fromEnvironment('USE_FAKE', defaultValue: true);
  return useFake ? FakeAuthRepository() : ApiAuthRepository(ref.read(dioProvider));
});