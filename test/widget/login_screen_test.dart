import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:avaradesa_app/features/auth/presentation/login_screen.dart';
import 'package:avaradesa_app/features/auth/presentation/provider/auth_provider.dart';

class _MockAuthNotifier extends StateNotifier<AuthState> {
  _MockAuthNotifier() : super(const AuthState(isLoading: false));
}

final _mockAuthProvider = StateNotifierProvider<_MockAuthNotifier, AuthState>((_) => _MockAuthNotifier());

Widget _buildTestApp() {
  return ProviderScope(
    overrides: [authProvider.overrideWithProvider(_mockAuthProvider)],
    child: const MaterialApp(home: LoginScreen()),
  );
}

void main() {
  group('LoginScreen', () {
    testWidgets('renders login form for warga', (tester) async {
      await tester.pumpWidget(_buildTestApp());
      await tester.pump();
      expect(find.text('AvaraDesa'), findsOneWidget);
      expect(find.text('Sistem Informasi Desa'), findsOneWidget);
      expect(find.text('Masuk'), findsOneWidget);
    });

    testWidgets('toggles between warga and admin', (tester) async {
      await tester.pumpWidget(_buildTestApp());
      await tester.pump();
      expect(find.text('NIK'), findsOneWidget);
      expect(find.text('No. KK'), findsOneWidget);
      await tester.tap(find.text('Admin'));
      await tester.pump();
      expect(find.text('Username'), findsOneWidget);
      expect(find.text('Password'), findsOneWidget);
    });
  });
}
