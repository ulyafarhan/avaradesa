import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/storage/secure_storage.dart';

final authLocalProvider = Provider<AuthLocal>((ref) {
  return AuthLocal(ref.read(secureStorageProvider));
});

class AuthLocal {
  final SecureStorageService _storage;

  AuthLocal(this._storage);

  Future<void> saveSession(String token, Map<String, dynamic> user, String role) async {
    await _storage.write(key: 'auth_token', value: token);
    await _storage.write(key: 'user_data', value: user.toString());
    await _storage.write(key: 'user_role', value: role);
  }

  Future<String?> getToken() => _storage.read(key: 'auth_token');

  Future<String?> getRole() => _storage.read(key: 'user_role');

  Future<void> clearSession() async {
    await _storage.delete(key: 'auth_token');
    await _storage.delete(key: 'user_data');
    await _storage.delete(key: 'user_role');
  }

  Future<bool> hasSession() async {
    final token = await _storage.read(key: 'auth_token');
    return token != null;
  }
}
