const BASE = '/api/v1'

export const endpoints = {
  auth: {
    loginWarga:        `${BASE}/auth/login/warga`,
    loginAdmin:        `${BASE}/auth/login/admin`,
    registerPin:       `${BASE}/auth/register-pin`,
    loginPin:          `${BASE}/auth/login-pin`,
    resetPin:          `${BASE}/auth/reset-pin`,
    loginBiometric:    `${BASE}/auth/login-biometric`,
    registerBiometric: `${BASE}/auth/register-biometric`,
    me:                `${BASE}/auth/profile`,
  },
  statistik: {
    demografi: `${BASE}/statistik/demografi`,
    layanan: `${BASE}/statistik/layanan`,
    clearCache: `${BASE}/admin/statistik/clear-cache`,
  },
  penduduk: {
    list: `${BASE}/admin/penduduk`,
    detail: (nik: string) => `${BASE}/admin/penduduk/${nik}`,
    create: `${BASE}/admin/penduduk`,
    update: (nik: string) => `${BASE}/admin/penduduk/${nik}`,
    delete: (nik: string) => `${BASE}/admin/penduduk/${nik}`,
  },
  surat: {
    kategori: `${BASE}/surat/kategori`,
    create: `${BASE}/surat/pengajuan`,
    list: `${BASE}/surat/pengajuan`,
    pengajuan: `${BASE}/admin/surat/pengajuan`,
    detail: (id: string) => `${BASE}/surat/pengajuan/${id}`,
    approve: (id: string) => `${BASE}/admin/surat/pengajuan/${id}/approve`,
    reject: (id: string) => `${BASE}/admin/surat/pengajuan/${id}/reject`,
  },
  mutasi: {
    list: `${BASE}/admin/mutasi`,
    detail: (id: string) => `${BASE}/mutasi/${id}`,
    create: `${BASE}/mutasi`,
    approve: (id: string) => `${BASE}/admin/mutasi/${id}/approve`,
    reject: (id: string) => `${BASE}/admin/mutasi/${id}/reject`,
  },
  informasi: {
    list: `${BASE}/admin/informasi`,
    detail: (id: string) => `${BASE}/informasi/${id}`,
    create: `${BASE}/admin/informasi`,
    update: (id: string) => `${BASE}/admin/informasi/${id}`,
    delete: (id: string) => `${BASE}/admin/informasi/${id}`,
  },
  dashboard: {
    warga: `${BASE}/dashboard`,
  },
  keluarga: {
    list:   `${BASE}/admin/keluarga`,
    create: `${BASE}/admin/keluarga`,
    delete: (no_kk: string) => `${BASE}/admin/keluarga/${no_kk}`,
  },
  kategoriSurat: {
    list:   `${BASE}/admin/kategori-surat`,
    create: `${BASE}/admin/kategori-surat`,
    update: (id: string) => `${BASE}/admin/kategori-surat/${id}`,
    delete: (id: string) => `${BASE}/admin/kategori-surat/${id}`,
  },
  fasilitas: {
    list:   `${BASE}/admin/fasilitas`,
    create: `${BASE}/admin/fasilitas`,
    delete: (id: string) => `${BASE}/admin/fasilitas/${id}`,
  },
  auditLog: {
    list:   `${BASE}/admin/audit-log`,
  },
  sync: {
    push: `${BASE}/sync/push`,
    pull: `${BASE}/sync/pull?since=`,
  },
}
