<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WaService
{
    /**
     * Kirim Pesan ke Grup Toko (ID diambil dari Setting)
     */
    public static function sendGroupMessage($message)
    {
        // 1. Ambil Konfigurasi dari Database
        $baseUrl = Setting::get('waha_url'); // http://localhost:3000
        $session = Setting::get('waha_session', 'default');
        $groupId = Setting::get('waha_group_id'); // ID Grup: xxxx@g.us
        $apiKey  = Setting::get('waha_api_key');

        // Cek kelengkapan setting
        if (!$baseUrl || !$groupId) {
            Log::warning('WAHA: Gagal kirim pesan. URL atau Group ID belum disetting.');
            return false;
        }

        // 2. Kirim Request ke WAHA
        try {
            $endpoint = $baseUrl . '/api/sendText';
            
            $payload = [
                'session' => $session,
                'chatId' => $groupId,
                'text' => $message,
            ];

            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ];

            // Jika ada API Key, tambahkan ke header
            if ($apiKey) {
                $headers['X-Api-Key'] = $apiKey;
            }

            $response = Http::withHeaders($headers)->post($endpoint, $payload);

            if ($response->successful()) {
                return true;
            } else {
                Log::error('WAHA Error: ' . $response->body());
                return false;
            }

        } catch (\Exception $e) {
            Log::error('WAHA Exception: ' . $e->getMessage());
            return false;
        }
    }
}