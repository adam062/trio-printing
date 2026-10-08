<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeTrioAdmin extends Command
{
    protected $signature = 'trio:make-admin {name : الاسم الظاهر للأدمن} {email : بريد الدخول} {--password= : كلمة المرور (اتركها فارغة لإدخالها بشكل مخفي)}';

    protected $description = 'Create or promote a TRIO administrator account';

    public function handle(): int
    {
        $password = $this->option('password') ?: $this->secret('Admin password');

        if (! $password || mb_strlen($password) < 12) {
            $this->error('استخدم كلمة مرور لا تقل عن 12 حرفًا.');

            return self::FAILURE;
        }

        User::updateOrCreate(
            ['email' => $this->argument('email')],
            [
                'name' => $this->argument('name'),
                'password' => Hash::make($password),
                'is_admin' => true,
            ],
        );

        $this->info('تم تجهيز حساب الأدمن.');

        return self::SUCCESS;
    }
}
