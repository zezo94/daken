<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavigationSetting;
use App\Models\SitePage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NavigationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Show navigation settings
    |--------------------------------------------------------------------------
    */

    public function edit(): View
    {
        $items = $this->availableItems();

        $settings = NavigationSetting::query()
            ->get()
            ->keyBy('key');

        $logoDesktop = (int) (
            $settings->get('logo_desktop_width')?->sort_order
            ?? 125
        );

        $logoMobile = (int) (
            $settings->get('logo_mobile_width')?->sort_order
            ?? 105
        );

        return view('admin.navigation.edit', compact(
            'items',
            'settings',
            'logoDesktop',
            'logoMobile'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Save navigation settings
    |--------------------------------------------------------------------------
    */

    public function update(Request $request): RedirectResponse
    {
        $available = $this->availableItems();

        $allowedKeys = array_column(
            $available,
            'key'
        );

        $validated = $request->validate([
            'items' => [
                'required',
                'array',
            ],

            'items.*.placement' => [
                'required',
                Rule::in([
                    'main',
                    'more',
                    'hidden',
                ]),
            ],

            'items.*.sort_order' => [
                'required',
                'integer',
                'min:0',
                'max:9999',
            ],

            'logo_desktop_width' => [
                'required',
                'integer',
                'min:70',
                'max:250',
            ],

            'logo_mobile_width' => [
                'required',
                'integer',
                'min:60',
                'max:180',
            ],
        ]);

        DB::transaction(function () use (
            $validated,
            $allowedKeys
        ) {
            /*
            | Save navigation links
            */

            foreach ($validated['items'] as $key => $values) {
                if (!in_array($key, $allowedKeys, true)) {
                    continue;
                }

                NavigationSetting::updateOrCreate(
                    [
                        'key' => $key,
                    ],
                    [
                        'placement' => $values['placement'],
                        'sort_order' => (int) $values['sort_order'],
                    ]
                );
            }

            /*
            | Save desktop logo width
            */

            NavigationSetting::updateOrCreate(
                [
                    'key' => 'logo_desktop_width',
                ],
                [
                    'placement' => 'hidden',
                    'sort_order' => (int) $validated['logo_desktop_width'],
                ]
            );

            /*
            | Save mobile logo width
            */

            NavigationSetting::updateOrCreate(
                [
                    'key' => 'logo_mobile_width',
                ],
                [
                    'placement' => 'hidden',
                    'sort_order' => (int) $validated['logo_mobile_width'],
                ]
            );
        });

        return redirect()
            ->route('admin.navigation.edit')
            ->with(
                'success',
                'تم حفظ إعدادات القائمة والشعار بنجاح.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Available navigation links
    |--------------------------------------------------------------------------
    */

    private function availableItems(): array
    {
        $items = [
            [
                'key' => 'home',
                'title' => 'الرئيسية / Home',
            ],
            [
                'key' => 'products.index',
                'title' => 'المنتجات / Products',
            ],
            [
                'key' => 'gallery.index',
                'title' => 'المعرض / Gallery',
            ],
            [
                'key' => 'locations.index',
                'title' => 'أماكن البيع / Locations',
            ],
            [
                'key' => 'about',
                'title' => 'من نحن / About',
            ],
            [
                'key' => 'vision',
                'title' => 'رؤيتنا / Vision',
            ],
            [
                'key' => 'contact.index',
                'title' => 'تواصل معنا / Contact',
            ],
        ];

        /*
        | Add published CMS pages
        */

        $pages = SitePage::query()
            ->where('is_active', true)
            ->whereNotIn('key', [
                'about',
                'vision',
            ])
            ->orderBy('id')
            ->get();

        foreach ($pages as $page) {
            $items[] = [
                'key' => 'page:' . $page->key,
                'title' => $page->translated_title
                    ?: $page->key,
            ];
        }

        return $items;
    }
}
