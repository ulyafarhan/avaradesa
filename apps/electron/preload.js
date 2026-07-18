const { contextBridge, ipcRenderer } = require('electron')

contextBridge.exposeInMainWorld('electronAPI', {
  getVersion: () => process.versions.electron,
  platform: process.platform,
  onMenuAction: (cb: (action: string) => void) => {
    ipcRenderer.on('menu-action', (_event: any, action: string) => cb(action))
  },
})
