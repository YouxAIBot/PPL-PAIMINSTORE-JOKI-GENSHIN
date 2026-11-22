import 'package:dio/dio.dart';
import '../../../core/network/api_error.dart';
import '../../../core/network/dio_provider.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

class Profile {
  final String name;
  final String email;
  final bool emailVerified;

  const Profile({
    required this.name,
    required this.email,
    required this.emailVerified,
  });

  factory Profile.fromJson(Map<String, dynamic> j) => Profile(
        name: j['name'],
        email: j['email'],
        emailVerified: j['email_verified'] ?? false,
      );
}

abstract class ProfileRepository {
  Future<Profile> me();
  Future<Profile> update({required String name, required String email});
  Future<void> changePassword({
    required String current,
    required String password,
  });
}

class FakeProfileRepository implements ProfileRepository {
  Profile _profile = const Profile(
    name: 'Pengguna Contoh',
    email: 'user@paiminstore.test',
    emailVerified: true,
  );

  @override
  Future<Profile> me() async {
    await Future.delayed(const Duration(milliseconds: 300));
    return _profile;
  }

  @override
  Future<Profile> update({required String name, required String email}) async {
    await Future.delayed(const Duration(milliseconds: 400));
    final changed = email.trim() != _profile.email;
    _profile = Profile(
      name: name.trim(),
      email: email.trim(),
      emailVerified: changed ? false : _profile.emailVerified,
    );
    return _profile;
  }

  @override
  Future<void> changePassword({
    required String current,
    required String password,
  }) async {
    await Future.delayed(const Duration(milliseconds: 400));
    if (current.length < 8) throw Exception('Password lama salah');
  }
}

class ApiProfileRepository implements ProfileRepository {
  final Dio dio;
  ApiProfileRepository(this.dio);

  @override
  Future<Profile> me() => guard(() async {
        final r = await dio.get('/me');
        return Profile.fromJson(r.data['data']);
      });

  @override
  Future<Profile> update({required String name, required String email}) =>
      guard(() async {
        final r = await dio.patch('/me', data: {'name': name, 'email': email});
        return Profile.fromJson(r.data['data']);
      });

  @override
  Future<void> changePassword({
    required String current,
    required String password,
  }) =>
      guard(() async {
        await dio.put('/me/password', data: {
          'current_password': current,
          'password': password,
          'password_confirmation': password,
        });
      });
}

final profileRepositoryProvider = Provider<ProfileRepository>((ref) {
  const useFake = bool.fromEnvironment('USE_FAKE', defaultValue: true);
  return useFake ? FakeProfileRepository() : ApiProfileRepository(ref.read(dioProvider));
});

final profileProvider = FutureProvider.autoDispose<Profile>((ref) {
  return ref.watch(profileRepositoryProvider).me();
});
