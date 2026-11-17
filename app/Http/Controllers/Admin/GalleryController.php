<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;












class GalleryController extends Controller
{
    public function index(): View
    {
        $images = GalleryImage::query()->orderBy('sort_order')->latest('id')->paginate(18);
        return view('admin.gallery.index', compact('images'));
    }

    public function create(): View
    {
        return view('admin.gallery.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, true);
        $path = $request->file('image')->store('gallery', 'public');

        if ($path === false) {
            throw new \RuntimeException('Gallery image upload failed.');
        }

        $data['image_path'] = $path;
        unset($data['image']);

        try {
            GalleryImage::create($data);
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($path);

            throw $exception;
        }
        return redirect()->route('admin.gallery.index')->with('success', 'تمت إضافة الصورة بنجاح.');
    }

    public function edit(GalleryImage $gallery): View
    {
        return view('admin.gallery.edit', ['image' => $gallery]);
    }

    public function update(Request $request, GalleryImage $gallery): RedirectResponse
    {
        $data = $this->validated($request, false);
        $newPath = null;

        if ($request->hasFile('image')) {
            $newPath = $request->file('image')->store('gallery', 'public');

            if ($newPath === false) {
                throw new \RuntimeException('Gallery image upload failed.');
            }

            $data['image_path'] = $newPath;
        }

        unset($data['image']);

        try {
            $gallery->update($data);
        } catch (\Throwable $exception) {
            if ($newPath !== null) {
                Storage::disk('public')->delete($newPath);
            }

            throw $exception;
        }

        return redirect()
            ->route('admin.gallery.index')
            ->with('success', 'تم تحديث الصورة بنجاح.');
    }
    private function validated(Request $request, bool $creating): array
    {
        $data = $request->validate([
            'title.ar' => ['required', 'string', 'max:255'],
            'title.en' => ['required', 'string', 'max:255'],
            'description.ar' => ['nullable', 'string', 'max:3000'],
            'description.en' => ['nullable', 'string', 'max:3000'],
            'category' => ['required', 'in:products,packaging,campaigns,events,general'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999999'],
            'is_active' => ['required', 'boolean'],
            'image' => [$creating ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
        $data['description'] = [
            'ar' => $data['description']['ar'] ?? '',
            'en' => $data['description']['en'] ?? '',
        ];
        return $data;
    }
}
