import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class TokenStorage {
  static const _key = 'token';
  final _s = const FlutterSecureStorage();
  Future<String?> read() => _s.read(key: _key);
  Future<void> save(String t) => _s.write(key: _key, value: t);
  Future<void> clear() => _s.delete(key: _key);
}