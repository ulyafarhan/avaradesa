import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:avaradesa_app/shared/widgets/error_state.dart';

void main() {
  group('ErrorState', () {
    testWidgets('renders with retry button', (tester) async {
      await tester.pumpWidget(
        MaterialApp(home: Scaffold(
          body: ErrorState(
            message: 'Terjadi kesalahan',
            onRetry: () {},
          ),
        )),
      );
      expect(find.text('Gagal Memuat'), findsOneWidget);
      expect(find.text('Terjadi kesalahan'), findsOneWidget);
      expect(find.text('Coba Lagi'), findsOneWidget);
    });

    testWidgets('renders without retry', (tester) async {
      await tester.pumpWidget(
        MaterialApp(home: Scaffold(
          body: ErrorState(message: 'Error saja'),
        )),
      );
      expect(find.text('Error saja'), findsOneWidget);
      expect(find.text('Coba Lagi'), findsNothing);
    });
  });
}
