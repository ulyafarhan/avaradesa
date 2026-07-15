import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:avaradesa_app/shared/widgets/skeleton_loader.dart';

void main() {
  group('SkeletonLoader', () {
    testWidgets('renders with given dimensions', (tester) async {
      await tester.pumpWidget(
        MaterialApp(home: Scaffold(
          body: SkeletonLoader(width: 100, height: 50),
        )),
      );
      expect(find.byType(SkeletonLoader), findsOneWidget);
    });
  });

  group('SkeletonList', () {
    testWidgets('renders 5 items by default', (tester) async {
      await tester.pumpWidget(
        MaterialApp(home: Scaffold(body: SkeletonList())),
      );
      expect(find.byType(SkeletonLoader), findsNWidgets(10));
    });

    testWidgets('renders custom count', (tester) async {
      await tester.pumpWidget(
        MaterialApp(home: Scaffold(body: SkeletonList(itemCount: 3))),
      );
      expect(find.byType(SkeletonLoader), findsNWidgets(6));
    });
  });
}
