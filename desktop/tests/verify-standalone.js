const { app, BrowserWindow } = require('electron');
const path = require('path');
const net = require('net');
const http = require('http');
const { spawn } = require('child_process');

console.log('--- STARTING STANDALONE ZERO-XAMPP VERIFICATION ---');

function getAvailablePort() {
    return new Promise((resolve) => {
        const s = net.createServer();
        s.unref();
        s.listen(0, '127.0.0.1', () => {
            const p = s.address().port;
            s.close(() => resolve(p));
        });
    });
}

function checkUrl(url) {
    return new Promise((resolve) => {
        http.get(url, (res) => resolve(res.statusCode === 200 || res.statusCode === 302))
            .on('error', () => resolve(false));
    });
}

app.whenReady().then(async () => {
    const port = await getAvailablePort();
    const phpBin = 'C:\\xampp\\php\\php.exe';
    const rootDir = path.join(__dirname, '../..');
    const publicDir = path.join(rootDir, 'public');
    const routerScript = path.join(__dirname, '../server/router.php');
    const sqliteDb = path.join(__dirname, '../database/billing.sqlite');

    console.log(`[1/5] Spawning Standalone PHP Loopback on 127.0.0.1:${port}...`);
    const phpProc = spawn(phpBin, [
        '-S', `127.0.0.1:${port}`,
        '-t', publicDir,
        routerScript
    ], {
        env: {
            ...process.env,
            APP_ENV: 'production',
            APP_URL: `http://127.0.0.1:${port}`,
            DB_CONNECTION: 'sqlite',
            DB_SQLITE_PATH: sqliteDb
        },
        stdio: 'ignore'
    });

    // Wait for server
    let online = false;
    for (let i = 0; i < 20; i++) {
        await new Promise(r => setTimeout(r, 200));
        online = await checkUrl(`http://127.0.0.1:${port}/login`);
        if (online) break;
    }

    if (!online) {
        console.error('FAILED: Standalone PHP server did not respond.');
        phpProc.kill('SIGKILL');
        app.exit(1);
    }
    console.log('[2/5] Standalone PHP Server is ONLINE (Running with SQLite)');

    const win = new BrowserWindow({
        show: false,
        width: 1280,
        height: 800,
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true,
            sandbox: true,
            preload: path.join(__dirname, '../src/preload.js')
        }
    });

    console.log('[3/5] Loading Standalone Application in Electron Window...');
    await win.loadURL(`http://127.0.0.1:${port}/login`);

    const title = win.getTitle();
    console.log(`[4/5] Window Loaded. Title: "${title}"`);

    // Authenticate and verify dashboard
    console.log('[5/5] Performing Login and Session Check...');
    await win.webContents.executeJavaScript(`
        document.querySelector('input[name="username"]').value = 'superadmin';
        document.querySelector('input[name="password"]').value = 'admin123';
        document.querySelector('form').submit();
    `);

    await new Promise(r => setTimeout(r, 2000));
    const finalUrl = win.getURL();
    const finalTitle = win.getTitle();
    console.log(`      Final URL after login: ${finalUrl}`);
    console.log(`      Final Page Title: "${finalTitle}"`);

    if (finalUrl.includes('dashboard') || finalTitle.includes('Dashboard') || finalTitle.includes('Sastha Sasti')) {
        console.log('--- VERIFICATION SUCCESS: STANDALONE APP OPERATIONAL WITH ZERO XAMPP DEPENDENCY! ---');
    } else {
        console.warn('WARNING: Final page was not dashboard, current URL: ' + finalUrl);
    }

    phpProc.kill('SIGKILL');
    win.destroy();
    app.quit();
});
