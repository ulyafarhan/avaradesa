import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:avaradesa_app/shared/widgets/status_badge.dart';

void main() {
  group('SuratStatusBadge', () {
    testWidgets('renders menunggu status', (tester) async {
      await tester.pumpWidget(
        MaterialApp(home: Scaffold(body: SuratStatusBadge('Menunggu'))),
      );
      expect(find.text('Menunggu'), findsOneWidget);
    });

    testWidgets('renders disetujui status', (tester) async {
      await tester.pumpWidget(
        MaterialApp(home: Scaffold(body: SuratStatusBadge('Disetujui'))),
      );
      expect(find.text('Disetujui'), findsOneWidget);
    });

    testWidgets('renders pending fallback', (tester) async {
      await tester.pumpWidget(
        MaterialApp(home: Scaffold(body: SuratStatusBadge('Pending'))),
      );
      expect(find.text('Menunggu'), findsOneWidget);
    });
  });

  group('SuratStatusIcon', () {
    testWidgets('renders icon for diproses', (tester) async {
      await tester.pumpWidget(
        MaterialApp(home: Scaffold(body: SuratStatusIcon('Diproses'))),
      );
      expect(find.byType(CircleAvatar), findsOneWidget);
    });
  });
}
