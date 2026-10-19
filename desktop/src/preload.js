const { contextBridge, ipcRenderer, webFrame } = require('electron');

// Protect renderer AJAX/fetch responses from unexpected PHP server warnings/notices
if (webFrame) {
    try {
        webFrame.executeJavaScript(`
            (() => {
                // 1. Resilient Response.prototype.json
                const origJson = Response.prototype.json;
                Response.prototype.json = async function() {
                    const clone = this.clone();
                    try {
                        return await origJson.call(this);
                    } catch (e) {
                        try {
                            const text = await clone.text();
                            const firstBrace = text.indexOf('{');
                            const firstBracket = text.indexOf('[');
                            let start = -1;
                            if (firstBrace !== -1 && firstBracket !== -1) {
                                start = Math.min(firstBrace, firstBracket);
                            } else if (firstBrace !== -1) {
                                start = firstBrace;
                            } else if (firstBracket !== -1) {
                                start = firstBracket;
                            }
                            if (start !== -1) {
                                return JSON.parse(text.slice(start));
                            }
                        } catch (recoveryErr) {
                            // If recovery fails, throw original parse error
                        }
                        throw e;
                    }
                };

                // 2. Resilient JSON.parse for direct string parsing
                const origJsonParse = JSON.parse;
                JSON.parse = function(text, reviver) {
                    try {
                        return origJsonParse(text, reviver);
                    } catch (e) {
                        if (typeof text === 'string') {
                            const firstBrace = text.indexOf('{');
                            const firstBracket = text.indexOf('[');
                            let start = -1;
                            if (firstBrace !== -1 && firstBracket !== -1) {
                                start = Math.min(firstBrace, firstBracket);
                            } else if (firstBrace !== -1) {
                                start = firstBrace;
                            } else if (firstBracket !== -1) {
                                start = firstBracket;
                            }
                            if (start !== -1) {
                                try {
                                    return origJsonParse(text.slice(start), reviver);
                                } catch (inner) {}
                            }
                        }
                        throw e;
                    }
                };
            })();
        `);
    } catch (err) {
        console.error('Failed to inject JSON resilience handler:', err);
    }
}

// Expose safe, isolated APIs to the renderer process
contextBridge.exposeInMainWorld('electronAPI', {
    isDesktop: true,
    platform: process.platform,
    version: '1.0.0',
    
    // Connectivity & Navigation helpers
    retryConnection: () => ipcRenderer.send('app:retry-connection'),
    checkHealth: () => ipcRenderer.invoke('app:check-health'),
    
    // Print triggers for billing invoices
    printInvoice: (options) => ipcRenderer.send('app:print', options),
    
    // Notification or system actions
    openExternal: (url) => ipcRenderer.send('app:open-external', url)
});
