<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { api } from '../../../api/client'
import { endpoints } from '../../../api/endpoints'
import type { PengajuanSurat } from '../../../api/types'
import StatusBadge from '../../../components/StatusBadge.vue'

import { useAuthStore } from '../../../stores/authStore'
import { saveFileLocally } from '../../../api/native'

const route   = useRoute()
const router  = useRouter()
const auth    = useAuthStore()
const item    = ref<PengajuanSurat | null>(null)
const loading = ref(true)
const downloading = ref(false)

const showPreviewModal = ref(false)

onMounted(async () => {
  try {
    const res: any = await api.get(endpoints.surat.detail(route.params.id as string))
    item.value = res.data ?? res
  } catch {
    item.value = null
  } finally {
    loading.value = false
  }
})

function formatDate(d: string): string {
  if (!d) return ''
  return new Date(d).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })
}

async function handleDownloadPrint() {
  if (!item.value) return
  downloading.value = true
  try {
    const downloadUrl = `/api/v1/surat/pengajuan/${item.value.id}/download`
    const token = auth.token ?? ''
    const baseUrl = import.meta.env.VITE_API_URL ?? 'http://localhost:8000'
    
    const response = await fetch(`${baseUrl}${downloadUrl}`, {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    })
    
    if (!response.ok) {
      throw new Error('Gagal mengambil berkas PDF')
    }
    
    const blob = await response.blob()
    const blobUrl = URL.createObjectURL(blob)
    const filename = `Surat_${item.value.nomor_registrasi?.replace(/\//g, '_') ?? 'Selesai'}.pdf`
    
    await saveFileLocally(filename, blobUrl)
  } catch (err: any) {
    alert(err?.message ?? 'Gagal mengunduh PDF surat')
  } finally {
    downloading.value = false
  }
}

function printClientPdf() {
  if (!item.value?.surat_html) {
    alert('Format surat tidak tersedia untuk dicetak secara offline.');
    return;
  }

  const elem = document.getElementById('printable-surat')
  if (!elem) {
    alert('Templat surat tidak ditemukan.')
    return
  }

  const win = window.open('', '', 'width=800,height=900')
  if (!win) return
  
  win.document.write(`
    <!DOCTYPE html>
    <html>
      <head>
        <title>Cetak Surat - ${item.value?.nomor_registrasi ?? ''}</title>
        <style>
          @page { size: A4; margin: 20mm 15mm; }
          body { 
            font-family: 'Times New Roman', Times, serif; 
            background: #fff; 
            color: #000; 
            margin: 0;
            padding: 0;
            font-size: 12pt;
            line-height: 1.35;
          }
          * { box-sizing: border-box; }
          
          /* Kop Surat */
          .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
          }
          .kop-logo {
            width: 80px;
            display: flex;
            justify-content: flex-start;
          }
          .kop-logo img { width: 70px; height: auto; }
          .kop-text {
            flex: 1;
            text-align: center;
          }
          .kop-text h4, .kop-text h3, .kop-text p {
            margin: 0; padding: 0; line-height: 1.25;
          }
          .kop-text h4 { font-size: 11pt; font-weight: bold; text-transform: uppercase; }
          .kop-text h3 { font-size: 13pt; font-weight: bold; text-transform: uppercase; margin-top: 2px; }
          .kop-text p { font-size: 8pt; font-style: italic; margin-top: 4px; }
          .kop-space { width: 80px; } /* for balance */

          /* Header */
          .title-box { text-align: center; margin: 15px 0; }
          .title-box h1 { font-size: 14pt; text-decoration: underline; font-weight: bold; margin: 0; }
          .title-box p { font-size: 12pt; margin: 5px 0 0 0; }

          /* Content */
          .content-text { text-align: justify; margin: 15px 0; }
          
          /* Table Biodata */
          .biodata-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 10px 30px;
          }
          .biodata-table td { padding: 3px 0; vertical-align: top; }
          .biodata-table td.col-label { width: 160px; }
          .biodata-table td.col-colon { width: 15px; }

          /* Signature block */
          .footer-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
          }
          .qr-box {
            text-align: center;
          }
          .qr-box img { width: 80px; height: 80px; margin-bottom: 5px; }
          .qr-box p { font-size: 8pt; margin: 0; color: #555; }
          
          .signature-box {
            text-align: center;
            width: 250px;
            position: relative;
          }
          .signature-box p { margin: 2px 0; }
          .signature-space { height: 70px; position: relative; }
          
          /* Stempel placement */
          .stempel-img {
            position: absolute;
            left: -40px;
            top: 10px;
            width: 130px;
            opacity: 0.85;
            z-index: -1;
          }

          .bottom-footer {
            margin-top: 50px;
            text-align: center;
            font-size: 8pt;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px;
          }
        </style>
      </head>
      <body>
        ${elem.innerHTML}
        <script>
          window.onload = function() {
            window.focus();
            window.print();
            setTimeout(function() { window.close(); }, 500);
          }
        <\/script>
      </body>
    </html>
  `)
  win.document.close()
}
</script>

<template>
  <div class="space-y-4">
    <!-- Breadcrumb back -->
    <div class="flex items-center gap-2 text-xs" style="color: var(--clr-text-tertiary);">
      <button @click="router.back()" class="hover:text-[var(--clr-primary)] flex items-center gap-1 font-medium transition-colors">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
        Kembali
      </button>
      <span>/</span>
      <router-link to="/warga/surat/pengajuan" class="hover:text-[var(--clr-primary)] transition-colors">Pengajuan</router-link>
    </div>

    <!-- Skeleton -->
    <div v-if="loading" class="space-y-3">
      <div class="skeleton h-48 rounded-xl" />
      <div class="skeleton h-32 rounded-xl" />
    </div>

    <div v-else-if="item" class="space-y-4">
      <!-- Main Detail Card -->
      <div
        class="rounded-xl p-5"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
      >
        <div class="flex items-start justify-between gap-3 mb-4 pb-3" style="border-bottom: 1px solid var(--clr-border-light);">
          <div>
            <h2 class="text-lg font-bold" style="color: var(--clr-text);">{{ item.kategori?.nama_surat ?? 'Surat Keterangan' }}</h2>
            <p class="text-xs font-mono mt-0.5" style="color: var(--clr-text-tertiary);">No. Reg: {{ item.nomor_registrasi }}</p>
          </div>
          <StatusBadge :status="item.status" size="lg" />
        </div>

        <div class="space-y-3 text-[14px]">
          <div class="flex justify-between py-1.5" style="border-bottom: 1px solid var(--clr-border-light);">
            <span style="color: var(--clr-text-secondary);">Jenis Surat</span>
            <span class="font-semibold text-right" style="color: var(--clr-text);">{{ item.kategori?.nama_surat ?? '-' }}</span>
          </div>
          <div class="flex justify-between py-1.5" style="border-bottom: 1px solid var(--clr-border-light);">
            <span style="color: var(--clr-text-secondary);">Keperluan</span>
            <span class="font-semibold text-right max-w-[60%]" style="color: var(--clr-text);">{{ item?.data_isian?.keperluan ?? '-' }}</span>
          </div>
          <div v-if="item.catatan_penolakan" class="flex justify-between py-1.5" style="border-bottom: 1px solid var(--clr-border-light);">
            <span style="color: var(--clr-text-secondary);">Catatan Alasan</span>
            <span class="font-semibold text-right max-w-[60%]" style="color: var(--clr-error);">{{ item.catatan_penolakan }}</span>
          </div>
          <div class="flex justify-between py-1.5">
            <span style="color: var(--clr-text-secondary);">Tanggal Pengajuan</span>
            <span class="font-semibold text-right" style="color: var(--clr-text);">{{ formatDate(item.created_at) }}</span>
          </div>
        </div>

        <!-- Action Download / Print Buttons when Selesai -->
        <div v-if="item.status === 'Selesai'" class="mt-4 pt-3 border-t space-y-2.5" style="border-color: var(--clr-border-light);">
          <button @click="printClientPdf" class="w-full btn btn-primary py-3 flex items-center justify-center gap-2 font-bold shadow-md press">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Surat Ke PDF (Klien)
          </button>
          <div class="grid grid-cols-2 gap-2">
            <button @click="showPreviewModal = true" class="w-full py-2 text-xs font-semibold rounded-xl border flex items-center justify-center gap-1.5 transition-colors press" style="border-color: var(--clr-border); color: var(--clr-text);">
              <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
              Pratinjau Surat
            </button>
            <button @click="handleDownloadPrint" class="w-full py-2 text-xs font-semibold rounded-xl border flex items-center justify-center gap-1.5 transition-colors press" style="border-color: var(--clr-border); color: var(--clr-primary);">
              <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
              Unduh PDF Server
            </button>
          </div>
        </div>
      </div>

      <!-- Native Vue Template (Hidden, Used for Print) -->
      <div class="hidden" v-if="item?.surat_html">
        <div id="printable-surat" v-html="item.surat_html"></div>
      </div>

      <!-- Modal Pratinjau Lembar Surat -->
      <div v-if="showPreviewModal" class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white text-black max-w-2xl w-full h-[90vh] flex flex-col overflow-hidden rounded-2xl shadow-2xl relative">
          <div class="flex items-center justify-between p-4 border-b bg-gray-50">
            <h3 class="font-bold text-sm">Pratinjau Lembar Surat Resmi (Klien)</h3>
            <button @click="showPreviewModal = false" class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-700 hover:bg-gray-300">✕</button>
          </div>
          <div class="flex-1 bg-gray-200 p-6 overflow-y-auto flex justify-center">
            <div class="bg-white shadow-md w-full max-w-[210mm] min-h-[297mm] p-[15mm] text-black" v-if="item?.surat_html">
              <div v-html="item.surat_html"></div>
            </div>
          </div>

          <div class="p-4 bg-white border-t flex gap-2">
            <button @click="printClientPdf(); showPreviewModal = false;" class="w-full btn btn-primary py-3 font-bold text-xs flex items-center justify-center gap-1.5 shadow-md press">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
              Cetak Surat Ini Sekarang
            </button>
          </div>
        </div>
      </div>

      <!-- Data Isian Card -->
      <div
        v-if="item?.data_isian && Object.keys(item.data_isian).length > 1"
        class="rounded-xl p-5"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
      >
        <h3 class="font-bold text-[15px] mb-3" style="color: var(--clr-text);">Rincian Data Isian</h3>
        <div class="space-y-2.5 text-[14px]">
          <template v-for="(val, key) in item?.data_isian" :key="key">
            <div v-if="key !== 'keperluan'" class="flex justify-between py-1.5 border-b last:border-0" style="border-color: var(--clr-border-light);">
              <span class="capitalize" style="color: var(--clr-text-secondary);">{{ String(key).replace(/_/g, ' ') }}</span>
              <span class="font-semibold text-right max-w-[60%]" style="color: var(--clr-text);">{{ val }}</span>
            </div>
          </template>
        </div>
      </div>

      <!-- Timeline Tracking -->
      <div
        class="rounded-xl p-5"
        style="background: var(--clr-surface); border: 1px solid var(--clr-border-light);"
      >
        <h3 class="font-bold text-[15px] mb-4" style="color: var(--clr-text);">Riwayat Status Tracking</h3>

        <div v-if="!item?.tracking?.length" class="text-center py-6 text-xs" style="color: var(--clr-text-tertiary);">
          Belum ada pembaruan tracking
        </div>

        <div v-else class="space-y-4 relative pl-3">
          <div
            v-for="(t, i) in item?.tracking"
            :key="i"
            class="flex items-start gap-3 relative"
          >
            <!-- Line connector -->
            <div
              v-if="item?.tracking && i < item.tracking.length - 1"
              class="absolute left-[7px] top-[18px] bottom-[-16px] w-[2px]"
              style="background: var(--clr-border);"
            />
            <!-- Dot -->
            <div
              class="w-4 h-4 rounded-full flex items-center justify-center shrink-0 z-10 mt-0.5"
              :style="{ background: (item?.tracking && i === item.tracking.length - 1) ? 'var(--clr-primary)' : 'var(--clr-border)' }"
            >
              <div class="w-1.5 h-1.5 rounded-full bg-white" />
            </div>
            <!-- Info -->
            <div class="min-w-0 flex-1">
              <p class="font-bold text-[14px]" style="color: var(--clr-text);">{{ t.status_baru }}</p>
              <p v-if="t.keterangan_update" class="text-[12px] mt-0.5" style="color: var(--clr-text-secondary);">{{ t.keterangan_update }}</p>
              <p class="text-[11px] mt-1 font-medium" style="color: var(--clr-text-tertiary);">
                {{ t.diupdate_oleh ?? 'Sistem' }} · {{ formatDate(t.created_at) }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="text-center py-16 text-sm" style="color: var(--clr-text-tertiary);">
      Pengajuan tidak ditemukan
    </div>
  </div>
</template>
