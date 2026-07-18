<script setup lang="ts">
const features = [
  { icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', label: 'Ajukan Surat', desc: 'Surat keterangan domisili, usaha, dll.', to: '/warga/surat', iconBg: 'var(--clr-primary-bg)', iconClr: 'var(--clr-primary)' },
  { icon: 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', label: 'Mutasi Penduduk', desc: 'Pindah masuk atau keluar desa', to: '/warga/mutasi', iconBg: 'var(--clr-secondary-bg)', iconClr: 'var(--clr-secondary)' },
  { icon: 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z', label: 'Informasi Desa', desc: 'Berita dan pengumuman resmi', to: '/warga/informasi', iconBg: 'var(--clr-tertiary-bg)', iconClr: 'var(--clr-tertiary)' },
  { icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', label: 'Profil Saya', desc: 'Data pribadi dan kartu keluarga', to: '/warga/profil', iconBg: 'var(--clr-warning-bg)', iconClr: 'var(--clr-warning)' },
  { icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', label: 'Statistik', desc: 'Agregat data kependudukan', to: '/warga/statistik', iconBg: 'var(--clr-surface-dim)', iconClr: 'var(--clr-text)' },
]
</script>

<template>
  <div class="space-y-4 max-w-screen-sm mx-auto">
    <div class="text-center py-4">
      <div
        class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-3"
        style="background: var(--clr-primary-bg);"
      >
        <svg class="w-8 h-8" style="color: var(--clr-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
      </div>
      <h1 class="text-xl font-bold" style="color: var(--clr-text);">AvaraDesa</h1>
      <p class="text-xs mt-1 font-medium" style="color: var(--clr-text-tertiary);">Aplikasi Layanan Desa Digital Terpadu</p>
    </div>

    <div class="space-y-3">
      <router-link v-for="f in features" :key="f.to" :to="f.to" class="block">
        <div
          class="rounded-xl p-4 flex items-center gap-3.5 press transition-all"
          style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
        >
          <div class="icon-box-md" :style="{ background: f.iconBg }">
            <svg class="w-5 h-5" :style="{ color: f.iconClr }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="f.icon" /></svg>
          </div>
          <div class="min-w-0 flex-1">
            <h3 class="font-bold text-[15px]" style="color: var(--clr-text);">{{ f.label }}</h3>
            <p class="text-xs mt-0.5 truncate" style="color: var(--clr-text-tertiary);">{{ f.desc }}</p>
          </div>
          <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background: var(--clr-surface-dim);">
            <svg class="w-4 h-4" style="color: var(--clr-text-tertiary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
          </div>
        </div>
      </router-link>
    </div>
  </div>
</template>
