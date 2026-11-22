import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class AppColors {
  static const accent = Color(0xFF2F4B8F);
  static const text = Color(0xFF0F172A);
  static const textMuted = Color(0xFF64748B);
  static const background = Color(0xFFF8FAFC);
  static const border = Color(0xFFE2E8F0);
  static const accentSoft = Color(0xFFE8F0FC);
  static const danger = Color(0xFFDC2626);
  static const warning = Color(0xFFF59E0A);
}

// Dirakit manual karena GoogleFonts.interTextTheme() mengembalikan TextTheme
// yang tidak cocok dengan ThemeData di Flutter terbaru (flutter/flutter#191067).
TextTheme _interTextTheme() {
  final base = ThemeData.light().textTheme.apply(
    bodyColor: AppColors.text,
    displayColor: AppColors.text,
  );
  TextStyle? inter(TextStyle? s) => GoogleFonts.inter(textStyle: s);
  return TextTheme(
    displayLarge: inter(base.displayLarge),
    displayMedium: inter(base.displayMedium),
    displaySmall: inter(base.displaySmall),
    headlineLarge: inter(base.headlineLarge),
    headlineMedium: inter(base.headlineMedium),
    headlineSmall: inter(base.headlineSmall),
    titleLarge: inter(base.titleLarge),
    titleMedium: inter(base.titleMedium),
    titleSmall: inter(base.titleSmall),
    bodyLarge: inter(base.bodyLarge),
    bodyMedium: inter(base.bodyMedium),
    bodySmall: inter(base.bodySmall),
    labelLarge: inter(base.labelLarge),
    labelMedium: inter(base.labelMedium),
    labelSmall: inter(base.labelSmall),
  );
}

ThemeData buildTheme() {
  final border = OutlineInputBorder(
    borderRadius: BorderRadius.circular(12),
    borderSide: const BorderSide(color: AppColors.border),
  );
  return ThemeData(
    useMaterial3: true,
    colorScheme: ColorScheme.fromSeed(
      seedColor: AppColors.accent,
      primary: AppColors.accent,
      error: AppColors.danger,
    ),
    scaffoldBackgroundColor: AppColors.background,
    textTheme: _interTextTheme(),
    dividerColor: AppColors.border,
    filledButtonTheme: FilledButtonThemeData(
      style: FilledButton.styleFrom(
        minimumSize: const Size.fromHeight(48),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      ),
    ),
    inputDecorationTheme: InputDecorationTheme(
      filled: true,
      fillColor: Colors.white,
      border: border,
      enabledBorder: border,
    ),
  );
}