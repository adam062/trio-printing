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
            // When PUBLIC_STORAGE_ROOT is set to 'public/storage', files are stored directly in public/storage
            // We need to check if we're using the real folder or the symlink approach
            if (env('PUBLIC_STORAGE_ROOT')) {
                // Using real folder: path is already relative to public/
                return "<?php echo asset($expression).(file_exists(public_path($expression)) ? '?v='.filemtime(public_path($expression)) : ''); ?>";
            }

            // Using symlink: prepend storage/
            return "<?php \$__sp = 'storage/'.($expression); echo asset(\$__sp).(file_exists(public_path(\$__sp)) ? '?v='.filemtime(public_path(\$__sp)) : ''); ?>";
        });
    }
}
