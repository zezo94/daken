<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateHomepageRequest;
use App\Services\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HomepageController extends Controller
{
    private const SECTIONS = [
        'categories',
        'featured',
        'about',
        'locations',
        'gallery',
        'contact',
    ];

    public function edit(SiteSettings $settings): View
    {
        return view('admin.homepage.edit', [
            'settings' => $settings->all(),
            'sections' => self::SECTIONS,
        ]);
    }

    public function update(
        UpdateHomepageRequest $request,
        SiteSettings $settings
    ): RedirectResponse {
        /*
        |--------------------------------------------------------------------------
        | Existing Hero fields
        |--------------------------------------------------------------------------
        */

        $data = $request->safe()->except('hero_image');

        /*
        |--------------------------------------------------------------------------
        | Validate section visibility and order
        |--------------------------------------------------------------------------
        */

        $rules = [];

        foreach (self::SECTIONS as $section) {
            $rules['home_show_' . $section] = [
                'required',
                'boolean',
            ];

            $rules['home_order_' . $section] = [
                'required',
                'integer',
                'min:1',
                'max:99',
            ];
        }

        $sectionData = $request->validate($rules);

        foreach (self::SECTIONS as $section) {
            $data['home_show_' . $section] =
                (string) (int) $sectionData['home_show_' . $section];

            $data['home_order_' . $section] =
                (string) (int) $sectionData['home_order_' . $section];
        }

        /*
        |--------------------------------------------------------------------------
        | Hero image
        |--------------------------------------------------------------------------
        */

        $oldImage = $settings->get('hero_image');
        $newImage = null;

        if ($request->hasFile('hero_image')) {
            $newImage = $request->file('hero_image')
                ->store('homepage', 'public');

            $data['hero_image'] = $newImage;
        }

        /*
        |--------------------------------------------------------------------------
        | Save settings
        |--------------------------------------------------------------------------
        */

        try {
            $settings->setMany($data);
        } catch (\Throwable $e) {
            if ($newImage) {
                Storage::disk('public')->delete($newImage);
            }

            throw $e;
        }

        /*
        |--------------------------------------------------------------------------
        | Delete replaced Hero image
        |--------------------------------------------------------------------------
        */

        if (
            $newImage &&
            $oldImage &&
            $oldImage !== $newImage &&
            str_starts_with($oldImage, 'homepage/')
        ) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()
            ->route('admin.homepage.edit')
            ->with(
                'success',
                'تم حفظ إعدادات الصفحة الرئيسية وترتيب الأقسام بنجاح.'
            );
    }
}
