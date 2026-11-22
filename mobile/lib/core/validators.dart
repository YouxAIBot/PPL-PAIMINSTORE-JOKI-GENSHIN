String? validateRequired(String? v) =>
    (v == null || v.trim().isEmpty) ? 'Wajib diisi' : null;

String? validateEmail(String? v) =>
    (v == null || !RegExp(r'^[^@\s]+@[^@\s]+\.[^@\s]+$').hasMatch(v.trim()))
        ? 'Email tidak valid'
        : null;

String? validatePassword(String? v) =>
    (v == null || v.length < 8) ? 'Minimal 8 karakter' : null;
