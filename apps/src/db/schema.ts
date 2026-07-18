export const schema = {
  surat: 'id, user_id, kategori, jenis_surat, keperluan, keterangan, status, created_at',
  mutasi: 'id, user_id, jenis, alasan, alamat_tujuan, status, created_at',
  informasi: 'id, judul, kategori, published, created_at',
  penduduk: 'id, nik, nama, no_kk',
  sync_log: 'id, table, operation, record_id, synced_at',
}
