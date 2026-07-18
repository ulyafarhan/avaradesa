/**
 * native.ts — Helper untuk akses sensor & hardware via Capacitor
 *
 * Import dari file ini untuk menggunakan fitur native:
 * - Kamera
 * - GPS / Geolokasi
 * - Status jaringan
 * - Informasi perangkat
 * - Haptic feedback
 * - App info
 */

import { Capacitor } from '@capacitor/core'

// ── Cek apakah sedang berjalan di native (Android/iOS) ─────────────────────
export const isNative  = Capacitor.isNativePlatform()
export const platform  = Capacitor.getPlatform() // 'android' | 'ios' | 'web'

// ════════════════════════════════════════════════════════════════════════════
//  KAMERA
// ════════════════════════════════════════════════════════════════════════════
export async function takePhoto(): Promise<string | null> {
  if (!isNative) {
    console.warn('[native] takePhoto: hanya tersedia di perangkat native')
    return null
  }
  const { Camera, CameraResultType, CameraSource } = await import('@capacitor/camera')
  const photo = await Camera.getPhoto({
    quality:      90,
    allowEditing: false,
    resultType:   CameraResultType.DataUrl,
    source:       CameraSource.Camera,
  })
  return photo.dataUrl ?? null
}

export async function pickFromGallery(): Promise<string | null> {
  if (!isNative) return null
  const { Camera, CameraResultType, CameraSource } = await import('@capacitor/camera')
  const photo = await Camera.getPhoto({
    quality:    90,
    resultType: CameraResultType.DataUrl,
    source:     CameraSource.Photos,
  })
  return photo.dataUrl ?? null
}

// ════════════════════════════════════════════════════════════════════════════
//  GEOLOKASI / GPS
// ════════════════════════════════════════════════════════════════════════════
export interface GeoPosition {
  lat: number
  lng: number
  accuracy: number
}

export async function getCurrentLocation(): Promise<GeoPosition | null> {
  try {
    const { Geolocation } = await import('@capacitor/geolocation')
    await Geolocation.requestPermissions()
    const pos = await Geolocation.getCurrentPosition({ enableHighAccuracy: true, timeout: 10000 })
    return {
      lat:      pos.coords.latitude,
      lng:      pos.coords.longitude,
      accuracy: pos.coords.accuracy,
    }
  } catch (err) {
    console.error('[native] getCurrentLocation error:', err)
    return null
  }
}

// ════════════════════════════════════════════════════════════════════════════
//  JARINGAN / NETWORK
// ════════════════════════════════════════════════════════════════════════════
export interface NetworkStatus {
  connected:       boolean
  connectionType:  string  // 'wifi' | 'cellular' | 'none' | 'unknown'
}

export async function getNetworkStatus(): Promise<NetworkStatus> {
  try {
    const { Network } = await import('@capacitor/network')
    const status = await Network.getStatus()
    return { connected: status.connected, connectionType: status.connectionType }
  } catch {
    // Fallback to browser navigator
    return { connected: navigator.onLine, connectionType: 'unknown' }
  }
}

export async function watchNetwork(
  callback: (status: NetworkStatus) => void
): Promise<() => void> {
  const { Network } = await import('@capacitor/network')
  const handle = await Network.addListener('networkStatusChange', (status) => {
    callback({ connected: status.connected, connectionType: status.connectionType })
  })
  return () => handle.remove()
}

// ════════════════════════════════════════════════════════════════════════════
//  DEVICE INFO
// ════════════════════════════════════════════════════════════════════════════
export interface DeviceInfo {
  model:        string
  platform:     string
  osVersion:    string
  manufacturer: string
  isVirtual:    boolean
  memUsed?:     number
}

export async function getDeviceInfo(): Promise<DeviceInfo | null> {
  try {
    const { Device } = await import('@capacitor/device')
    const info = await Device.getInfo()
    return {
      model:        info.model,
      platform:     info.platform,
      osVersion:    info.osVersion,
      manufacturer: info.manufacturer,
      isVirtual:    info.isVirtual,
      memUsed:      info.memUsed,
    }
  } catch {
    return null
  }
}

export async function getBatteryInfo() {
  try {
    const { Device } = await import('@capacitor/device')
    return await Device.getBatteryInfo()
  } catch {
    return null
  }
}

// ════════════════════════════════════════════════════════════════════════════
//  HAPTIC FEEDBACK (getaran perangkat)
// ════════════════════════════════════════════════════════════════════════════
export async function hapticLight() {
  if (!isNative) return
  const { Haptics, ImpactStyle } = await import('@capacitor/haptics')
  await Haptics.impact({ style: ImpactStyle.Light })
}

export async function hapticMedium() {
  if (!isNative) return
  const { Haptics, ImpactStyle } = await import('@capacitor/haptics')
  await Haptics.impact({ style: ImpactStyle.Medium })
}

export async function hapticError() {
  if (!isNative) return
  const { Haptics, NotificationType } = await import('@capacitor/haptics')
  await Haptics.notification({ type: NotificationType.Error })
}

export async function hapticSuccess() {
  if (!isNative) return
  const { Haptics, NotificationType } = await import('@capacitor/haptics')
  await Haptics.notification({ type: NotificationType.Success })
}

// ════════════════════════════════════════════════════════════════════════════
//  STATUS BAR
// ════════════════════════════════════════════════════════════════════════════
export async function setStatusBarDark(isDark: boolean) {
  if (!isNative) return
  const { StatusBar, Style } = await import('@capacitor/status-bar')
  await StatusBar.setStyle({ style: isDark ? Style.Dark : Style.Light })
}

// ════════════════════════════════════════════════════════════════════════════
//  LOCAL NOTIFICATIONS / NOTIFIKASI NATIVE
// ════════════════════════════════════════════════════════════════════════════
export async function scheduleNotification(title: string, body: string, id = 1) {
  try {
    const plugins = (Capacitor as any).Plugins
    if (plugins?.LocalNotifications) {
      await plugins.LocalNotifications.requestPermissions()
      await plugins.LocalNotifications.schedule({
        notifications: [{ title, body, id, schedule: { at: new Date(Date.now() + 1000) } }],
      })
      return
    }
  } catch (err) {
    console.warn('[native] scheduleNotification fallback browser:', title, body)
  }

  if ('Notification' in window && Notification.permission === 'granted') {
    new Notification(title, { body })
  }
}

// ════════════════════════════════════════════════════════════════════════════
//  QR CODE & BARCODE SCANNER
// ════════════════════════════════════════════════════════════════════════════
export async function scanQrCode(): Promise<string | null> {
  if (!isNative) {
    const code = prompt('Simulasi Web QR Scanner: Masukkan Hash QR Code / No. Registrasi Surat:')
    return code ? code.trim() : null
  }
  try {
    const plugins = (Capacitor as any).Plugins
    if (plugins?.BarcodeScanner) {
      await plugins.BarcodeScanner.checkPermission({ force: true })
      plugins.BarcodeScanner.hideBackground()
      document.body.classList.add('scanner-active')
      const result = await plugins.BarcodeScanner.startScan()
      document.body.classList.remove('scanner-active')
      if (result.hasContent) return result.content
    }
    return null
  } catch (err) {
    console.error('[native] scanQrCode error:', err)
    return null
  }
}

// ════════════════════════════════════════════════════════════════════════════
//  FILESYSTEM & DOKUMEN PDF OFFLINE
// ════════════════════════════════════════════════════════════════════════════
export async function saveFileLocally(filename: string, dataUrl: string): Promise<boolean> {
  try {
    const plugins = (Capacitor as any).Plugins
    if (plugins?.Filesystem) {
      await plugins.Filesystem.writeFile({
        path: filename,
        data: dataUrl,
        directory: 'DOCUMENTS',
      })
      hapticSuccess()
      return true
    }
  } catch {
    // Web fallback download
  }

  const link = document.createElement('a')
  link.href = dataUrl
  link.download = filename
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  return true
}

// ════════════════════════════════════════════════════════════════════════════
//  APP INFO & EXIT
// ════════════════════════════════════════════════════════════════════════════
export async function getAppVersion(): Promise<string> {
  try {
    const { App } = await import('@capacitor/app')
    const info = await App.getInfo()
    return `v${info.version} (${info.build})`
  } catch {
    return 'Web'
  }
}

export async function exitApp() {
  if (!isNative) return
  const { App } = await import('@capacitor/app')
  await App.exitApp()
}
