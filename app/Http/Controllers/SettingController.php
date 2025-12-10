<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        // Ambil semua setting dan ubah jadi array biar mudah dipanggil di View
        // Hasilnya: ['waha_url' => '...', 'admin_wa' => '...']
        $settings = Setting::pluck('value', 'key')->toArray();

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'waha_url' => 'required|url', // Wajib pakai http:// atau https://
            'waha_api_key' => 'nullable|string',
            'waha_session' => 'required|string',
            'waha_group_id' => 'required|string',
            'admin_wa' => 'required|numeric',
            'wa_notification_trx' => 'required|in:1,0',
        ]);

        // 2. Daftar Key yang diizinkan disimpan
        $keys = [
            'waha_url', 
            'waha_api_key', 
            'waha_session', 
            'waha_group_id', 
            'admin_wa',
            'wa_notification_trx',
        ];

        // 3. Loop dan Simpan (Pakai updateOrCreate)
        foreach ($keys as $key) {
            Setting::updateOrCreate(
                ['key' => $key], // Cari berdasarkan key
                ['value' => $request->input($key)] // Update value-nya
            );
        }

        return redirect()->back()->with('success', 'Konfigurasi WAHA berhasil disimpan!');
    }
}