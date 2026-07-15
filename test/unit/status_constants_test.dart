import 'package:flutter_test/flutter_test.dart';
import 'package:avaradesa_app/shared/constants/status_constants.dart';

void main() {
  group('SuratStatus', () {
    test('has correct values', () {
      expect(SuratStatus.menunggu, 'Menunggu');
      expect(SuratStatus.diproses, 'Diproses');
      expect(SuratStatus.disetujui, 'Disetujui');
      expect(SuratStatus.ditolak, 'Ditolak');
      expect(SuratStatus.selesai, 'Selesai');
    });

    test('all contains all statuses', () {
      expect(SuratStatus.all.length, 5);
      expect(SuratStatus.all, containsAll([
        SuratStatus.menunggu,
        SuratStatus.diproses,
        SuratStatus.disetujui,
        SuratStatus.ditolak,
        SuratStatus.selesai,
      ]));
    });
  });
}
