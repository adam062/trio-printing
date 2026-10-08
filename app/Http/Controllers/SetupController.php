<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SetupController extends Controller
{
    public function __invoke(Request $request)
    {
        $expectedKey = config('app.setup_key');
        $providedKey = $request->query('key');

        if (! is_string($expectedKey) || $expectedKey === '') {
            return response()->json([
                'error' => 'Web setup is not configured.',
            ], 503);
        }

        if (! is_string($providedKey) || ! hash_equals($expectedKey, $providedKey)) {
            return response()->json([
                'error' => 'Unauthorized setup access.',
            ], 403);
        }

        if (! is_string(config('app.admin_password')) || config('app.admin_password') === '') {
            return response()->json([
                'error' => 'Set TRIO_ADMIN_PASSWORD before running web setup.',
            ], 503);
        }

        try {
            // Run database migrations
            Artisan::call('migrate', ['--force' => true]);
            $migrateOutput = Artisan::output();

            // Run database seeders (Admin, Filaments, Products)
            Artisan::call('db:seed', ['--force' => true]);
            $seedOutput = Artisan::output();

            // Create storage symbolic link if not on Windows symlink restriction
            @Artisan::call('storage:link');

            $adminCount = User::where('is_admin', true)->count();

            return response()->html("
                <!doctype html>
                <html lang='ar' dir='rtl'>
                <head>
                    <meta charset='utf-8'>
                    <title>TRIO · تجهيز الموقع وقاعدة البيانات</title>
                    <style>
                        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #071019; color: #edf2f1; padding: 40px; text-align: center; }
                        .card { max-width: 600px; margin: 0 auto; background: #101e29; border: 1px solid rgba(214,232,237,.2); border-radius: 12px; padding: 35px; box-shadow: 0 20px 40px rgba(0,0,0,.5); }
                        h1 { color: #c8f228; margin-top: 0; }
                        .badge { background: #c8f228; color: #071019; padding: 6px 14px; border-radius: 20px; font-weight: bold; display: inline-block; margin-bottom: 20px; }
                        .creds { background: #08121a; padding: 18px; border-radius: 8px; text-align: right; margin: 25px 0; border: 1px dashed #34505c; font-family: monospace; }
                        .creds p { margin: 8px 0; font-size: 15px; }
                        .creds strong { color: #c8f228; }
                        a.btn { display: inline-block; background: #c8f228; color: #071019; text-decoration: none; font-weight: bold; padding: 12px 28px; border-radius: 6px; margin: 10px; }
                        pre { text-align: left; background: #04090e; padding: 12px; border-radius: 6px; font-size: 11px; max-height: 140px; overflow: auto; color: #8fa1a8; }
                    </style>
                </head>
                <body>
                    <div class='card'>
                        <span class='badge'>✓ تم تجهيز النظام بنجاح</span>
                        <h1>مبروك! قاعدة البيانات جاهزة</h1>
                        <p>تم تشغيل الجداول (Migrations) وإدخال الخامات والمنتجات وحساب الأدمن تلقائيًا.</p>

                        <div class='creds'>
                            <p><strong>البريد الإلكتروني للأدمن:</strong> admin@trio.local</p>
                            <p><strong>كلمة المرور:</strong> تم تعيينها من متغير TRIO_ADMIN_PASSWORD.</p>
                            <p><strong>الرابط المباشر للوحة:</strong> /admin</p>
                        </div>

                        <div>
                            <a class='btn' href='/admin'>الدخول إلى لوحة التحكم ↗</a>
                            <a class='btn' style='background: transparent; color: #edf2f1; border: 1px solid #c8f228;' href='/'>عرض المتجر ↗</a>
                        </div>

                        <details style='margin-top: 25px; text-align: right;'>
                            <summary style='cursor: pointer; color: #8fa1a8;'>عرض سجل التشغيل التقني (Artisan Output)</summary>
                            <pre>MIGRATE:\n{$migrateOutput}\n\nSEED:\n{$seedOutput}</pre>
                        </details>
                    </div>
                </body>
                </html>
            ");
        } catch (\Throwable $e) {
            return response()->html("
                <!doctype html>
                <html lang='ar' dir='rtl'>
                <head><meta charset='utf-8'><title>خطأ في التجهيز</title></head>
                <body style='background:#071019;color:#ff8e7a;font-family:sans-serif;padding:40px;text-align:center;'>
                    <h2>تعذّر تشغيل التجهيز التلقائي</h2>
                    <p style='color:#edf2f1;'>تأكد من إدخال بيانات قاعدة البيانات الصحيحة في ملف .env (DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD)</p>
                    <pre style='background:#101e29;color:#ff8e7a;padding:20px;text-align:left;max-width:700px;margin:20px auto;border-radius:8px;overflow:auto;'>".htmlspecialchars($e->getMessage()).'</pre>
                </body>
                </html>
            ', 500);
        }
    }
}
