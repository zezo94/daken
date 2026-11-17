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
        $data = $request->safe()->except([
            'logo',
            'favicon',
            'reset_favicon',
        ]);

        $oldLogo = $settings->get('logo');
        $oldFavicon = $settings->get('favicon');

        $newLogo = null;
        $newFavicon = null;

        try {
            if ($request->hasFile('logo')) {
                $newLogo = $request->file('logo')
                    ->store('branding', 'public');

                if ($newLogo === false) {
                    throw new \RuntimeException('Logo upload failed.');
                }

                $data['logo'] = $newLogo;
            }

            if ($request->hasFile('favicon')) {
                $newFavicon = $request->file('favicon')
                    ->store('branding/favicons', 'public');

                if ($newFavicon === false) {
                    throw new \RuntimeException('Favicon upload failed.');
                }

                $data['favicon'] = $newFavicon;
            } elseif ($request->boolean('reset_favicon')) {
                $data['favicon'] = null;
            }

            $settings->setMany($data);
        } catch (\Throwable $e) {
            if ($newLogo) {
                Storage::disk('public')->delete($newLogo);
            }

            if ($newFavicon) {
                Storage::disk('public')->delete($newFavicon);
            }

            throw $e;
        }

        if (
            $newLogo
            && $oldLogo
            && $oldLogo !== $newLogo
            && str_starts_with($oldLogo, 'branding/')
        ) {
            Storage::disk('public')->delete($oldLogo);
        }

        if (
            array_key_exists('favicon', $data)
            && $oldFavicon
            && $oldFavicon !== $newFavicon
            && str_starts_with($oldFavicon, 'branding/favicons/')
        ) {
            Storage::disk('public')->delete($oldFavicon);
        }

        return redirect()
            ->route('admin.settings.edit')
            ->with('success', 'Site settings updated successfully.');
    }

}
