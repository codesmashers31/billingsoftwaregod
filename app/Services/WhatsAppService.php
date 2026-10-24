<?php
namespace App\Services;

class WhatsAppService {
    /**
     * Send direct WhatsApp text message in background via API without opening WhatsApp Web.
     * Supports UltraMsg, Meta WhatsApp Cloud API, and Custom Webhook.
     *
     * @param string $mobile Customer mobile number
     * @param string $textMessage Direct text bill (no PDF link)
     * @return array
     */
    public static function sendDirectMessage(string $mobile, string $textMessage): array {
        $cleanPhone = preg_replace('/\D/', '', $mobile);
        if (empty($cleanPhone) || strlen($cleanPhone) < 10) {
            return ['success' => false, 'message' => 'Invalid phone number'];
        }

        // Format Indian numbers to 91XXXXXXXXXX
        if (strlen($cleanPhone) === 10) {
            $fullPhone = '91' . $cleanPhone;
        } elseif (strlen($cleanPhone) === 12 && str_starts_with($cleanPhone, '91')) {
            $fullPhone = $cleanPhone;
        } else {
            $fullPhone = $cleanPhone;
        }

        // 1. Meta WhatsApp Cloud API (Free Official API directly from WhatsApp / Meta - 1,000 free msgs/mo)
        $metaToken = trim((string)getSetting('whatsapp_meta_token', ''));
        $metaPhoneId = trim((string)getSetting('whatsapp_phone_number_id', ''));
        if (!empty($metaToken) && !empty($metaPhoneId)) {
            return self::sendViaMeta($metaPhoneId, $metaToken, $fullPhone, $textMessage);
        }

        // 2. Custom Webhook Gateway / Local WhatsApp Bridge (100% Free self-hosted)
        $webhookUrl = trim((string)getSetting('whatsapp_custom_webhook', ''));
        if (!empty($webhookUrl)) {
            return self::sendViaWebhook($webhookUrl, $fullPhone, $textMessage);
        }

        // 3. UltraMsg Gateway
        $instanceId = trim((string)getSetting('whatsapp_instance_id', ''));
        $token = trim((string)getSetting('whatsapp_token', ''));
        if (!empty($instanceId) && !empty($token)) {
            return self::sendViaUltraMsg($instanceId, $token, $fullPhone, $textMessage);
        }

        // Gateway not configured yet
        return [
            'success' => false,
            'configured' => false,
            'message' => 'Direct WhatsApp Gateway API not configured yet in Store Settings.'
        ];
    }

    /**
     * UltraMsg REST API Dispatch
     */
    private static function sendViaUltraMsg(string $instanceId, string $token, string $to, string $body): array {
        $url = "https://api.ultramsg.com/{$instanceId}/messages/chat";
        $params = [
            'token' => $token,
            'to' => $to,
            'body' => $body
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query($params),
            CURLOPT_HTTPHEADER => ["content-type: application/x-www-form-urlencoded"],
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return ['success' => false, 'message' => "UltraMsg cURL Error: {$err}"];
        }

        $data = json_decode($response, true);
        if (isset($data['sent']) && ($data['sent'] === 'true' || $data['sent'] === true)) {
            return ['success' => true, 'message' => 'Direct WhatsApp bill sent successfully via UltraMsg', 'data' => $data];
        }

        return ['success' => false, 'message' => $data['message'] ?? 'Failed to send via UltraMsg', 'data' => $data];
    }

    /**
     * Meta WhatsApp Cloud API Dispatch
     */
    private static function sendViaMeta(string $phoneId, string $token, string $to, string $body): array {
        $url = "https://graph.facebook.com/v19.0/{$phoneId}/messages";
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'text',
            'text' => ['preview_url' => false, 'body' => $body]
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$token}",
                "Content-Type: application/json"
            ],
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return ['success' => false, 'message' => "Meta API cURL Error: {$err}"];
        }

        $data = json_decode($response, true);
        if (isset($data['messages'][0]['id'])) {
            return ['success' => true, 'message' => 'Direct WhatsApp bill sent successfully via Meta Cloud API', 'data' => $data];
        }

        return ['success' => false, 'message' => $data['error']['message'] ?? 'Failed to send via Meta Cloud API', 'data' => $data];
    }

    /**
     * Generic Webhook Dispatch
     */
    private static function sendViaWebhook(string $url, string $to, string $body): array {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode(['to' => $to, 'message' => $body]),
            CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        return ['success' => true, 'message' => 'Dispatched to custom webhook'];
    }
}
