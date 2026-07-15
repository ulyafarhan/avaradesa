import 'package:flutter_test/flutter_test.dart';
import 'package:avaradesa_app/features/auth/domain/user_model.dart';

void main() {
  group('UserModel', () {
    test('fromJson parses warga response correctly', () {
      final json = {
        'data': {
          'user': {
            'nik': '1234567890123456',
            'nama_lengkap': 'Ulya Farhan',
          }
        },
        'token': '1|abc123',
      };
      final user = UserModel.fromJson(json);
      expect(user.nik, '1234567890123456');
      expect(user.namaLengkap, 'Ulya Farhan');
      expect(user.role, 'warga');
      expect(user.isWarga, true);
      expect(user.isAdmin, false);
    });

    test('fromJson parses admin response correctly', () {
      final json = {
        'data': {
          'user': {
            'id': 1,
            'username': 'admin',
            'role': 'kepala_desa',
            'nama_lengkap': 'Admin Desa',
          }
        },
        'token': '1|xyz789',
      };
      final user = UserModel.fromJson(json);
      expect(user.role, 'kepala_desa');
      expect(user.isAdmin, true);
      expect(user.isWarga, false);
    });

    test('fromJson handles missing data gracefully', () {
      final json = <String, dynamic>{};
      final user = UserModel.fromJson(json);
      expect(user.nik, isNull);
      expect(user.namaLengkap, isNull);
      expect(user.role, 'unknown');
    });

    test('toJson excludes token', () {
      const user = UserModel(nik: '12345', namaLengkap: 'Test', role: 'warga', token: 'secret');
      final json = user.toJson();
      expect(json.containsKey('token'), isFalse);
      expect(json['nik'], '12345');
      expect(json['role'], 'warga');
    });
  });
}
