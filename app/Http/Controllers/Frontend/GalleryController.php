<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(Request $request): View
    {
        $category = (string) $request->query('category', '');
        $allowed = ['products', 'packaging', 'campaigns', 'events', 'general'];
        $images = GalleryImage::query()
            ->where('is_active', true)
            ->when(in_array($category, $allowed, true), fn ($query) => $query->where('category', $category))
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();
        return view('frontend.gallery.index', compact('images', 'category'));
    }
}
