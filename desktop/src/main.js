const { app, BrowserWindow, Menu, shell, ipcMain, dialog } = require('electron');
const path = require('path');
const fs = require('fs');
const http = require('http');
const https = require('https');
const net = require('net');
const { spawn } = require('child_process');

let mainWindow = null;
let phpServerProcess = null;
let activeAppUrl = null;

const APP_NAME = 'God Statue Billing';

// Find a free ephemeral TCP port on loopback
function getAvailablePort() {
    return new Promise((resolve, reject) => {
        const server = net.createServer();
        server.unref();
        server.on('error', reject);
        server.listen(0, '127.0.0.1', () => {
            const port = server.address().port;
            server.close(() => resolve(port));
        });
    });
}

// Locate compatible PHP executable
function locatePhpBinary() {
    // 1. Packaged resource binary (for macOS standalone bundle)
    if (app.isPackaged) {
        const macArchBundled = path.join(process.resourcesPath, 'bin', 'mac', process.arch, 'php');
        if (fs.existsSync(macArchBundled)) return macArchBundled;

        const macBundled = path.join(process.resourcesPath, 'bin', 'mac', 'php');
        if (fs.existsSync(macBundled)) return macBundled;

        const winBundled = path.join(process.resourcesPath, 'bin', 'win', 'php.exe');
        if (fs.existsSync(winBundled)) return winBundled;
    }

    // 2. Local desktop/bin directory (macOS / Windows dev)
    const localMacArchBin = path.join(__dirname, '../bin/mac', process.arch, 'php');
    if (fs.existsSync(localMacArchBin)) return localMacArchBin;

    const localMacBin = path.join(__dirname, '../bin/mac/php');
    if (fs.existsSync(localMacBin)) return localMacBin;

    const localWinBin = path.join(__dirname, '../bin/win/php.exe');
    if (fs.existsSync(localWinBin)) return localWinBin;

    // 3. Common system installations (XAMPP on Windows or /usr/bin/php on Mac)
    if (process.platform === 'win32' && fs.existsSync('C:\\xampp\\php\\php.exe')) {
        return 'C:\\xampp\\php\\php.exe';
    }

    if (process.platform === 'darwin') {
        const macLocations = [
            '/opt/homebrew/bin/php',
            '/usr/local/bin/php',
            '/usr/bin/php'
        ];
        for (const loc of macLocations) {
            if (fs.existsSync(loc)) return loc;
        }
    }

    return null;
}

// Prepare customer data directories in ~/Library/Application Support/GodStatueBilling
function initializeUserData() {
    const userData = app.getPath('userData');
    const dirs = {
        database: path.join(userData, 'database'),
        uploads: path.join(userData, 'uploads'),
        invoices: path.join(userData, 'invoices'),
        backups: path.join(userData, 'backups')
    };

    for (const dir of Object.values(dirs)) {
        if (!fs.existsSync(dir)) {
            fs.mkdirSync(dir, { recursive: true });
        }
    }

    const liveDbPath = path.join(dirs.database, 'billing.sqlite');
    // Seed initial SQLite database if not yet present
    if (!fs.existsSync(liveDbPath)) {
        const templateDbPath = app.isPackaged
            ? path.join(process.resourcesPath, 'database', 'billing.sqlite')
            : path.join(__dirname, '../database/billing.sqlite');

        if (fs.existsSync(templateDbPath)) {
            try {
                fs.copyFileSync(templateDbPath, liveDbPath);
                console.log('Seeded customer database from template:', liveDbPath);
            } catch (e) {
                console.error('Failed to copy initial database:', e);
            }
        }
    }

    return {
        userData,
        dbPath: liveDbPath,
        uploadsDir: dirs.uploads,
        backupsDir: dirs.backups
    };
}

// Start local loopback PHP server
async function startStandalonePhpServer() {
    const phpBinary = locatePhpBinary();
    if (!phpBinary) {
        console.warn('No bundled or system PHP binary found, checking fallback...');
        return null;
    }

    const port = await getAvailablePort();
    const storage = initializeUserData();

    const rootDir = app.isPackaged
        ? path.join(process.resourcesPath, 'backend')
        : path.join(__dirname, '../..');

    const publicDir = path.join(rootDir, 'public');
    const routerScript = app.isPackaged
        ? path.join(process.resourcesPath, 'backend', 'server', 'router.php')
        : path.join(__dirname, '../server/router.php');

    console.log(`Starting standalone PHP server on 127.0.0.1:${port}`);
    console.log(`Using PHP binary: ${phpBinary}`);
    console.log(`Database: ${storage.dbPath}`);

    const env = {
        ...process.env,
        APP_ENV: 'production',
        APP_URL: `http://127.0.0.1:${port}`,
        DB_CONNECTION: 'sqlite',
        DB_SQLITE_PATH: storage.dbPath,
        UPLOAD_DIR: storage.uploadsDir,
        STORAGE_DIR: path.join(storage.userData, 'storage'),
        PUBLIC_DIR: publicDir
    };

    // On macOS ensure binary is executable
    if (process.platform === 'darwin' && fs.existsSync(phpBinary)) {
        try {
            fs.chmodSync(phpBinary, 0o755);
        } catch (e) {}
    }

    phpServerProcess = spawn(phpBinary, [
        '-S', `127.0.0.1:${port}`,
        '-t', publicDir,
        routerScript
    ], {
        env,
        stdio: ['ignore', 'pipe', 'pipe']
    });

    phpServerProcess.stdout.on('data', (data) => {
        // console.log(`[PHP STDOUT] ${data}`);
    });

    phpServerProcess.stderr.on('data', (data) => {
        // console.log(`[PHP STDERR] ${data}`);
    });

    phpServerProcess.on('exit', (code, signal) => {
        console.log(`PHP server process exited with code ${code}, signal ${signal}`);
        phpServerProcess = null;
    });

    // Wait until server is accepting HTTP connections
    const targetUrl = `http://127.0.0.1:${port}/`;
    let isUp = false;
    for (let i = 0; i < 25; i++) {
        await new Promise(r => setTimeout(r, 200));
        isUp = await checkServerHealth(targetUrl);
        if (isUp) break;
    }

    if (isUp) {
        console.log('Standalone PHP server is online and responding!');
        return targetUrl;
    }

    console.error('PHP server failed to become responsive within timeout.');
    return null;
}

// Health check
function checkServerHealth(targetUrl) {
    return new Promise((resolve) => {
        try {
            const parsed = new URL(targetUrl);
            const client = parsed.protocol === 'https:' ? https : http;
            const req = client.request({
                hostname: parsed.hostname,
                port: parsed.port || (parsed.protocol === 'https:' ? 443 : 80),
                path: parsed.pathname || '/',
                method: 'GET',
                timeout: 3000
            }, (res) => {
                resolve(true);
            });

            req.on('error', () => resolve(false));
            req.on('timeout', () => {
                req.destroy();
                resolve(false);
            });
            req.end();
        } catch (e) {
            resolve(false);
        }
    });
}

function stopPhpServer() {
    if (phpServerProcess) {
        console.log('Terminating PHP server process...');
        phpServerProcess.kill('SIGTERM');
        setTimeout(() => {
            if (phpServerProcess) {
                phpServerProcess.kill('SIGKILL');
            }
        }, 1500);
    }
}

async function createMainWindow() {
    const iconPath = process.platform === 'win32'
        ? path.join(__dirname, '../assets/icon.ico')
        : path.join(__dirname, '../assets/icon.png');

    mainWindow = new BrowserWindow({
        title: APP_NAME,
        width: 1280,
        height: 850,
        minWidth: 1024,
        minHeight: 700,
        icon: fs.existsSync(iconPath) ? iconPath : undefined,
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true,
            sandbox: true,
            preload: path.join(__dirname, 'preload.js')
        },
        backgroundColor: '#0f172a',
        show: false
    });

    mainWindow.setTitle(APP_NAME);
    setupMenu();

    // 1. Try launching standalone embedded server first
    try {
        const standaloneUrl = await startStandalonePhpServer();
        if (standaloneUrl) {
            activeAppUrl = standaloneUrl;
        }
    } catch (err) {
        console.warn('Standalone server launch error:', err);
    }

    // 2. Fallback to local development XAMPP if standalone not running
    if (!activeAppUrl) {
        activeAppUrl = 'http://localhost/billing/billingsoftwaregod/';
    }

    loadApplication();

    mainWindow.once('ready-to-show', () => {
        mainWindow.show();
    });

    mainWindow.webContents.on('did-fail-load', (event, errorCode, errorDescription, validatedURL) => {
        console.warn(`Page load failed [${errorCode}]: ${errorDescription}`);
        if (errorCode === -102 || errorCode === -105 || errorCode === -109) {
            loadErrorPage();
        }
    });

    mainWindow.webContents.setWindowOpenHandler(({ url }) => {
        if (url.startsWith('http://127.0.0.1') || url.startsWith('http://localhost')) {
            return {
                action: 'allow',
                overrideBrowserWindowOptions: {
                    width: 900,
                    height: 700,
                    autoHideMenuBar: true,
                    webPreferences: {
                        nodeIntegration: false,
                        contextIsolation: true,
                        sandbox: true
                    }
                }
            };
        }
        shell.openExternal(url);
        return { action: 'deny' };
    });

    mainWindow.on('closed', () => {
        mainWindow = null;
    });
}

async function loadApplication() {
    if (!mainWindow || !activeAppUrl) return;

    const isHealthy = await checkServerHealth(activeAppUrl);
    if (isHealthy) {
        mainWindow.loadURL(activeAppUrl);
    } else {
        loadErrorPage();
    }
}

function loadErrorPage() {
    if (!mainWindow) return;
    const errorHtmlPath = path.join(__dirname, 'views/error.html');
    mainWindow.loadFile(errorHtmlPath);
}

function setupMenu() {
    const isMac = process.platform === 'darwin';
    const template = [
        ...(isMac ? [{
            label: app.name,
            submenu: [
                { role: 'about' },
                { type: 'separator' },
                { role: 'services' },
                { type: 'separator' },
                { role: 'hide' },
                { role: 'hideOthers' },
                { role: 'unhide' },
                { type: 'separator' },
                { role: 'quit' }
            ]
        }] : []),
        {
            label: 'File',
            submenu: [
                {
                    label: 'Print Invoice / Page',
                    accelerator: 'CmdOrCtrl+P',
                    click: () => {
                        if (mainWindow) mainWindow.webContents.print();
                    }
                },
                {
                    label: 'Reload Application',
                    accelerator: 'CmdOrCtrl+R',
                    click: () => loadApplication()
                },
                { type: 'separator' },
                isMac ? { role: 'close' } : { role: 'quit' }
            ]
        },
        {
            label: 'Edit',
            submenu: [
                { role: 'undo' },
                { role: 'redo' },
                { type: 'separator' },
                { role: 'cut' },
                { role: 'copy' },
                { role: 'paste' },
                { role: 'selectAll' }
            ]
        },
        {
            label: 'View',
            submenu: [
                { role: 'resetZoom' },
                { role: 'zoomIn' },
                { role: 'zoomOut' },
                { type: 'separator' },
                { role: 'togglefullscreen' },
                { type: 'separator' },
                {
                    label: 'Developer Tools',
                    accelerator: 'Ctrl+Shift+I',
                    click: () => {
                        if (mainWindow) mainWindow.webContents.toggleDevTools();
                    }
                }
            ]
        },
        {
            label: 'Billing Tools',
            submenu: [
                {
                    label: 'New Sale / Billing POS',
                    click: () => {
                        if (mainWindow && activeAppUrl) mainWindow.loadURL(activeAppUrl + 'billing');
                    }
                },
                {
                    label: 'Product Catalog',
                    click: () => {
                        if (mainWindow && activeAppUrl) mainWindow.loadURL(activeAppUrl + 'products');
                    }
                },
                {
                    label: 'Customer Directory',
                    click: () => {
                        if (mainWindow && activeAppUrl) mainWindow.loadURL(activeAppUrl + 'customers');
                    }
                },
                {
                    label: 'Dashboard Overview',
                    click: () => {
                        if (mainWindow && activeAppUrl) mainWindow.loadURL(activeAppUrl + 'dashboard');
                    }
                }
            ]
        },
        {
            label: 'Help',
            submenu: [
                {
                    label: 'About God Statue Billing',
                    click: () => {
                        dialog.showMessageBox(mainWindow, {
                            type: 'info',
                            title: 'About God Statue Billing',
                            message: 'Sastha Sasti - God Statue Billing ERP',
                            detail: 'Version: 1.0.0 (Standalone Desktop)\nPlatform: ' + process.platform + ' (' + process.arch + ')',
                            buttons: ['OK']
                        });
                    }
                }
            ]
        }
    ];

    const menu = Menu.buildFromTemplate(template);
    Menu.setApplicationMenu(menu);
}

// IPC Handlers
ipcMain.on('app:retry-connection', async () => {
    loadApplication();
});

ipcMain.handle('app:check-health', async () => {
    return await checkServerHealth(activeAppUrl);
});

ipcMain.on('app:print', (event, options = {}) => {
    if (mainWindow) {
        mainWindow.webContents.print(options, (success, failureReason) => {
            if (!success) console.log('Print canceled or failed:', failureReason);
        });
    }
});

ipcMain.on('app:open-external', (event, url) => {
    if (url && (url.startsWith('http://') || url.startsWith('https://'))) {
        shell.openExternal(url);
    }
});

// Single instance lock
const gotTheLock = app.requestSingleInstanceLock();
if (!gotTheLock) {
    app.quit();
} else {
    app.on('second-instance', () => {
        if (mainWindow) {
            if (mainWindow.isMinimized()) mainWindow.restore();
            mainWindow.focus();
        }
    });

    app.whenReady().then(() => {
        createMainWindow();

        app.on('activate', () => {
            if (BrowserWindow.getAllWindows().length === 0) {
                createMainWindow();
            }
        });
    });
}

app.on('before-quit', () => {
    stopPhpServer();
});

app.on('window-all-closed', () => {
    stopPhpServer();
    if (process.platform !== 'darwin') {
        app.quit();
    }
});
