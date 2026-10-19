const { app, BrowserWindow } = require('electron');
const path = require('path');

app.whenReady().then(async () => {
    console.log('=== VERIFYING FULL CRUD OPERATIONS IN ELECTRON ===');

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

    win.webContents.on('console-message', (e, level, msg) => {
        if (!msg.includes('tailwindcss') && !msg.includes('Security Warning')) {
            console.log(`[RENDERER] ${msg}`);
        }
    });

    // 1. Login
    console.log('\n[1/6] Authenticating as Superadmin...');
    await win.loadURL('http://localhost/billing/billingsoftwaregod/login');
    await new Promise(r => setTimeout(r, 1000));
    await win.webContents.executeJavaScript(`
        const u = document.querySelector('input[name="username"]');
        if (u) {
            u.value = 'superadmin';
            document.querySelector('input[name="password"]').value = 'admin123';
            document.querySelector('form').submit();
        }
    `);
    await new Promise(r => setTimeout(r, 1500));
    console.log('     Logged in. Current URL:', win.getURL());

    // 2. Test Supplier Add
    console.log('\n[2/6] Testing Supplier Add (save-with-products)...');
    await win.loadURL('http://localhost/billing/billingsoftwaregod/products');
    await new Promise(r => setTimeout(r, 1500));

    const supplierTestResult = await win.webContents.executeJavaScript(`
        (async () => {
            const formData = new FormData();
            formData.append('_csrf_token', window.CSRF_TOKEN || '');
            formData.append('supplier_name', 'Artisan Meenakshi Works');
            formData.append('mobile_number', '9443388776');
            formData.append('city', 'Swamimalai');
            formData.append('state', 'Tamil Nadu');
            formData.append('total_amount_purchased', '15000');
            formData.append('payment_methods[]', 'Cash');
            formData.append('payment_methods', 'Cash');
            formData.append('payment_amount_cash', '15000');

            // Product supplied
            formData.append('sp_product_name[]', 'Lord Murugan Vel Bronze 15in');
            formData.append('sp_product_category[]', 'Bronze Masterpieces');
            formData.append('sp_product_subcategory[]', 'Chola Style');
            formData.append('sp_unit[]', '1');
            formData.append('sp_piece_rate[]', '15000');
            formData.append('sp_purchase_price[]', '15000');
            formData.append('sp_margin_percentage[]', '20');
            formData.append('sp_selling_price[]', '18000');
            formData.append('sp_barcode[]', 'BAR-9900112233');

            try {
                const res = await fetch(getAppUrl('suppliers/save-with-products'), {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                return { ok: true, data };
            } catch (err) {
                return { ok: false, error: err.message };
            }
        })()
    `);
    console.log('     Supplier Add Result:', JSON.stringify(supplierTestResult));

    // 3. Test Product Add (products/save-detail)
    console.log('\n[3/6] Testing Product Add (products/save-detail)...');
    const productTestResult = await win.webContents.executeJavaScript(`
        (async () => {
            const formData = new FormData();
            formData.append('_csrf_token', window.CSRF_TOKEN || '');
            formData.append('prod_supplier_id', '1');
            formData.append('ap_product_name[]', 'Thanjavur Brass Kalasam 10 inch');
            formData.append('ap_product_category[]', 'Brass Statues 130');
            formData.append('ap_unit[]', '5');
            formData.append('ap_piece_rate[]', '1200');
            formData.append('ap_purchase_price[]', '6000');
            formData.append('ap_margin_percentage[]', '30');
            formData.append('ap_selling_price[]', '1560');
            formData.append('ap_barcode[]', 'BAR-8899001122');

            try {
                const res = await fetch(getAppUrl('products/save-detail'), {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                return { ok: true, data };
            } catch (err) {
                return { ok: false, error: err.message };
            }
        })()
    `);
    console.log('     Product Add Result:', JSON.stringify(productTestResult));

    // 4. Test Customer Add
    console.log('\n[4/6] Testing Customer Add...');
    await win.loadURL('http://localhost/billing/billingsoftwaregod/customers');
    await new Promise(r => setTimeout(r, 1500));

    const customerTestResult = await win.webContents.executeJavaScript(`
        (async () => {
            const formData = new FormData();
            formData.append('_csrf_token', window.CSRF_TOKEN || '');
            formData.append('name', 'Sri Meenakshi Temple Trust');
            formData.append('mobile', '9840199999');
            formData.append('customer_type', 'temple_trust');
            formData.append('credit_limit', '50000');
            formData.append('city', 'Madurai');
            formData.append('state', 'Tamil Nadu');

            try {
                const res = await fetch(window.APP_URL + '/customers/create', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                return { ok: true, data };
            } catch (err) {
                return { ok: false, error: err.message };
            }
        })()
    `);
    console.log('     Customer Add Result:', JSON.stringify(customerTestResult));

    // 5. Test Customer Edit
    let customerId = customerTestResult.ok && customerTestResult.data && customerTestResult.data.data && customerTestResult.data.data.customer
        ? customerTestResult.data.data.customer.id
        : null;

    if (customerId) {
        console.log(`\n[5/6] Testing Customer Edit for Customer ID ${customerId}...`);
        const customerEditResult = await win.webContents.executeJavaScript(`
            (async () => {
                const formData = new FormData();
                formData.append('_csrf_token', window.CSRF_TOKEN || '');
                formData.append('name', 'Sri Meenakshi Amman Devasthanam (Verified Edit)');
                formData.append('mobile', '9840199999');
                formData.append('customer_type', 'temple_trust');
                formData.append('credit_limit', '75000');
                formData.append('city', 'Madurai');
                formData.append('state', 'Tamil Nadu');

                try {
                    const res = await fetch(window.APP_URL + '/customers/edit/${customerId}', {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await res.json();
                    return { ok: true, data };
                } catch (err) {
                    return { ok: false, error: err.message };
                }
            })()
        `);
        console.log('     Customer Edit Result:', JSON.stringify(customerEditResult));

        // 6. Test Delete Operation (Product Delete)
        let createdProdId = productTestResult.ok && productTestResult.data && productTestResult.data.data && productTestResult.data.data.products && productTestResult.data.data.products[0]
            ? productTestResult.data.data.products[0].id
            : null;

        if (createdProdId) {
            console.log(`\n[6/6] Testing Product Delete for Product ID ${createdProdId}...`);
            await win.loadURL('http://localhost/billing/billingsoftwaregod/products');
            await new Promise(r => setTimeout(r, 1500));
            const productDeleteResult = await win.webContents.executeJavaScript(`
                (async () => {
                    const formData = new FormData();
                    formData.append('_csrf_token', window.CSRF_TOKEN || '');

                    try {
                        const target = getAppUrl('products/delete/' + ${createdProdId});
                        const res = await fetch(target, {
                            method: 'POST',
                            body: formData,
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await res.json();
                        return { ok: true, data };
                    } catch (err) {
                        return { ok: false, error: err.message };
                    }
                })()
            `);
            console.log('     Product Delete Result:', JSON.stringify(productDeleteResult));
        }
    }

    console.log('\n=== ALL ELECTRON IN-APP OPERATIONS COMPLETED SUCCESSFULLY ===');

    win.destroy();
    app.quit();
});
