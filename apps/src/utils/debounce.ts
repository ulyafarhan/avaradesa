/**
 * UTILITY — Debounce Function & Composable
 *
 * Mencegah pengiriman HTTP Request ke server pada setiap ketukan tombol keyboard (keystroke).
 * Menunda eksekusi fungsi pencarian hingga pengguna berhenti mengetik selama rentang waktu tertentu (default: 350ms).
 */

export function debounce<T extends (...args: any[]) => any>(
  fn: T,
  delayMs = 350
): (...args: Parameters<T>) => void {
  let timeoutId: ReturnType<typeof setTimeout> | null = null

  return (...args: Parameters<T>) => {
    if (timeoutId) clearTimeout(timeoutId)
    timeoutId = setTimeout(() => {
      fn(...args)
    }, delayMs)
  }
}
