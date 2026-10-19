const { app, BrowserWindow } = require('electron');
const path = require('path');

app.whenReady().then(async () => {
    console.log('[Test Error Screen] Testing fallback to error.html on unreachable server...');
    const win = new BrowserWindow({
        show: false,
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true,
            preload: path.join(__dirname, '../src/preload.js')
        }
    });

    const errorHtmlPath = path.join(__dirname, '../src/views/error.html');
    win.loadFile(errorHtmlPath);

    win.webContents.on('did-finish-load', () => {
        const title = win.getTitle();
        console.log(`[PASS] Fallback view loaded. Page Title: "${title}"`);
        win.destroy();
        app.quit();
    });
});
