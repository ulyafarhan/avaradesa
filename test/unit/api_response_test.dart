import 'package:flutter_test/flutter_test.dart';
import 'package:avaradesa_app/core/api/api_response.dart';

void main() {
  group('ApiResponse', () {
    test('fromJson parses success response', () {
      final json = {
        'message': 'Berhasil',
        'data': {'id': 1},
      };
      final response = ApiResponse.fromJson(json, (data) => data as Map<String, dynamic>);
      expect(response.success, true);
      expect(response.message, 'Berhasil');
      expect(response.data, isNotNull);
    });

    test('fromJson parses error response', () {
      final json = {
        'message': 'Error',
        'errors': {
          'nik': ['NIK tidak valid'],
        },
      };
      final response = ApiResponse.fromJson(json, null);
      expect(response.success, true);
      expect(response.errors, isNotNull);
      expect(response.errors!['nik'], ['NIK tidak valid']);
    });
  });

  group('PaginatedResponse', () {
    test('fromJson parses paginated data', () {
      final json = {
        'data': [
          {'id': 1},
          {'id': 2},
        ],
        'current_page': 1,
        'total': 2,
        'per_page': 10,
        'last_page': 1,
      };
      final response = PaginatedResponse.fromJson(json, (item) => item as Map<String, dynamic>);
      expect(response.data.length, 2);
      expect(response.currentPage, 1);
      expect(response.total, 2);
      expect(response.perPage, 10);
    });
  });
}
