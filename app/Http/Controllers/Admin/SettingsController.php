<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'company_name'    => config('beauty-crm.company_name', 'BeautyCRM'),
            'company_email'   => config('beauty-crm.company_email', ''),
            'company_phone'   => config('beauty-crm.company_phone', ''),
            'company_address' => config('beauty-crm.company_address', ''),
            'notify_new_lead' => config('beauty-crm.notify_new_lead', true),
            'notify_won_deal' => config('beauty-crm.notify_won_deal', true),
        ];
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name'    => ['required', 'string', 'max:255'],
            'company_email'   => ['nullable', 'email'],
            'company_phone'   => ['nullable', 'string', 'max:20'],
            'company_address' => ['nullable', 'string'],
            'notify_new_lead' => ['boolean'],
            'notify_won_deal' => ['boolean'],
        ]);

        // Write to .env
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        $map = [
            'COMPANY_NAME'    => $validated['company_name'],
            'COMPANY_EMAIL'   => $validated['company_email'] ?? '',
            'COMPANY_PHONE'   => $validated['company_phone'] ?? '',
            'COMPANY_ADDRESS' => $validated['company_address'] ?? '',
            'NOTIFY_NEW_LEAD' => $request->boolean('notify_new_lead') ? 'true' : 'false',
            'NOTIFY_WON_DEAL' => $request->boolean('notify_won_deal') ? 'true' : 'false',
        ];

        foreach ($map as $key => $value) {
            $value = addslashes($value);
            if (preg_match("/^{$key}=.*/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}=\"{$value}\"", $envContent);
            } else {
                $envContent .= "\n{$key}=\"{$value}\"";
            }
        }

        file_put_contents($envPath, $envContent);
        Artisan::call('config:clear');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }
}
