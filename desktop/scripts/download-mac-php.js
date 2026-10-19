const https = require('https');
const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const MAC_BIN_DIR = path.join(__dirname, '../bin/mac');

const DOWNLOADS = [
    {
        arch: 'arm64',
        url: 'https://raw.githubusercontent.com/NativePHP/php-bin/main/bin/mac/arm64/php-8.3.zip'
    },
    {
        arch: 'x64',
        url: 'https://raw.githubusercontent.com/NativePHP/php-bin/main/bin/mac/x64/php-8.3.zip'
    }
];

function downloadFile(url, dest) {
    return new Promise((resolve, reject) => {
        const file = fs.createWriteStream(dest);
        https.get(url, (response) => {
            if (response.statusCode >= 300 && response.statusCode < 400 && response.headers.location) {
                file.close();
                return downloadFile(response.headers.location, dest).then(resolve).catch(reject);
            }
            if (response.statusCode !== 200) {
                file.close();
                return reject(new Error(`Download failed with HTTP ${response.statusCode}`));
            }
            response.pipe(file);
            file.on('finish', () => {
                file.close(resolve);
            });
        }).on('error', (err) => {
            fs.unlink(dest, () => {});
            reject(err);
        });
    });
}

async function setup() {
    console.log('=== PREPARING STANDALONE MACOS PHP RUNTIMES ===');

    for (const item of DOWNLOADS) {
        const targetDir = path.join(MAC_BIN_DIR, item.arch);
        if (!fs.existsSync(targetDir)) {
            fs.mkdirSync(targetDir, { recursive: true });
        }

        const zipPath = path.join(targetDir, 'php-8.3.zip');
        const phpBinary = path.join(targetDir, 'php');

        if (!fs.existsSync(phpBinary)) {
            console.log(`Downloading macOS PHP 8.3 (${item.arch})...`);
            await downloadFile(item.url, zipPath);
            console.log(`Extracting ${item.arch} runtime...`);

            try {
                // Use tar or powershell Expand-Archive on Windows
                if (process.platform === 'win32') {
                    execSync(`tar -xf "${zipPath}" -C "${targetDir}"`);
                } else {
                    execSync(`unzip -o "${zipPath}" -d "${targetDir}"`);
                    execSync(`chmod +x "${phpBinary}"`);
                }
                console.log(`Successfully extracted PHP for ${item.arch}!`);
            } catch (err) {
                console.error(`Extraction failed:`, err.message);
            }
        } else {
            console.log(`macOS PHP (${item.arch}) already present at: ${phpBinary}`);
        }
    }

    // Ensure default fallback binary in desktop/bin/mac/php (symlink or copy arm64)
    const defaultPhp = path.join(MAC_BIN_DIR, 'php');
    const arm64Php = path.join(MAC_BIN_DIR, 'arm64', 'php');
    if (!fs.existsSync(defaultPhp) && fs.existsSync(arm64Php)) {
        fs.copyFileSync(arm64Php, defaultPhp);
    }

    console.log('=== MACOS PHP RUNTIMES READY ===');
}

setup().catch(console.error);
