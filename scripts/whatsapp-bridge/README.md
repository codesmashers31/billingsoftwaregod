# 100% Free Local WhatsApp Bridge (Direct from Shop Phone)

This script lets your Billing ERP send WhatsApp bills directly from your **own shop's WhatsApp mobile number** for **100% FREE forever** without paying any third-party gateway or subscription.

### How to use:

1. Open PowerShell or Terminal in this folder:
   ```bash
   cd C:\xampp\htdocs\billing\billingsoftwaregod\scripts\whatsapp-bridge
   npm install
   npm start
   ```

2. A QR Code will appear in the terminal.
3. Open WhatsApp on your shop phone:
   - Go to **Linked Devices** -> **Link a Device**
   - Scan the QR code.
4. Done! Your terminal will show:
   `✅ WHATSAPP CONNECTED TO YOUR SHOP PHONE SUCCESSFULLY!`
5. In Store Settings (`http://localhost/billing/billingsoftwaregod/settings`), ensure the Local Bridge URL is:
   `http://localhost:3000/send`

Every time you complete a bill, it will silently send from your shop's WhatsApp to the customer without opening any WhatsApp Web tab!
