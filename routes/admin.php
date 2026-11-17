
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AdminActivityController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\AnalyticsExportController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmailNotificationController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\HomepageController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\NavigationController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\SitePageController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\SystemHealthController;
use App\Http\Controllers\Admin\UserManagementController;

/*
|--------------------------------------------------------------------------
| DAKEN Admin Routes — Stage 60
|--------------------------------------------------------------------------
|
| All routes require:
| - Authentication
| - Active admin account
|
| Each management section has its own permission.
|
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/', [DashboardController::class, 'index'])
            ->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Analytics
        |--------------------------------------------------------------------------
        */

        Route::middleware('admin.permission:analytics.view')
            ->group(function (): void {
                Route::get(
                    '/analytics',
                    [AnalyticsController::class, 'index']
                )->name('analytics.index');

                Route::get(
                    '/analytics/export',
                    [AnalyticsExportController::class, 'export']
                )->name('analytics.export');
            });

        /*
        |--------------------------------------------------------------------------
        | Activity Log
        |--------------------------------------------------------------------------
        */

        Route::middleware('admin.permission:activities.view')
            ->group(function (): void {
                Route::get(
                    '/activities',
                    [AdminActivityController::class, 'index']
                )->name('activities.index');

                Route::get(
                    '/activities/export',
                    [AdminActivityController::class, 'export']
                )->name('activities.export');

                Route::get(
                    '/activities/{activity}',
                    [AdminActivityController::class, 'show']
                )
                    ->whereNumber('activity')
                    ->name('activities.show');
            });

        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */

        Route::middleware('admin.permission:categories.manage')
            ->group(function (): void {
                Route::delete(
                    '/categories/bulk-delete',
                    [CategoryController::class, 'bulkDestroy']
                )->name('categories.bulk-destroy');

                Route::resource(
                    'categories',
                    CategoryController::class
                )->except(['show']);
            });

        /*
        |--------------------------------------------------------------------------
        | Products and Product Images
        |--------------------------------------------------------------------------
        */

        Route::middleware('admin.permission:products.manage')
            ->group(function (): void {
                Route::post(
                    '/products/bulk-action',
                    [ProductController::class, 'bulkAction']
                )->name('products.bulk-action');

                Route::resource(
                    'products',
                    ProductController::class
                )->except(['show']);

                Route::get(
                    '/products/{product}/images',
                    [ProductImageController::class, 'index']
                )->name('products.images.index');

                Route::post(
                    '/products/{product}/images',
                    [ProductImageController::class, 'store']
                )->name('products.images.store');

                Route::put(
                    '/products/{product}/images/order',
                    [ProductImageController::class, 'updateOrder']
                )->name('products.images.order');

                Route::patch(
                    '/products/{product}/images/{image}/move',
                    [ProductImageController::class, 'move']
                )->name('products.images.move');

                Route::patch(
                    '/products/{product}/images/{image}/primary',
                    [ProductImageController::class, 'setPrimary']
                )->name('products.images.primary');

                Route::delete(
                    '/products/{product}/images/{image}',
                    [ProductImageController::class, 'destroy']
                )->name('products.images.destroy');
            });

        /*
        |--------------------------------------------------------------------------
        | Locations
        |--------------------------------------------------------------------------
        */

        Route::middleware('admin.permission:locations.manage')
            ->group(function (): void {
                Route::resource(
                    'locations',
                    LocationController::class
                )->except(['show']);
            });

        /*
        |--------------------------------------------------------------------------
        | Contact Messages
        |--------------------------------------------------------------------------
        */

        Route::middleware('admin.permission:messages.manage')
            ->group(function (): void {
                Route::get(
                    '/messages',
                    [ContactMessageController::class, 'index']
                )->name('messages.index');

                Route::get(
                    '/messages/{message}',
                    [ContactMessageController::class, 'show']
                )->name('messages.show');

                Route::delete(
                    '/messages/{message}',
                    [ContactMessageController::class, 'destroy']
                )->name('messages.destroy');
            });

        /*
        |--------------------------------------------------------------------------
        | Email Notifications
        |--------------------------------------------------------------------------
        */

        Route::middleware('admin.permission:email-notifications.manage')
            ->group(function (): void {
                Route::get(
                    '/email-notifications',
                    [EmailNotificationController::class, 'edit']
                )->name('email-notifications.edit');

                Route::put(
                    '/email-notifications',
                    [EmailNotificationController::class, 'update']
                )->name('email-notifications.update');
            });

        /*
        |--------------------------------------------------------------------------
        | CMS Pages
        |--------------------------------------------------------------------------
        */

        Route::middleware('admin.permission:pages.manage')
            ->group(function (): void {
                Route::get(
                    '/pages',
                    [SitePageController::class, 'index']
                )->name('pages.index');

                Route::get(
                    '/pages/create',
                    [SitePageController::class, 'create']
                )->name('pages.create');

                Route::post(
                    '/pages',
                    [SitePageController::class, 'store']
                )->name('pages.store');

                Route::get(
                    '/pages/{page}/edit',
                    [SitePageController::class, 'edit']
                )
                    ->whereNumber('page')
                    ->name('pages.edit');

                Route::put(
                    '/pages/{page}',
                    [SitePageController::class, 'update']
                )
                    ->whereNumber('page')
                    ->name('pages.update');
            });

        /*
        |--------------------------------------------------------------------------
        | Gallery
        |--------------------------------------------------------------------------
        */

        Route::middleware('admin.permission:gallery.manage')
            ->group(function (): void {
                Route::get(
                    '/gallery',
                    [GalleryController::class, 'index']
                )->name('gallery.index');

                Route::get(
                    '/gallery/create',
                    [GalleryController::class, 'create']
                )->name('gallery.create');

                Route::post(
                    '/gallery',
                    [GalleryController::class, 'store']
                )->name('gallery.store');

                Route::get(
                    '/gallery/{gallery}/edit',
                    [GalleryController::class, 'edit']
                )
                    ->whereNumber('gallery')
                    ->name('gallery.edit');

                Route::put(
                    '/gallery/{gallery}',
                    [GalleryController::class, 'update']
                )
                    ->whereNumber('gallery')
                    ->name('gallery.update');
            });

        /*
        |--------------------------------------------------------------------------
        | Navigation and Logo
        |--------------------------------------------------------------------------
        */

        Route::middleware('admin.permission:navigation.manage')
            ->group(function (): void {
                Route::get(
                    '/navigation',
                    [NavigationController::class, 'edit']
                )->name('navigation.edit');

                Route::put(
                    '/navigation',
                    [NavigationController::class, 'update']
                )->name('navigation.update');
            });

        /*
        |--------------------------------------------------------------------------
        | Homepage
        |--------------------------------------------------------------------------
        */

        Route::middleware('admin.permission:homepage.manage')
            ->group(function (): void {
                Route::get(
                    '/homepage',
                    [HomepageController::class, 'edit']
                )->name('homepage.edit');

                Route::put(
                    '/homepage',
                    [HomepageController::class, 'update']
                )->name('homepage.update');
            });

        /*
        |--------------------------------------------------------------------------
        | User Management
        |--------------------------------------------------------------------------
        */

        Route::middleware('admin.permission:users.manage')
            ->group(function (): void {
                Route::resource(
                    'users',
                    UserManagementController::class
                )->except(['show']);
            });


        /*
        |--------------------------------------------------------------------------
        | System Health
        |--------------------------------------------------------------------------
        */

        Route::middleware('admin.permission:system-health.view')
            ->group(function (): void {
                Route::get(
                    '/system-health',
                    [SystemHealthController::class, 'index']
                )->name('system-health.index');
            });

        /*
        |--------------------------------------------------------------------------
        | General Site Settings
        |--------------------------------------------------------------------------
        */

        Route::middleware('admin.permission:settings.manage')
            ->group(function (): void {
                Route::get(
                    '/settings',
                    [SiteSettingController::class, 'edit']
                )->name('settings.edit');

                Route::put(
                    '/settings',
                    [SiteSettingController::class, 'update']
                )->name('settings.update');
            });
    });
