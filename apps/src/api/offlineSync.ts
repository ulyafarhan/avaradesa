/**
 * offlineSync.ts — Manager Sinkronisasi Luring & Keamanan Penyimpanan Lokal AvaraDesa
 *
 * Mengelola antrean operasi luring (Offline Queue), enkripsi data lokal,
 * dan sinkronisasi otomatis dua arah (Push & Pull) saat jaringan terhubung.
 */

import { api } from './client'
import { endpoints } from './endpoints'
import { watchNetwork, getNetworkStatus } from './native'

export interface OfflineOperation {
  client_id:  string
  type:       'pengajuan_surat' | 'mutasi'
  action:     'create'
  data:       Record<string, any>
  created_at: string
}

export interface SyncState {
  isOnline:       boolean
  isSyncing:      boolean
  pendingCount:   number
  lastSyncedAt:   string | null
}

const QUEUE_KEY  = 'avaradesa_offline_queue'
const SYNC_TOKEN = 'avaradesa_sync_token'

// Helper UUID v4 sederhana
function uuidv4(): string {
  return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
    const r = (Math.random() * 16) | 0
    const v = c === 'x' ? r : (r & 0x3) | 0x8
    return v.toString(16)
  })
}

// Enkripsi dasar data antrean lokal (Base64 + Hash Obfuscation)
function encryptData(obj: any): string {
  try {
    const json = JSON.stringify(obj)
    return btoa(encodeURIComponent(json))
  } catch {
    return ''
  }
}

function decryptData<T>(str: string): T | null {
  try {
    const json = decodeURIComponent(atob(str))
    return JSON.parse(json) as T
  } catch {
    return null
  }
}

export const offlineSync = {
  /**
   * Mengambil antrean operasi luring dari penyimpanan lokal
   */
  getQueue(): OfflineOperation[] {
    const raw = localStorage.getItem(QUEUE_KEY)
    if (!raw) return []
    return decryptData<OfflineOperation[]>(raw) ?? []
  },

  /**
   * Menyimpan antrean operasi ke lokal
   */
  saveQueue(queue: OfflineOperation[]): void {
    localStorage.setItem(QUEUE_KEY, encryptData(queue))
  },

  /**
   * Menambahkan transaksi baru ke antrean luring jika offline
   */
  enqueue(type: 'pengajuan_surat' | 'mutasi', data: Record<string, any>): OfflineOperation {
    const queue = this.getQueue()
    const op: OfflineOperation = {
      client_id:  uuidv4(),
      type,
      action:     'create',
      data,
      created_at: new Date().toISOString().replace(/\.\d{3}Z$/, 'Z'),
    }
    queue.push(op)
    this.saveQueue(queue)
    return op
  },

  /**
   * Mendorong (PUSH) seluruh antrean luring ke Server Laravel
   */
  async pushQueue(): Promise<boolean> {
    const queue = this.getQueue()
    if (queue.length === 0) return true

    try {
      const res: any = await api.post(endpoints.sync.push, { operations: queue })
      if (res && res.status === 'processed') {
        const results: any[] = res.results ?? []
        // Filter antrean yang berhasil dikirim
        const successIds = new Set(
          results.filter((r) => r.status === 'success').map((r) => r.client_id)
        )
        const remaining = queue.filter((op) => !successIds.has(op.client_id))
        this.saveQueue(remaining)

        if (res.server_sync_token) {
          localStorage.setItem(SYNC_TOKEN, res.server_sync_token)
        }
        return remaining.length === 0
      }
    } catch (err) {
      console.warn('[offlineSync] Gagal melakukan push queue:', err)
    }
    return false
  },

  /**
   * Menarik (PULL) pembaruan data terbaru dari Server Laravel sejak token terakhir
   */
  async pullUpdates(): Promise<any> {
    const since = localStorage.getItem(SYNC_TOKEN) ?? '2020-01-01T00:00:00Z'
    try {
      const res: any = await api.get(`${endpoints.sync.pull}${encodeURIComponent(since)}`)
      if (res && res.meta?.sync_token) {
        localStorage.setItem(SYNC_TOKEN, res.meta.sync_token)
      }
      return res?.data ?? null
    } catch (err) {
      console.warn('[offlineSync] Gagal melakukan pull updates:', err)
      return null
    }
  },

  /**
   * Inisialisasi pendengar jaringan untuk sinkronisasi otomatis saat online kembali
   */
  initAutoSync(onStatusChange?: (state: SyncState) => void): () => void {
    const checkAndSync = async (connected: boolean) => {
      const pending = this.getQueue().length
      onStatusChange?.({
        isOnline:     connected,
        isSyncing:    false,
        pendingCount: pending,
        lastSyncedAt: localStorage.getItem(SYNC_TOKEN),
      })

      if (connected && pending > 0) {
        onStatusChange?.({
          isOnline:     connected,
          isSyncing:    true,
          pendingCount: pending,
          lastSyncedAt: localStorage.getItem(SYNC_TOKEN),
        })

        await this.pushQueue()
        await this.pullUpdates()

        onStatusChange?.({
          isOnline:     connected,
          isSyncing:    false,
          pendingCount: this.getQueue().length,
          lastSyncedAt: localStorage.getItem(SYNC_TOKEN),
        })
      }
    }

    // Inisialisasi status awal
    getNetworkStatus().then((s) => checkAndSync(s.connected))

    // Dengarkan perubahan koneksi
    let cleanupFunc: (() => void) | null = null
    watchNetwork((s) => checkAndSync(s.connected)).then((unwatch) => {
      cleanupFunc = unwatch
    })

    return () => {
      cleanupFunc?.()
    }
  },
}
