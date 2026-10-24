/**
 * 100% Free Local WhatsApp Bridge for Billing ERP
 * 
 * Runs on http://localhost:3000
 * Receives bill dispatch requests from PHP Billing ERP and sends
 * direct WhatsApp messages from your shop's own phone number.
 */

const http = require('http');
const { default: makeWASocket, useMultiFileAuthState, DisconnectReason } = require('@whiskeysockets/baileys');
const qrcode = require('qrcode-terminal');
const pino = require('pino');

const PORT = 3000;
let sock = null;
let isConnected = false;

async function startWhatsApp() {
    const { state, saveCreds } = await useMultiFileAuthState('./auth_info_baileys');

    sock = makeWASocket({
        auth: state,
        printQRInTerminal: false,
        logger: pino({ level: 'silent' })
    });

    sock.ev.on('connection.update', (update) => {
        const { connection, lastDisconnect, qr } = update;

        if (qr) {
            console.log('\n======================================================');
            console.log('📱 SCAN THIS QR CODE WITH YOUR SHOP WHATSAPP:');
            console.log('(Open WhatsApp > Linked Devices > Link a Device)');
            console.log('======================================================\n');
            qrcode.generate(qr, { small: true });
        }

        if (connection === 'close') {
            isConnected = false;
            const shouldReconnect = (lastDisconnect?.error)?.output?.statusCode !== DisconnectReason.loggedOut;
            console.log('WhatsApp connection closed. Reconnecting...', shouldReconnect);
            if (shouldReconnect) {
                startWhatsApp();
            } else {
                console.log('Logged out. Please delete auth_info_baileys and restart.');
            }
        } else if (connection === 'open') {
            isConnected = true;
            console.log('\n======================================================');
            console.log('✅ WHATSAPP CONNECTED TO YOUR SHOP PHONE SUCCESSFULLY!');
            console.log('Ready to dispatch bills from ERP on http://localhost:' + PORT);
            console.log('======================================================\n');
        }
    });

    sock.ev.on('creds.update', saveCreds);
}

// Start WhatsApp socket
startWhatsApp().catch(err => console.error('Failed to start WhatsApp socket:', err));

// HTTP Dispatch Server for Billing ERP
const server = http.createServer(async (req, res) => {
    // Enable CORS
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'POST, GET, OPTIONS');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

    if (req.method === 'OPTIONS') {
        res.writeHead(200);
        res.end();
        return;
    }

    if (req.method === 'GET' && req.url === '/status') {
        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({
            status: 'running',
            connected: isConnected,
            message: isConnected ? 'WhatsApp is ready' : 'Waiting for WhatsApp QR scan'
        }));
        return;
    }

    if (req.method === 'POST' && (req.url === '/send' || req.url === '/')) {
        let body = '';
        req.on('data', chunk => { body += chunk.toString(); });
        req.on('end', async () => {
            try {
                const data = JSON.parse(body);
                const to = data.to;
                const message = data.message || data.text;

                if (!to || !message) {
                    res.writeHead(400, { 'Content-Type': 'application/json' });
                    res.end(JSON.stringify({ success: false, message: 'Missing "to" or "message" parameter' }));
                    return;
                }

                if (!sock || !isConnected) {
                    res.writeHead(503, { 'Content-Type': 'application/json' });
                    res.end(JSON.stringify({ 
                        success: false, 
                        message: 'Shop WhatsApp is not connected. Please scan the QR code in the terminal.' 
                    }));
                    return;
                }

                // Format number to JID: 919876543210@s.whatsapp.net
                let cleanNumber = to.toString().replace(/\D/g, '');
                if (cleanNumber.length === 10) cleanNumber = '91' + cleanNumber;
                const jid = `${cleanNumber}@s.whatsapp.net`;

                await sock.sendMessage(jid, { text: message });

                console.log(`[✓] Bill sent to +${cleanNumber}`);
                res.writeHead(200, { 'Content-Type': 'application/json' });
                res.end(JSON.stringify({ success: true, message: `Bill sent to +${cleanNumber} successfully` }));
            } catch (err) {
                console.error('[!] Error sending WhatsApp message:', err);
                res.writeHead(500, { 'Content-Type': 'application/json' });
                res.end(JSON.stringify({ success: false, message: err.message }));
            }
        });
        return;
    }

    res.writeHead(404, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify({ error: 'Endpoint not found' }));
});

server.listen(PORT, () => {
    console.log(`Local WhatsApp Bridge listening on http://localhost:${PORT}`);
});
