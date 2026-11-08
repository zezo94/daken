
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Frontend Controllers
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\DynamicPageController;
use App\Http\Controllers\Frontend\GalleryController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\LocationController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\SitePageController;

/*
|--------------------------------------------------------------------------
| DAKEN Public Website Routes
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Homepage
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

/*
|--------------------------------------------------------------------------
| Products
|--------------------------------------------------------------------------
*/

Route::get('/products', [ProductController::class, 'index'])
    ->name('products.index');

Route::get('/products/{slug}', [ProductController::class, 'show'])
    ->name('products.show');

/*
|--------------------------------------------------------------------------
| Gallery
|--------------------------------------------------------------------------
*/

Route::get('/gallery', [GalleryController::class, 'index'])
    ->name('gallery.index');

/*
|--------------------------------------------------------------------------
| Store Locations
|--------------------------------------------------------------------------
*/

Route::get('/locations', [LocationController::class, 'index'])
    ->name('locations.index');

/*
|--------------------------------------------------------------------------
| Static Pages
|--------------------------------------------------------------------------
*/

Route::get('/about', [SitePageController::class, 'about'])
    ->name('about');

Route::get('/vision', [SitePageController::class, 'vision'])
    ->name('vision');

/*
|--------------------------------------------------------------------------
| Dynamic CMS Pages
|--------------------------------------------------------------------------
|
| Example:
| /pages/our-story
| /pages/privacy-policy
|
*/

Route::get('/pages/{key}', [DynamicPageController::class, 'show'])
    ->where('key', '[a-z][a-z0-9-]*')
    ->name('pages.show');

/*
|--------------------------------------------------------------------------
| Contact
|--------------------------------------------------------------------------
*/

Route::get('/contact', [ContactController::class, 'index'])
    ->name('contact.index');

Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:5,10')
    ->name('contact.store');

/*
|--------------------------------------------------------------------------
| Language Switcher
|--------------------------------------------------------------------------
|
| Supported languages:
| ar = Arabic
| en = English
|
*/

Route::post('/language', function (Request $request) {

    $validated = $request->validate([
        'locale' => [
            'required',
            'in:ar,en',
        ],
    ]);

    $request->session()->put(
        'locale',
        $validated['locale']
    );

    return redirect()->back();

})
    ->middleware('throttle:20,1')
    ->name('language.switch');

/*
|--------------------------------------------------------------------------
| User Profile
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::view('/my-profile', 'profile.show')
        ->name('profile.show');
});
