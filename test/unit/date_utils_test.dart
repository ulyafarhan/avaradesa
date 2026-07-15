import 'package:flutter_test/flutter_test.dart';
import 'package:avaradesa_app/shared/utils/date_utils.dart';

void main() {
  group('formatDate', () {
    test('formats valid date string', () {
      expect(formatDate('2026-07-13T10:00:00Z'), '13/07/2026');
    });

    test('returns dash for null', () {
      expect(formatDate(null), '-');
    });

    test('returns dash for empty string', () {
      expect(formatDate(''), '-');
    });

    test('returns original for invalid format', () {
      expect(formatDate('invalid'), 'invalid');
    });
  });

  group('formatDateTime', () {
    test('formats valid datetime string', () {
      expect(formatDateTime('2026-07-13T10:30:00Z'), '13/07/2026 10:30');
    });

    test('returns dash for null', () {
      expect(formatDateTime(null), '-');
    });
  });
}
