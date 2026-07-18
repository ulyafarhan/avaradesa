import { localDB } from '../db/localDatabase'

interface QueueItem {
  id: string
  table: string
  operation: 'create' | 'update' | 'delete'
  recordId: string
  data: any
  timestamp: number
}

const QUEUE_STORE = 'sync_log'

export const syncQueue = {
  async add(item: Omit<QueueItem, 'id' | 'timestamp'>) {
    const record: QueueItem = {
      id: crypto.randomUUID(),
      timestamp: Date.now(),
      ...item,
    }
    await localDB.put(QUEUE_STORE, record)
    return record
  },

  async getAll(): Promise<QueueItem[]> {
    return localDB.getAll(QUEUE_STORE)
  },

  async remove(id: string) {
    return localDB.delete(QUEUE_STORE, id)
  },

  async clear() {
    return localDB.clear(QUEUE_STORE)
  },

  async count(): Promise<number> {
    const items = await localDB.getAll(QUEUE_STORE)
    return items.length
  },
}
