class ApiEndpoints {
  ApiEndpoints._();

  static const String loginWarga = '/auth/login/warga';
  static const String loginAdmin = '/auth/login/admin';
  static const String logout = '/auth/logout';
  static const String profile = '/auth/profile';
  static const String bindTelegram = '/auth/bind-telegram';

  static const String suratKategori = '/surat/kategori';
  static String suratKategoriDetail(int id) => '/surat/kategori/$id';
  static const String suratPengajuan = '/surat/pengajuan';
  static String suratPengajuanDetail(int id) => '/surat/pengajuan/$id';

  static const String mutasi = '/mutasi';

  static const String informasi = '/informasi';
  static String informasiDetail(String slug) => '/informasi/$slug';

  static const String statistikDemografi = '/statistik/demografi';
  static const String statistikLayanan = '/statistik/layanan';

  static String verifikasi(String hash) => '/verifikasi/$hash';

  static const String syncPull = '/sync/pull';
  static const String syncPush = '/sync/push';

  static const String adminSurat = '/admin/surat/pengajuan';
  static String adminSuratApprove(int id) => '/admin/surat/pengajuan/$id/approve';
  static String adminSuratReject(int id) => '/admin/surat/pengajuan/$id/reject';

  static const String adminMutasi = '/admin/mutasi';
  static String adminMutasiApprove(int id) => '/admin/mutasi/$id/approve';
  static String adminMutasiReject(int id) => '/admin/mutasi/$id/reject';

  static const String adminInformasi = '/admin/informasi';
  static String adminInformasiDetail(int id) => '/admin/informasi/$id';
}
