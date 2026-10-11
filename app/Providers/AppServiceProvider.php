<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::directive('assetv', function (string $expression): string {
            return "<?php echo asset($expression).(file_exists(public_path($expression)) ? '?v='.filemtime(public_path($expression)) : ''); ?>";
        });

        Blade::directive('sasset', function (string $expression): string {
            // The public disk root is base_path(env('PUBLIC_STORAGE_ROOT')) when set,
            // otherwise storage_path('app/public') served via the storage:link symlink.
            // The web-accessible URL prefix is the portion of that root below public/.
            $storageRoot = env('PUBLIC_STORAGE_ROOT');
            if ($storageRoot) {
                $prefix = rtrim(preg_replace('#^public/?#', '', $storageRoot), '/');
                $prefix = $prefix !== '' ? $prefix.'/' : '';
            } else {
                $prefix = 'storage/';
            }

            return "<?php \$__sp = '{$prefix}'.($expression); echo asset(\$__sp).(file_exists(public_path(\$__sp)) ? '?v='.filemtime(public_path(\$__sp)) : ''); ?>";
        });
    }
}
