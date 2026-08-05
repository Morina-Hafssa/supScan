const { contextBridge, ipcRenderer } = require('electron');

console.log('[Preload] Loading...');

// Expose protected methods that allow the renderer process to use
// the ipcRenderer without exposing the entire object
contextBridge.exposeInMainWorld(
    'electronAPI', {
        // Database operations
        db: {
            // Sessions
            getAllSessions: () => ipcRenderer.invoke('db:getAllSessions'),
            getSession: (sessionId) => ipcRenderer.invoke('db:getSession', sessionId),
            saveSession: (sessionId, data) => ipcRenderer.invoke('db:saveSession', sessionId, data),
            deleteSession: (sessionId) => ipcRenderer.invoke('db:deleteSession', sessionId),
            
            // Invoices
            getInvoicesForSession: (sessionId) => ipcRenderer.invoke('db:getInvoicesForSession', sessionId),
            saveInvoice: (sessionId, invoice) => ipcRenderer.invoke('db:saveInvoice', sessionId, invoice),
            deleteInvoice: (invoiceId) => ipcRenderer.invoke('db:deleteInvoice', invoiceId),
            
            // Current invoices
            getCurrentInvoices: () => ipcRenderer.invoke('db:getCurrentInvoices'),
            saveCurrentInvoices: (invoices) => ipcRenderer.invoke('db:saveCurrentInvoices', invoices),
            clearCurrentInvoices: () => ipcRenderer.invoke('db:clearCurrentInvoices'),
            
            // Settings
            getSetting: (key, defaultValue) => ipcRenderer.invoke('db:getSetting', key, defaultValue),
            setSetting: (key, value) => ipcRenderer.invoke('db:setSetting', key, value),
            
            // Stats
            getStats: () => ipcRenderer.invoke('db:getStats')
        }
    }
);

console.log('[Preload] electronAPI exposed successfully');