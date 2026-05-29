<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
    ) {
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'settings' => ['required', 'array'],
            'settings.*.key' => ['required', 'string'],
            'settings.*.value' => ['nullable', 'string'],
        ]);

        foreach ($data['settings'] as $item) {
            Setting::updateOrCreate(
                ['key' => $item['key']],
                ['value' => $item['value']]
            );
        }

        $this->auditLogService->log(
            $request->user()?->id,
            'settings.updated',
            'setting',
            null,
            $data['settings'],
            $request
        );

        return back()->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }

    public function updateContent(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'settings' => ['required', 'array'],
            'settings.*.key' => ['required', 'string', 'in:shop_address,shop_phone,shop_email,shop_hours,landing_hero_title,landing_hero_subtitle,about_story_timeline'],
            'settings.*.value' => ['nullable'],
        ]);

        foreach ($data['settings'] as $item) {
            Setting::updateOrCreate(
                ['key' => $item['key']],
                ['value' => is_array($item['value']) ? json_encode($item['value']) : $item['value']]
            );
        }

        $this->auditLogService->log(
            $request->user()?->id,
            'web_content.updated',
            'setting',
            null,
            $data['settings'],
            $request
        );

        return back()->with('success', 'Konten website berhasil diperbarui.');
    }

}
