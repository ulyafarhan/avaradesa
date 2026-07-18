import { api } from '../api/client'
import { endpoints } from '../api/endpoints'
import { localDB } from '../db/localDatabase'

let lastSync = localStorage.getItem('last_sync') ?? ''

export async function pullChanges() {
  try {
    const res = await api.get<any>(endpoints.sync.pull + lastSync)
    const { changes, sync_time } = res.data

    for (const table of Object.keys(changes ?? {})) {
      for (const record of changes[table]) {
        await localDB.put(table, record)
      }
    }

    if (sync_time) {
      lastSync = sync_time
      localStorage.setItem('last_sync', sync_time)
    }

    return { pulled: Object.values(changes ?? {}).reduce<number>((a, b) => a + (Array.isArray(b) ? b.length : 0), 0) }
  } catch (e) {
    return { pulled: 0, error: e }
  }
}
