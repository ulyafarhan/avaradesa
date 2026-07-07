class UserModel {
  final String? nik;
  final String? namaLengkap;
  final String role;
  final String? token;

  const UserModel({
    this.nik,
    this.namaLengkap,
    required this.role,
    this.token,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    final data = json['data'] as Map<String, dynamic>? ?? json;
    final user = data['user'] as Map<String, dynamic>? ?? data;
    return UserModel(
      nik: user['nik'] as String?,
      namaLengkap: (user['nama_lengkap'] ?? user['nama']) as String?,
      role: _parseRole(json),
      token: json['token'] as String?,
    );
  }

  static String _parseRole(Map<String, dynamic> json) {
    final data = json['data'] as Map<String, dynamic>? ?? json;
    final user = data['user'] as Map<String, dynamic>? ?? data;
    if (user['role'] != null) return user['role'] as String;
    if (user['nik'] != null) return 'warga';
    return 'unknown';
  }

  Map<String, dynamic> toJson() => {
    'nik': nik,
    'nama_lengkap': namaLengkap,
    'role': role,
    'token': token,
  };

  bool get isAdmin => role == 'kepala_desa' || role == 'sekdes' || role == 'operator';
  bool get isWarga => role == 'warga';
}
