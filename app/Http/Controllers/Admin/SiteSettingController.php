<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSiteSettingRequest;
use App\Services\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    public function edit(SiteSettings $settings): View
    {
        return view('admin.settings.edit', [
            'settings' => $settings->all(),
        ]);
    }

    public function update(
        UpdateSiteSettingRequest $request,
        SiteSettings $settings
    ): RedirectResponse {
        $data = $request->safe()->except('logo');

        $oldLogo = $settings->get('logo');
        $newLogo = null;

        if ($request->hasFile('logo')) {
            $newLogo = $request->file('logo')
                ->store('branding', 'public');

            $data['logo'] = $newLogo;
        }

        try {
            $settings->setMany($data);
        } catch (\Throwable $e) {
            if ($newLogo) {
                Storage::disk('public')->delete($newLogo);
            }

            throw $e;
        }

        if ($newLogo && $oldLogo) {
            Storage::disk('public')->delete($oldLogo);
        }

        return redirect()
            ->route('admin.settings.edit')
            ->with('success', 'تم تحديث إعدادات الموقع بنجاح.');
    }
}
