import { createRouter, createWebHashHistory } from 'vue-router'
import { useAuthStore } from '../stores/authStore'

const routes = [
  {
    path: '/auth',
    children: [
      { path: 'login-warga', name: 'LoginWarga', component: () => import('../views/auth/LoginWarga.vue') },
      { path: 'login-admin', name: 'LoginAdmin', component: () => import('../views/auth/LoginAdmin.vue') },
      { path: 'pin-setup', name: 'PinSetup', component: () => import('../views/auth/PinSetup.vue') },
    ],
  },
  {
    path: '/warga',
    component: () => import('../layouts/WargaLayout.vue'),
    meta: { role: 'warga' },
    children: [
      { path: '', redirect: { name: 'WargaDashboard' } },
      { path: 'dashboard', name: 'WargaDashboard', component: () => import('../views/warga/Dashboard.vue') },
      { path: 'home', name: 'WargaHome', component: () => import('../views/warga/Home.vue') },
      { path: 'surat', name: 'SuratKategori', component: () => import('../views/warga/surat/KategoriList.vue') },
      { path: 'surat/buat/:kategori', name: 'BuatSurat', component: () => import('../views/warga/surat/BuatSurat.vue') },
      { path: 'surat/pengajuan', name: 'PengajuanList', component: () => import('../views/warga/surat/PengajuanList.vue') },
      { path: 'surat/pengajuan/:id', name: 'PengajuanDetail', component: () => import('../views/warga/surat/PengajuanDetail.vue') },
      { path: 'mutasi', name: 'MutasiList', component: () => import('../views/warga/mutasi/MutasiList.vue') },
      { path: 'mutasi/buat', name: 'BuatMutasi', component: () => import('../views/warga/mutasi/BuatMutasi.vue') },
      { path: 'informasi', name: 'InformasiList', component: () => import('../views/warga/informasi/InformasiList.vue') },
      { path: 'informasi/:id', name: 'InformasiDetail', component: () => import('../views/warga/informasi/InformasiDetail.vue') },
      { path: 'profil', name: 'ProfilView', component: () => import('../views/warga/profil/ProfilView.vue') },
      { path: 'keluarga', name: 'KeluargaView', component: () => import('../views/warga/profil/KeluargaView.vue') },
      { path: 'statistik', name: 'StatistikView', component: () => import('../views/warga/statistik/StatistikView.vue') },
    ],
  },
  {
    path: '/admin',
    component: () => import('../layouts/AdminLayout.vue'),
    meta: { role: 'admin' },
    children: [
      { path: '', redirect: { name: 'AdminDashboard' } },
      { path: 'dashboard', name: 'AdminDashboard', component: () => import('../views/admin/AdminDashboard.vue') },
      { path: 'penduduk', name: 'PendudukList', component: () => import('../views/admin/penduduk/PendudukList.vue') },
      { path: 'penduduk/tambah', name: 'PendudukTambah', component: () => import('../views/admin/penduduk/PendudukForm.vue') },
      { path: 'penduduk/:id/edit', name: 'PendudukEdit', component: () => import('../views/admin/penduduk/PendudukForm.vue') },
      { path: 'surat', name: 'SuratManage', component: () => import('../views/admin/surat/SuratManage.vue') },
      { path: 'surat/:id', name: 'SuratDetail', component: () => import('../views/admin/surat/SuratDetail.vue') },
      { path: 'mutasi', name: 'MutasiManage', component: () => import('../views/admin/mutasi/MutasiManage.vue') },
      { path: 'informasi', name: 'AdminInformasiList', component: () => import('../views/admin/informasi/InformasiList.vue') },
      { path: 'informasi/tambah', name: 'InformasiTambah', component: () => import('../views/admin/informasi/InformasiForm.vue') },
      { path: 'informasi/:id/edit', name: 'InformasiEdit', component: () => import('../views/admin/informasi/InformasiForm.vue') },
      { path: 'statistik', name: 'AdminStatistik', component: () => import('../views/admin/statistik/AdminStatistik.vue') },
      { path: 'keluarga', name: 'KeluargaManage', component: () => import('../views/admin/keluarga/KeluargaManage.vue') },
      { path: 'kategori-surat', name: 'KategoriSuratManage', component: () => import('../views/admin/surat/KategoriSuratManage.vue') },
      { path: 'fasilitas', name: 'FasilitasManage', component: () => import('../views/admin/fasilitas/FasilitasManage.vue') },
      { path: 'audit-log', name: 'AuditLogView', component: () => import('../views/admin/audit/AuditLogView.vue') },
    ],
  },
  { path: '/:pathMatch(.*)*', redirect: '/auth/login-warga' },
]

const router = createRouter({
  history: createWebHashHistory(),
  routes,
})

router.beforeEach((to, _from) => {
  const auth = useAuthStore()
  const requiredRole = to.meta.role as string | undefined

  if (requiredRole && !auth.isLoggedIn) {
    return requiredRole === 'admin' ? '/auth/login-admin' : '/auth/login-warga'
  }

  if (requiredRole && auth.role !== requiredRole) {
    return auth.isAdmin ? '/admin/dashboard' : '/warga/dashboard'
  }

  return true
})

export default router
