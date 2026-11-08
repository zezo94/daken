<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SitePage;
use Illuminate\View\View;

class DynamicPageController extends Controller
{
    public function show(string $key): View
    {
        $page = SitePage::query()->where('key', $key)->where('is_active', true)->firstOrFail();
        return view('frontend.pages.show', compact('page'));
    }
}
