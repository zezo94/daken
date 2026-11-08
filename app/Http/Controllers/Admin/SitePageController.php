<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSitePageRequest;
use App\Http\Requests\Admin\UpdateSitePageRequest;
use App\Models\SitePage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class SitePageController extends Controller
{
    public function index(): View
    {
        $pages = SitePage::query()->orderBy('id')->get();
        return view('admin.pages.index', compact('pages'));
    }

    public function create(): View
    {
        return view('admin.pages.create');
    }

    public function store(StoreSitePageRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $newImage = null;

        try {
            if ($request->hasFile('image')) {
                $newImage = $request->file('image')->store('site-pages', 'public');
                $data['image'] = $newImage;
            }
            SitePage::create($data);
        } catch (Throwable $exception) {
            if ($newImage) Storage::disk('public')->delete($newImage);
            throw $exception;
        }

        return redirect()->route('admin.pages.index')->with('success', 'تم إنشاء الصفحة بنجاح.');
    }

    public function edit(SitePage $page): View
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(UpdateSitePageRequest $request, SitePage $page): RedirectResponse
    {
        $data = $request->validated();
        // لا نحذف الصور الحالية، ولا نسمح بحذفها عبر هذا النموذج.
        unset($data['image'], $data['remove_image']);
        $newImage = null;

        try {
            if ($request->hasFile('image')) {
                $newImage = $request->file('image')->store('site-pages', 'public');
                $data['image'] = $newImage;
            }
            $page->update($data);
        } catch (Throwable $exception) {
            if ($newImage) Storage::disk('public')->delete($newImage);
            throw $exception;
        }

        return redirect()->route('admin.pages.index')->with('success', 'تم تحديث الصفحة بنجاح.');
    }
}
