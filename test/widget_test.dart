import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_test/flutter_test.dart';

import 'package:avaradesa_app/app.dart';

void main() {
  testWidgets('AvaraDesa app renders MaterialApp', (WidgetTester tester) async {
    await tester.pumpWidget(const ProviderScope(child: App()));

    // Verify app renders Material app
    expect(find.byType(MaterialApp), findsOneWidget);
  });

  testWidgets('App renders without crashing', (WidgetTester tester) async {
    await tester.pumpWidget(const ProviderScope(child: App()));
    await tester.pump(const Duration(seconds: 1));

    // Should navigate to splash or login screen eventually
    expect(find.byType(MaterialApp), findsOneWidget);
  });

  testWidgets('App has title AvaraDesa', (WidgetTester tester) async {
    await tester.pumpWidget(const ProviderScope(child: App()));

    // Verify title is set
    final materialApp = tester.widget<MaterialApp>(find.byType(MaterialApp));
    expect(materialApp.title, 'AvaraDesa');
  });

  testWidgets('App uses light theme with teal primary', (WidgetTester tester) async {
    await tester.pumpWidget(const ProviderScope(child: App()));

    final materialApp = tester.widget<MaterialApp>(find.byType(MaterialApp));
    expect(materialApp.theme?.colorScheme?.primary.value, 0xFF0D9488);
  });
}
