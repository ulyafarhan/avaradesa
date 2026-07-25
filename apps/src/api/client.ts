import type { ApiError } from './types'

export const BASE_URL = 'https://avaradesa.my.id'

function getToken(): string | null {
  try {
    const raw = localStorage.getItem('auth')
    if (!raw) return null
    return JSON.parse(raw).state?.token ?? null
  } catch {
    return null
  }
}

async function request<T>(
  endpoint: string,
  options: RequestInit = {}
): Promise<T> {
  const token = getToken()
  const headers: Record<string, string> = {
    'Content-Type': 'application/json',
    ...(options.headers as Record<string, string>),
  }
  if (token) headers['Authorization'] = `Bearer ${token}`

  const url = `${BASE_URL}${endpoint}`
  // ponytail: debug log — hapus setelah root cause ditemukan
  console.log(`[API] ${options.method ?? 'GET'} ${url}`, { hasToken: !!token })

  let res: Response
  try {
    res = await fetch(url, { ...options, headers })
  } catch (e: unknown) {
    const msg = e instanceof TypeError ? 'NETWORK_ERROR: CORS / DNS / SSL' : String(e)
    console.error(`[API] FETCH FAILED: ${msg}`, { url, method: options.method ?? 'GET' })
    throw { message: msg }
  }

  if (!res.ok) {
    if (res.status === 401 && !endpoint.includes('/auth/login')) {
      localStorage.removeItem('auth')
      if (window.location.hash.includes('/admin')) {
        window.location.href = '#/auth/login-admin'
      } else {
        window.location.href = '#/auth/login-warga'
      }
    }
    const body = await res.text().catch(() => '')
    let err: ApiError
    try {
      err = JSON.parse(body)
    } catch {
      err = { message: `HTTP ${res.status}: ${res.statusText}${body ? ' — ' + body.slice(0, 200) : ''}` }
    }
    console.error(`[API] HTTP ${res.status}`, { url, method: options.method ?? 'GET', error: err.message })
    throw err
  }

  return res.json()
}

export const api = {
  get: <T>(endpoint: string) => request<T>(endpoint),
  post: <T>(endpoint: string, body?: unknown) =>
    request<T>(endpoint, { method: 'POST', body: body ? JSON.stringify(body) : undefined }),
  put: <T>(endpoint: string, body?: unknown) =>
    request<T>(endpoint, { method: 'PUT', body: body ? JSON.stringify(body) : undefined }),
  patch: <T>(endpoint: string, body?: unknown) =>
    request<T>(endpoint, { method: 'PATCH', body: body ? JSON.stringify(body) : undefined }),
  delete: <T>(endpoint: string) =>
    request<T>(endpoint, { method: 'DELETE' }),
}
