<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SitePage;
use Illuminate\View\View;

class SitePageController extends Controller
{
    public function about(): View
    {
        return $this->renderPage('about');
    }

    public function vision(): View
    {
        return $this->renderPage('vision');
    }

    private function renderPage(string $key): View
    {
        $page = SitePage::query()
            ->where('key', $key)
            ->where('is_active', true)
            ->firstOrFail();

        return view('pages.content', compact('page'));
    }
}
