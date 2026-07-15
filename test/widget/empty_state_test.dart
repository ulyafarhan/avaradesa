import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:avaradesa_app/shared/widgets/empty_state.dart';

void main() {
  group('EmptyState', () {
    testWidgets('renders with all props', (tester) async {
      await tester.pumpWidget(
        MaterialApp(home: Scaffold(
          body: EmptyState(
            icon: Icons.description_outlined,
            title: 'Belum ada data',
            subtitle: 'Coba buat data baru',
            actionLabel: 'Tambah',
            onAction: () {},
          ),
        )),
      );
      expect(find.text('Belum ada data'), findsOneWidget);
      expect(find.text('Coba buat data baru'), findsOneWidget);
      expect(find.text('Tambah'), findsOneWidget);
    });

    testWidgets('renders without action button', (tester) async {
      await tester.pumpWidget(
        MaterialApp(home: Scaffold(
          body: EmptyState(
            icon: Icons.info,
            title: 'Info',
            subtitle: 'Tidak ada aksi',
          ),
        )),
      );
      expect(find.text('Info'), findsOneWidget);
      expect(find.text('Tidak ada aksi'), findsOneWidget);
      expect(find.byType(FilledButton), findsNothing);
    });
  });
}
