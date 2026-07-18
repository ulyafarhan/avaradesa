<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import { api } from '../../../api/client'
import { endpoints } from '../../../api/endpoints'
import type { Penduduk } from '../../../api/types'
import { debounce } from '../../../utils/debounce'

interface Paginated<T> { current_page: number; data: T[]; total: number; per_page: number; meta?: { total: number; last_page: number } }

const items = ref<Penduduk[]>([])
const loading = ref(true)
const error = ref('')
const search = ref('')
const statusFilter = ref('')
const viewMode = ref<'grid' | 'table'>('grid')
const page = ref(1)
const total = ref(0)
const lastPage = ref(1)
const perPage = 12

async function fetch() {
  loading.value = true
  error.value = ''
  try {
    const statusParam = statusFilter.value ? `&status=${encodeURIComponent(statusFilter.value)}` : ''
    const params = `?search=${encodeURIComponent(search.value)}&page=${page.value}&per_page=${perPage}${statusParam}`
    const res = await api.get<Paginated<Penduduk>>(endpoints.penduduk.list + params)
    items.value = res.data ?? []
    total.value = res.meta?.total ?? res.total ?? 0
    lastPage.value = res.meta?.last_page ?? (Math.ceil(total.value / perPage) || 1)
  } catch {
    error.value = 'Gagal memuat data kependudukan desa'
  } finally {
    loading.value = false
  }
}

const debouncedFetch = debounce(() => {
  page.value = 1
  fetch()
}, 350)

watch(search, () => {
  debouncedFetch()
})

function handleSearch() {
  page.value = 1
  fetch()
}

function handleStatusFilter(status: string) {
  statusFilter.value = status
  page.value = 1
  fetch()
}

function changePage(newPage: number) {
  if (newPage < 1 || newPage > lastPage.value) return
  page.value = newPage
  fetch()
}

function hapus(nik: string, nama: string) {
  if (!confirm(`Apakah Anda yakin ingin menghapus data warga "${nama}" (NIK: ${nik})?`)) return
  api.delete(endpoints.penduduk.delete(nik)).then(() => fetch())
}

function getInitials(name: string): string {
  if (!name) return '?'
  return name.split(' ').slice(0, 2).map((w) => w[0]).join('').toUpperCase() || '?'
}

function maskNik(nik: string): string {
  if (!nik) return '—'
  if (nik.length === 16) return nik.slice(0, 6) + '******' + nik.slice(-4)
  return nik
}

function maskKk(kk: string): string {
  if (!kk) return '—'
  if (kk.length === 16) return kk.slice(0, 6) + '******' + kk.slice(-4)
  return kk
}

onMounted(fetch)
</script>

<template>
  <div class="space-y-5 max-w-7xl mx-auto">
    <!-- ── PAGE TITLE & ACTION HEADER ── -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
      <div>
        <h1 class="text-2xl font-extrabold tracking-tight" style="color: var(--clr-text);">Data Kependudukan Desa</h1>
        <p class="text-xs mt-1 font-medium" style="color: var(--clr-text-tertiary);">
          Kelola data kependudukan warga desa, pencarian NIK, KK, dan verifikasi status mutasi secara terpusat.
        </p>
      </div>
      <router-link
        to="/admin/penduduk/tambah"
        class="btn btn-primary btn-md self-start sm:self-auto inline-flex items-center gap-2 shadow-sm"
      >
        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
        <span class="font-bold text-xs">Tambah Penduduk Baru</span>
      </router-link>
    </div>

    <!-- ── ENTERPRISE FILAMENT CARD WRAPPER ── -->
    <div
      class="rounded-2xl overflow-hidden shadow-sm transition-all"
      style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
    >
      <!-- Card Toolbar Header -->
      <div class="p-4 sm:p-5 border-b space-y-3 sm:space-y-0 sm:flex sm:items-center sm:justify-between gap-4" style="border-color: var(--clr-border-light); background: var(--clr-surface-overlay);">
        <!-- Search Input -->
        <div class="relative flex-1 max-w-md">
          <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none z-10" style="color: var(--clr-text-tertiary);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
          </div>
          <input
            v-model="search"
            type="text"
            inputmode="search"
            placeholder="Cari NIK, Nama Warga, atau No. KK..."
            class="input-field pr-9 py-2.5 text-xs font-medium w-full rounded-xl"
            style="padding-left: 2.75rem !important;"
            @keyup.enter="handleSearch"
          />
          <button
            v-if="search"
            @click="search = ''; handleSearch()"
            class="absolute inset-y-0 right-0 pr-3 flex items-center z-10 press"
            style="color: var(--clr-text-tertiary);"
            title="Bersihkan Pencarian"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>

        <!-- Filters & View Switcher -->
        <div class="flex items-center gap-3 overflow-x-auto justify-between sm:justify-end">
          <!-- Filter status chips -->
          <div class="flex gap-1.5 p-1 rounded-xl" style="background: var(--clr-surface-dim); border: 1px solid var(--clr-border-light);">
            <button
              v-for="st in [ { l: 'Semua', v: '' }, { l: 'Tetap', v: 'Tetap' }, { l: 'Pendatang', v: 'Pendatang' }, { l: 'Pindah', v: 'Pindah' } ]"
              :key="st.v"
              @click="handleStatusFilter(st.v)"
              class="px-2.5 py-1 rounded-lg text-xs font-semibold press transition-all"
              :style="statusFilter === st.v
                ? 'background: var(--clr-surface); color: var(--clr-primary); box-shadow: 0 1px 3px rgba(0,0,0,0.1);'
                : 'color: var(--clr-text-tertiary);'"
            >
              {{ st.l }}
            </button>
          </div>

          <!-- View Mode Switcher (Grid vs Table) -->
          <div class="hidden sm:flex items-center gap-1 p-1 rounded-xl" style="background: var(--clr-surface-dim); border: 1px solid var(--clr-border-light);">
            <button
              @click="viewMode = 'grid'"
              class="p-1.5 rounded-lg press transition-all"
              :style="viewMode === 'grid' ? 'background: var(--clr-surface); color: var(--clr-primary);' : 'color: var(--clr-text-tertiary);'"
              title="Tampilan Kartu (Grid)"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
            </button>
            <button
              @click="viewMode = 'table'"
              class="p-1.5 rounded-lg press transition-all"
              :style="viewMode === 'table' ? 'background: var(--clr-surface); color: var(--clr-primary);' : 'color: var(--clr-text-tertiary);'"
              title="Tampilan Tabel"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
            </button>
          </div>

          <!-- Total Counter -->
          <span class="badge badge-primary font-bold text-xs shrink-0 whitespace-nowrap">
            {{ total }} Warga
          </span>
        </div>
      </div>

      <!-- Error Alert -->
      <div v-if="error" class="p-4 bg-red-500/10 text-red-500 text-xs font-semibold border-b border-red-500/20">
        {{ error }}
      </div>

      <!-- Skeleton Loading -->
      <div v-if="loading && items.length === 0" class="p-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div v-for="i in 6" :key="i" class="skeleton h-40 rounded-2xl" />
      </div>

      <!-- Empty State -->
      <div v-else-if="items.length === 0" class="text-center py-16 px-4 space-y-3">
        <div class="w-12 h-12 rounded-2xl mx-auto flex items-center justify-center" style="background: var(--clr-surface-dim); color: var(--clr-text-tertiary);">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
        </div>
        <p class="font-bold text-sm" style="color: var(--clr-text);">Tidak ada data kependudukan</p>
        <p class="text-xs" style="color: var(--clr-text-tertiary);">Coba sesuaikan kata kunci pencarian atau tambah data penduduk baru</p>
      </div>

      <!-- ── 1. MODERN GRID CARDS VIEW (DEFAULT HIGH-END UI) ── -->
      <div v-else-if="viewMode === 'grid'" class="p-4 sm:p-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div
          v-for="p in items"
          :key="p.nik"
          class="rounded-2xl p-4.5 transition-all duration-200 hover:-translate-y-1 hover:shadow-lg flex flex-col justify-between group"
          style="background: var(--clr-surface-dim); border: 1px solid var(--clr-border-light);"
        >
          <!-- Top Card Header -->
          <div>
            <div class="flex items-start justify-between gap-3 mb-3">
              <div class="flex items-center gap-3 min-w-0">
                <!-- Avatar Initials -->
                <div
                  class="w-11 h-11 rounded-2xl flex items-center justify-center font-bold text-sm shrink-0 shadow-sm"
                  :style="p.jenis_kelamin === 'L' ? 'background: linear-gradient(135deg, #2563eb, #3b82f6); color: #fff;' : 'background: linear-gradient(135deg, #9333ea, #a855f7); color: #fff;'"
                >
                  {{ getInitials(p.nama_lengkap) }}
                </div>
                <div class="min-w-0">
                  <h3 class="font-bold text-sm leading-snug truncate" style="color: var(--clr-text);" :title="p.nama_lengkap">
                    {{ p.nama_lengkap }}
                  </h3>
                  <div class="flex items-center gap-1.5 mt-0.5">
                    <span class="text-[11px] font-semibold" style="color: var(--clr-text-tertiary);">
                      {{ p.jenis_kelamin === 'L' ? '♂ Laki-laki' : '♀ Perempuan' }}
                    </span>
                    <span>·</span>
                    <span class="text-[11px] font-medium" style="color: var(--clr-text-tertiary);">
                      {{ p.status_keluarga ?? 'Anggota' }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Status Mutasi Badge -->
              <span
                class="badge shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-lg"
                :class="p.status_mutasi === 'Tetap' || !p.status_mutasi ? 'badge-tertiary' : 'badge-warning'"
              >
                {{ p.status_mutasi ?? 'Tetap' }}
              </span>
            </div>

            <!-- Identity Details -->
            <div class="space-y-1.5 p-3 rounded-xl text-xs font-mono mb-4" style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);">
              <div class="flex items-center justify-between">
                <span class="text-[10px] font-semibold uppercase tracking-wider" style="color: var(--clr-text-tertiary);">NIK:</span>
                <span class="font-bold" style="color: var(--clr-primary);" :title="p.nik">{{ maskNik(p.nik) }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-[10px] font-semibold uppercase tracking-wider" style="color: var(--clr-text-tertiary);">No. KK:</span>
                <span class="font-semibold" style="color: var(--clr-text-secondary);" :title="p.no_kk">{{ maskKk(p.no_kk) }}</span>
              </div>
            </div>
          </div>

          <!-- Bottom Card Action Buttons -->
          <div class="pt-3 border-t flex items-center justify-between gap-2" style="border-color: var(--clr-border-light);">
            <router-link
              :to="`/admin/penduduk/${p.nik}/edit`"
              class="flex-1 py-1.5 rounded-xl text-xs font-bold press transition-all flex items-center justify-center gap-1.5"
              style="background: var(--clr-primary-bg); color: var(--clr-primary);"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
              <span>Edit Data</span>
            </router-link>

            <button
              @click="hapus(p.nik, p.nama_lengkap)"
              class="px-3 py-1.5 rounded-xl text-xs font-bold press transition-all flex items-center justify-center gap-1.5"
              style="background: var(--clr-error-bg); color: var(--clr-error-text);"
              title="Hapus Warga"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
              <span>Hapus</span>
            </button>
          </div>
        </div>
      </div>

      <!-- ── 2. EXECUTIVE TABLE LIST VIEW ── -->
      <div v-else class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr style="background: var(--clr-surface-dim); border-bottom: 1px solid var(--clr-border-light);">
              <th class="px-4 py-3.5 text-[11px] font-bold tracking-wider uppercase" style="color: var(--clr-text-tertiary);">NIK</th>
              <th class="px-4 py-3.5 text-[11px] font-bold tracking-wider uppercase" style="color: var(--clr-text-tertiary);">NAMA LENGKAP</th>
              <th class="px-4 py-3.5 text-[11px] font-bold tracking-wider uppercase text-center" style="color: var(--clr-text-tertiary);">JK</th>
              <th class="px-4 py-3.5 text-[11px] font-bold tracking-wider uppercase" style="color: var(--clr-text-tertiary);">NO. KARTU KELUARGA</th>
              <th class="px-4 py-3.5 text-[11px] font-bold tracking-wider uppercase" style="color: var(--clr-text-tertiary);">STATUS MUTASI</th>
              <th class="px-4 py-3.5 text-[11px] font-bold tracking-wider uppercase text-right" style="color: var(--clr-text-tertiary);">AKSI MANAGEMENT</th>
            </tr>
          </thead>
          <tbody class="divide-y" style="border-color: var(--clr-border-light);">
            <tr
              v-for="p in items"
              :key="p.nik"
              class="hover:bg-[var(--clr-surface-dim)] transition-colors group"
            >
              <td class="px-4 py-3.5 whitespace-nowrap font-mono text-xs font-bold" style="color: var(--clr-primary);" :title="p.nik">
                {{ maskNik(p.nik) }}
              </td>
              <td class="px-4 py-3.5 whitespace-nowrap">
                <div class="flex items-center gap-3">
                  <div
                    class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs shrink-0"
                    :style="p.jenis_kelamin === 'L' ? 'background: var(--clr-primary-bg); color: var(--clr-primary);' : 'background: var(--clr-secondary-bg); color: var(--clr-secondary);'"
                  >
                    {{ getInitials(p.nama_lengkap) }}
                  </div>
                  <span class="font-bold text-xs" style="color: var(--clr-text);">{{ p.nama_lengkap }}</span>
                </div>
              </td>
              <td class="px-4 py-3.5 whitespace-nowrap text-center">
                <span
                  class="inline-flex items-center justify-center px-2 py-0.5 rounded-lg text-[11px] font-bold"
                  :style="p.jenis_kelamin === 'L' ? 'background: var(--clr-primary-bg); color: var(--clr-primary);' : 'background: var(--clr-secondary-bg); color: var(--clr-secondary);'"
                >
                  {{ p.jenis_kelamin === 'L' ? 'L' : 'P' }}
                </span>
              </td>
              <td class="px-4 py-3.5 whitespace-nowrap font-mono text-xs font-semibold" style="color: var(--clr-text-secondary);" :title="p.no_kk">
                {{ maskKk(p.no_kk) }}
              </td>
              <td class="px-4 py-3.5 whitespace-nowrap">
                <span class="badge badge-primary text-[10px] font-semibold">
                  {{ p.status_mutasi ?? 'Tetap' }}
                </span>
              </td>
              <td class="px-4 py-3.5 whitespace-nowrap text-right">
                <div class="inline-flex items-center gap-1.5">
                  <router-link
                    :to="`/admin/penduduk/${p.nik}/edit`"
                    class="px-2.5 py-1 rounded-lg text-xs font-bold press transition-all flex items-center gap-1"
                    style="background: var(--clr-primary-bg); color: var(--clr-primary);"
                    title="Edit Data"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    <span>Edit</span>
                  </router-link>
                  <button
                    @click="hapus(p.nik, p.nama_lengkap)"
                    class="px-2.5 py-1 rounded-lg text-xs font-bold press transition-all flex items-center gap-1"
                    style="background: var(--clr-error-bg); color: var(--clr-error-text);"
                    title="Hapus Data"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    <span>Hapus</span>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Card Footer Toolbar & Pagination Controls -->
      <div class="p-4 border-t flex flex-col sm:flex-row items-center justify-between gap-3 text-xs" style="border-color: var(--clr-border-light); background: var(--clr-surface-overlay);">
        <span style="color: var(--clr-text-tertiary);">
          Menampilkan Halaman <span class="font-bold" style="color: var(--clr-text);">{{ page }}</span> dari <span class="font-bold" style="color: var(--clr-text);">{{ lastPage }}</span> (Total {{ total }} Data Warga)
        </span>

        <div class="flex items-center gap-2">
          <button
            @click="changePage(page - 1)"
            :disabled="page <= 1"
            class="px-3 py-1.5 rounded-xl text-xs font-bold press transition-all disabled:opacity-40 disabled:pointer-events-none"
            style="background: var(--clr-surface-dim); color: var(--clr-text); border: 1px solid var(--clr-border-light);"
          >
            ← Sebelumnya
          </button>

          <span class="px-3 py-1.5 rounded-xl font-bold text-xs" style="background: var(--clr-primary-bg); color: var(--clr-primary);">
            {{ page }} / {{ lastPage }}
          </span>

          <button
            @click="changePage(page + 1)"
            :disabled="page >= lastPage"
            class="px-3 py-1.5 rounded-xl text-xs font-bold press transition-all disabled:opacity-40 disabled:pointer-events-none"
            style="background: var(--clr-surface-dim); color: var(--clr-text); border: 1px solid var(--clr-border-light);"
          >
            Selanjutnya →
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
