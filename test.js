const jsdom = require('jsdom');
const { JSDOM } = jsdom;

const html = \
<label>
    <input type="radio" name="payment_method_radio" value="cash" checked class="hidden">
    Cash
</label>
<label>
    <input type="radio" name="payment_method_radio" value="upi" class="hidden">
    UPI
</label>

<div id="dynamic-upi-container" class="hidden mt-4">QR CONTAINER</div>
<button type="button" id="btn-show-qr" class="hidden">Show QR</button>
<button type="button" id="btn-done-qr" class="hidden">Done</button>
<button type="button" id="btn-confirm-checkout" class="gold-btn">Complete</button>
\;

const dom = new JSDOM(html);
const document = dom.window.document;

function run() {
    const radios = document.querySelectorAll('input[name="payment_method_radio"]');
    radios.forEach(r => {
        if (!r.dataset.hasListener) {
            r.addEventListener('change', (e) => {
                const upiContainer = document.getElementById('dynamic-upi-container');
                const showQrBtn = document.getElementById('btn-show-qr');
                const doneQrBtn = document.getElementById('btn-done-qr');
                const confirmBtn = document.getElementById('btn-confirm-checkout');
                
                if(upiContainer && showQrBtn && doneQrBtn && confirmBtn) {
                    if (e.target.value === 'upi') {
                        showQrBtn.classList.remove('hidden');
                        showQrBtn.classList.add('flex');
                        confirmBtn.classList.add('hidden');
                        confirmBtn.classList.remove('flex');
                        doneQrBtn.classList.add('hidden');
                        doneQrBtn.classList.remove('flex');
                        upiContainer.classList.add('hidden');
                        upiContainer.classList.remove('flex');
                    }
                }
            });
            r.dataset.hasListener = 'true';
        }
    });
}

run();

const upiInput = document.querySelector('input[value="upi"]');
upiInput.checked = true;
upiInput.dispatchEvent(new dom.window.Event('change'));

console.log('showQrBtn:', document.getElementById('btn-show-qr').className);
console.log('confirmBtn:', document.getElementById('btn-confirm-checkout').className);

