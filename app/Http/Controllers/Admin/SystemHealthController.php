<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class SystemHealthController extends Controller
{
    public function index(): View
    {
        $checks = [];

        try {
            DB::connection()->getPdo();

            $checks[] = [
                'name' => 'الاتصال بقاعدة البيانات',
                'status' => true,
                'description' => 'الاتصال بقاعدة البيانات يعمل بنجاح.',
            ];
        } catch (Throwable $exception) {
            report($exception);

            $checks[] = [
                'name' => 'الاتصال بقاعدة البيانات',
                'status' => false,
                'description' => 'تعذر الاتصال بقاعدة البيانات. راجع سجل الأخطاء.',
            ];
        }

        $storagePath = storage_path('app/public');

        $checks[] = [
            'name' => 'مجلد تخزين الصور',
            'status' => is_dir($storagePath) && is_writable($storagePath),
            'description' => 'التحقق من وجود مجلد الصور وإمكانية الكتابة داخله.',
        ];

        $checks[] = [
            'name' => 'رابط الصور العامة',
            'status' => is_link(public_path('storage'))
                || is_dir(public_path('storage')),
            'description' => 'التحقق من وجود مسار public/storage.',
        ];

        $cachePath = storage_path('framework/cache');

        $checks[] = [
            'name' => 'مجلد التخزين المؤقت',
            'status' => is_dir($cachePath) && is_writable($cachePath),
            'description' => 'التحقق من إمكانية الكتابة داخل مجلد Cache.',
        ];

        $viewsPath = storage_path('framework/views');

        $checks[] = [
            'name' => 'مجلد ملفات Blade المؤقتة',
            'status' => is_dir($viewsPath) && is_writable($viewsPath),
            'description' => 'التحقق من مجلد ملفات العرض المجمعة.',
        ];

        return view('admin.system-health.index', [
            'checks' => $checks,
            'phpVersion' => PHP_VERSION,
            'laravelVersion' => app()->version(),
            'environment' => app()->environment(),
        ]);
    }
}
