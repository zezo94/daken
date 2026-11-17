<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLocationRequest;
use App\Http\Requests\Admin\UpdateLocationRequest;
use App\Models\Location;
use App\Services\AdminActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class LocationController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:all,active,inactive'],
        ]);

        $search = trim($filters['search'] ?? '');
        $status = $filters['status'] ?? 'all';

        $locations = Location::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name->ar', 'like', "%{$search}%")
                        ->orWhere('name->en', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('country', 'like', "%{$search}%");
                });
            })
            ->when($status === 'active', fn ($query) =>
            $query->where('is_active', true)
            )
            ->when($status === 'inactive', fn ($query) =>
            $query->where('is_active', false)
            )
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.locations.index', compact(
            'locations',
            'search',
            'status'
        ));
    }

    public function create(): View
    {
        return view('admin.locations.create');
    }

    public function store(StoreLocationRequest $request): RedirectResponse
    {
        $data = $request->validated();

        unset($data['image'], $data['remove_image']);

        $data['is_active'] = $request->boolean('is_active');

        $newImage = null;

        try {
            if ($request->hasFile('image')) {
                $newImage = $request->file('image')
                    ->store('locations', 'public');
                if ($newImage === false) {
                    throw new \RuntimeException('Location image upload failed.');
                }

                $data['image'] = $newImage;
            }

            DB::transaction(function () use ($data): void {
                $location = Location::create($data);

                app(AdminActivityLogger::class)->log(
                    'location.created',
                    $location,
                    ['name' => $location->name]
                );
            });
        } catch (Throwable $exception) {
            if ($newImage) {
                Storage::disk('public')->delete($newImage);
            }

            throw $exception;
        }

        return redirect()
            ->route('admin.locations.index')
            ->with('success', 'تم إضافة مكان البيع بنجاح.');
    }

    public function edit(Location $location): View
    {
        return view('admin.locations.edit', compact('location'));
    }

    public function update(
        UpdateLocationRequest $request,
        Location $location
    ): RedirectResponse {
        $data = $request->validated();

        unset($data['image'], $data['remove_image']);

        $data['is_active'] = $request->boolean('is_active');

        $oldImage = $location->image;
        $newImage = null;

        try {
            if ($request->hasFile('image')) {
                $newImage = $request->file('image')
                    ->store('locations', 'public');
                if ($newImage === false) {
                    throw new \RuntimeException('Location image upload failed.');
                }

                $data['image'] = $newImage;
            } elseif ($request->boolean('remove_image')) {
                $data['image'] = null;
            }

            DB::transaction(function () use ($location, $data): void {
                $location->update($data);

                app(AdminActivityLogger::class)->log(
                    'location.updated',
                    $location,
                    ['name' => $location->name]
                );
            });
        } catch (Throwable $exception) {
            if ($newImage) {
                Storage::disk('public')->delete($newImage);
            }

            throw $exception;
        }

        if (
            is_string($oldImage)
            && preg_match(
                '~\Alocations/[^/\\\\]+\.(?:jpg|jpeg|png|webp)\z~i',
                $oldImage
            )
            && ($newImage || $request->boolean('remove_image'))
        ) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()
            ->route('admin.locations.index')
            ->with('success', 'تم تحديث مكان البيع بنجاح.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        $imagePath = $location->image;

        DB::transaction(function () use ($location): void {
            $location->delete();

            app(AdminActivityLogger::class)->log(
                'location.deleted',
                $location,
                ['name' => $location->name]
            );
        });

        if (
            is_string($imagePath)
            && preg_match(
                '~\Alocations/[^/\\\\]+\.(?:jpg|jpeg|png|webp)\z~i',
                $imagePath
            )
        ) {
            Storage::disk('public')->delete($imagePath);
        }

        return redirect()
            ->route('admin.locations.index')
            ->with('success', 'تم حذف مكان البيع بنجاح.');
    }
}
