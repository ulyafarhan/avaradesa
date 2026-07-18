// ── API wrappers ──
export interface ApiResponse<T> {
  success: boolean
  data: T
  message?: string
  meta?: { page: number; per_page: number; total: number }
}

export interface SingleResponse<T> { message: string; data: T }
export interface PaginatedResponse<T> { current_page: number; data: T[]; total: number; per_page: number; last_page?: number; meta?: { total: number } }

export interface ApiError {
  message: string
  errors?: Record<string, string[]>
}

// ── Auth ──
export interface User {
  nik: string
  nama_lengkap: string
  no_kk: string
  jenis_kelamin: 'L' | 'P'
  tempat_lahir?: string
  tanggal_lahir?: string
  agama?: string
  pendidikan?: string
  pekerjaan?: string
  status_perkawinan?: string
  status_keluarga?: string
  status_mutasi?: string
  no_hp?: string
  telegram_chat_id?: string | null
  foto_profil?: string
  role: 'warga' | 'admin'
  keluarga?: AnggotaKeluarga[]
}

export interface AuthResponse {
  token: string
  user: User
}

export interface AnggotaKeluarga {
  nik: string
  nama_lengkap: string
  jenis_kelamin: 'L' | 'P'
  tempat_lahir?: string
  tanggal_lahir?: string
  status_keluarga?: string
}

// ── Penduduk (admin compat) ──
export interface Penduduk {
  id: number
  nik: string
  no_kk: string
  nama_lengkap: string
  jenis_kelamin: 'L' | 'P'
  tempat_lahir: string
  tanggal_lahir: string
  agama: string
  pendidikan: string
  pekerjaan: string
  status_perkawinan: string
  status_keluarga: string
  status_mutasi: string
  created_at?: string
}

export interface PendudukPemohon {
  nik: string
  no_kk?: string
  nama_lengkap: string
  jenis_kelamin?: 'L' | 'P'
  tempat_lahir?: string
  tanggal_lahir?: string
  agama?: string
  pendidikan?: string
  pekerjaan?: string
  alamat?: string
}

// ── Surat (admin compat — Laravel shape) ──
export interface SuratPengajuan {
  id: string
  nik_pemohon: string
  kategori_surat_id: number
  nomor_registrasi: string
  nomor_surat?: string
  status: string
  data_isian?: any
  file_syarat?: string[]
  catatan_penolakan?: string | null
  file_pdf_url?: string | null
  qr_hash?: string | null
  diverifikasi_oleh?: string | null
  created_at: string
  updated_at: string
  kategori?: KategoriSurat
  pemohon?: PendudukPemohon
  tracking: TrackingItem[]
  cetak_data?: any
}

// ── Mutasi (admin compat — Laravel shape) ──
export interface Mutasi {
  id: number
  nik: string
  jenis_mutasi: string
  tanggal_mutasi: string
  keterangan: string
  dokumen_bukti: string
  status_verifikasi: string
  diverifikasi_oleh?: string | null
  created_at: string
  penduduk?: PendudukPemohon
  verifikator?: any
}

// ── Informasi (admin compat — Laravel shape) ──
export interface Informasi {
  id: string
  judul: string
  konten: string
  isi?: string
  slug?: string
  kategori: string
  gambar?: string
  cover_image?: string
  published?: boolean
  is_published?: boolean
  author?: { id: number; nama_lengkap: string }
  created_at: string
}

// ── Laravel types (warga views) ──
export interface KategoriSurat {
  id: number
  kode_surat?: string
  nama_surat: string
  template_view?: string
  schema_isian: SchemaField[]
  syarat_dokumen: string[]
  is_active: boolean
  created_at?: string
  updated_at?: string
  deleted_at?: string | null
}

export interface SchemaField {
  field: string
  label: string
  type: 'text' | 'textarea' | 'number' | 'select'
  required?: boolean
  options?: string[]
}

export interface PengajuanSurat {
  id: string
  nomor_registrasi: string
  nomor_surat?: string
  nik_pemohon: string
  kategori_surat_id: number
  status: string
  data_isian: any
  file_syarat: string[]
  catatan_penolakan?: string
  file_pdf_url?: string | null
  qr_hash?: string | null
  surat_html?: string
  created_at: string
  updated_at: string
  kategori?: { id: number; nama_surat: string }
  pemohon?: PendudukPemohon
  tracking?: TrackingItem[]
}

export interface TrackingItem {
  status_sebelumnya?: string | null
  status_baru: string
  keterangan_update: string
  created_at: string
  diupdate_oleh?: string | null
}

export interface MutasiPenduduk {
  id: number
  nik: string
  jenis_mutasi: string
  tanggal_mutasi: string
  keterangan: string
  dokumen_bukti: string
  status_verifikasi: string
  created_at: string
  penduduk?: { nik: string; nama_lengkap: string }
}

export interface InformasiPublik {
  id: number
  judul: string
  slug: string
  konten: string
  kategori: string
  cover_image?: string
  is_published: boolean
  created_at: string
  author?: { id: number; nama_lengkap: string }
}
