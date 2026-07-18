import { api } from '../api/client'
import { endpoints } from '../api/endpoints'
import { syncQueue } from './syncQueue'

export async function pushChanges() {
  const items = await syncQueue.getAll()
  if (items.length === 0) return { pushed: 0 }

  const payload = items.map(({ table, operation, recordId, data }) => ({
    table, operation, record_id: recordId, data,
  }))

  try {
    await api.post(endpoints.sync.push, { operations: payload })
    await syncQueue.clear()
    return { pushed: payload.length }
  } catch (e) {
    return { pushed: 0, error: e }
  }
}
