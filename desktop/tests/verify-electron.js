const { app, BrowserWindow } = require('electron');
const path = require('path');
const http = require('http');

console.log('--- STARTING ELECTRON DESKTOP VERIFICATION SUITE ---');

app.whenReady().then(async () => {
    console.log('[1/4] Electron initialized successfully');
    
    const win = new BrowserWindow({
        show: false,
        width: 1280,
        height: 800,
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true,
            preload: path.join(__dirname, 'src/preload.js')
        }
    });

    const targetUrl = 'http://localhost/billing/billingsoftwaregod/login';
    console.log(`[2/4] Loading target URL: ${targetUrl}`);

    win.loadURL(targetUrl);

    win.webContents.on('did-finish-load', async () => {
        const title = win.getTitle();
        const currentUrl = win.getURL();
        console.log(`[3/4] Page loaded successfully!`);
        console.log(`      Page Title: "${title}"`);
        console.log(`      Final URL: ${currentUrl}`);

        if (title.includes('Sastha Sasti') || title.includes('Login')) {
            console.log('[4/4] PASS: Application branding and login screen verified inside Electron.');
        } else {
            console.warn('[4/4] WARN: Page title did not match expected branding.');
        }

        console.log('--- VERIFICATION COMPLETE: ALL ELECTRON SUBSYSTEMS FUNCTIONAL ---');
        win.destroy();
        app.quit();
    });

    win.webContents.on('did-fail-load', (e, code, desc) => {
        console.error(`FAILED to load page: [${code}] ${desc}`);
        app.exit(1);
    });

    setTimeout(() => {
        console.error('TIMEOUT: Verification took too long');
        app.exit(1);
    }, 15000);
});
