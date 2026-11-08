<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Location;
use App\Models\Product;
use App\Models\SitePage;
use App\Services\SiteSettings;
use Illuminate\View\View;
use App\Models\GalleryImage;
class HomeController extends Controller
{
    public function index(SiteSettings $settings): View
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        $featuredProducts = Product::query()
            ->with(['category', 'primaryImage'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->whereHas('category', function ($query) {
                $query->where('is_active', true);
            })
            ->orderBy('sort_order')
            ->limit(8)
            ->get();

        $locations = Location::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit(3)
            ->get();

        $aboutPage = SitePage::query()
            ->where('key', 'about')
            ->where('is_active', true)
            ->first();
        $galleryImages = GalleryImage::query()
            ->where('is_active', true)
            ->whereNotNull('image_path')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->limit(6)
            ->get();
        return view('pages.home', [
            'settings' => $settings->all(),
            'categories' => $categories,
            'featuredProducts' => $featuredProducts,
            'locations' => $locations,
            'aboutPage' => $aboutPage,
            'galleryImages' => $galleryImages,
            ]);
    }
}
