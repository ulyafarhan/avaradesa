import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class AppTypography {
  AppTypography._();

  static TextTheme get textTheme {
    final base = GoogleFonts.instrumentSansTextTheme();
    return TextTheme(
      headlineLarge: base.headlineLarge?.copyWith(fontSize: 28, fontWeight: FontWeight.w700),
      headlineMedium: base.headlineMedium?.copyWith(fontSize: 24, fontWeight: FontWeight.w700),
      titleLarge: base.titleLarge?.copyWith(fontSize: 20, fontWeight: FontWeight.w600),
      titleMedium: base.titleMedium?.copyWith(fontSize: 16, fontWeight: FontWeight.w600),
      bodyLarge: base.bodyLarge?.copyWith(fontSize: 16, fontWeight: FontWeight.w400),
      bodyMedium: base.bodyMedium?.copyWith(fontSize: 14, fontWeight: FontWeight.w400),
      bodySmall: base.bodySmall?.copyWith(fontSize: 12, fontWeight: FontWeight.w400),
      labelLarge: base.labelLarge?.copyWith(fontSize: 14, fontWeight: FontWeight.w600),
      labelSmall: base.labelSmall?.copyWith(fontSize: 11, fontWeight: FontWeight.w600),
    );
  }
}
