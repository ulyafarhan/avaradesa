import { api } from '../api/client'
import { endpoints } from '../api/endpoints'
import { watchNetwork, getNetworkStatus } from '../api/native'
import { localDB } from '../db/localDatabase'

export interface SyncOperation {
  client_id:  string
  type:       'pengajuan_surat' | 'mutasi'
  action:     'create' | 'update' | 'delete'
  data:       Record<string, any>
  created_at: string
}

export interface SyncState {
  isOnline:       boolean
  isSyncing:      boolean
  pendingCount:   number
  lastSyncedAt:   string | null
}

const QUEUE_STORE = 'sync_log'
const SYNC_TOKEN = 'avaradesa_sync_token'

function uuidv4(): string {
  return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
    const r = (Math.random() * 16) | 0
    const v = c === 'x' ? r : (r & 0x3) | 0x8
    return v.toString(16)
  })
}

export const syncManager = {
  async getQueue(): Promise<SyncOperation[]> {
    return localDB.getAll(QUEUE_STORE)
  },

  async enqueue(type: 'pengajuan_surat' | 'mutasi', data: Record<string, any>, action: 'create' | 'update' | 'delete' = 'create'): Promise<SyncOperation> {
    const op: SyncOperation = {
      client_id:  uuidv4(),
      type,
      action,
      data,
      created_at: new Date().toISOString().replace(/\.\d{3}Z$/, 'Z'),
    }
    await localDB.put(QUEUE_STORE, op)
    return op
  },

  async pushQueue(): Promise<boolean> {
    const queue = await this.getQueue()
    if (queue.length === 0) return true

    try {
      const res: any = await api.post(endpoints.sync.push, { operations: queue })
      if (res && res.status === 'processed') {
        const results: any[] = res.results ?? []
        const successIds = new Set(
          results.filter((r) => r.status === 'success').map((r) => r.client_id)
        )
        
        // Remove successful operations from IndexedDB
        for (const op of queue) {
          if (successIds.has(op.client_id)) {
            await localDB.delete(QUEUE_STORE, op.client_id)
          }
        }

        if (res.server_sync_token) {
          localStorage.setItem(SYNC_TOKEN, res.server_sync_token)
        }
        
        const remaining = await this.getQueue()
        return remaining.length === 0
      }
    } catch (err) {
      console.warn('[SyncManager] Gagal melakukan push queue:', err)
    }
    return false
  },

  async pullUpdates(): Promise<any> {
    const since = localStorage.getItem(SYNC_TOKEN) ?? '2020-01-01T00:00:00Z'
    try {
      const res: any = await api.get(`${endpoints.sync.pull}${encodeURIComponent(since)}`)
      if (res && res.meta?.sync_token) {
        localStorage.setItem(SYNC_TOKEN, res.meta.sync_token)
      }
      return res?.data ?? null
    } catch (err) {
      console.warn('[SyncManager] Gagal melakukan pull updates:', err)
      return null
    }
  },

  initAutoSync(onStatusChange?: (state: SyncState) => void): () => void {
    const checkAndSync = async (connected: boolean) => {
      const pending = (await this.getQueue()).length
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
          pendingCount: (await this.getQueue()).length,
          lastSyncedAt: localStorage.getItem(SYNC_TOKEN),
        })
      }
    }

    getNetworkStatus().then((s) => checkAndSync(s.connected))

    let cleanupFunc: (() => void) | null = null
    watchNetwork((s) => checkAndSync(s.connected)).then((unwatch) => {
      cleanupFunc = unwatch
    })

    return () => {
      cleanupFunc?.()
    }
  },
}
