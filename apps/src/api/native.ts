/**
 * native.ts — Helper untuk akses sensor & hardware via Capacitor
 *
 * Import dari file ini untuk menggunakan fitur native:
 * - Kamera
 * - GPS / Geolokasi
 * - Status jaringan
 * - Informasi perangkat
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
//  HAPTIC FEEDBACK
// ════════════════════════════════════════════════════════════════════════════
export async function hapticLight() {
  try {
    const { Haptics, ImpactStyle } = await import('@capacitor/haptics')
    await Haptics.impact({ style: ImpactStyle.Light })
  } catch {}
}

export async function hapticSuccess() {
  try {
    const { Haptics, ImpactStyle } = await import('@capacitor/haptics')
    await Haptics.impact({ style: ImpactStyle.Medium })
  } catch {}
}

export async function hapticError() {
  try {
    const { Haptics, ImpactStyle } = await import('@capacitor/haptics')
    await Haptics.impact({ style: ImpactStyle.Heavy })
  } catch {}
}

// ════════════════════════════════════════════════════════════════════════════
//  SAVE FILE LOCALLY
// ════════════════════════════════════════════════════════════════════════════
export async function saveFileLocally(_dataUrl: string, _fileName: string): Promise<string | null> {
  if (!isNative) return null
  return null
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


