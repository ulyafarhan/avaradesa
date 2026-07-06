import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/api/api_client.dart';
import '../../../core/storage/secure_storage.dart';
import '../../../shared/constants/api_constants.dart';

final authRepositoryProvider = Provider<AuthRepository>((ref) {
  return AuthRepository(ref.read(dioProvider), ref.read(secureStorageProvider));
});

class AuthRepository {
  final Dio _dio;
  final SecureStorageService _storage;

  AuthRepository(this._dio, this._storage);

  Future<Map<String, dynamic>> loginWarga(String nik, String noKk) async {
    final response = await _dio.post('${ApiConstants.baseUrl}/auth/login/warga', data: {
      'nik': nik,
      'no_kk': noKk,
    });
    final body = response.data as Map<String, dynamic>;
    final token = body['token'] as String;
    await _storage.write(key: 'auth_token', value: token);
    return body;
  }

  Future<Map<String, dynamic>> loginAdmin(String username, String password) async {
    final response = await _dio.post('${ApiConstants.baseUrl}/auth/login/admin', data: {
      'username': username,
      'password': password,
    });
    final body = response.data as Map<String, dynamic>;
    final token = body['token'] as String;
    await _storage.write(key: 'auth_token', value: token);
    return body;
  }

  Future<void> logout() async {
    try {
      await _dio.post('${ApiConstants.baseUrl}/auth/logout');
    } catch (_) {}
    await _storage.delete(key: 'auth_token');
    await _storage.delete(key: 'user_data');
    await _storage.delete(key: 'user_role');
  }

  Future<Map<String, dynamic>?> getProfile() async {
    try {
      final response = await _dio.get('${ApiConstants.baseUrl}/auth/profile');
      return response.data as Map<String, dynamic>;
    } catch (_) {
      return null;
    }
  }
}
