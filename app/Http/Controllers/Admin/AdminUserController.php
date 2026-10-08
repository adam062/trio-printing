<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index()
    {
        $admins = User::query()->where('is_admin', true)->orderBy('id')->get();

        return view('admin.admins.index', [
            'admins' => $admins,
            'availablePermissions' => [
                'orders' => 'إدارة الطلبات والشحن (Orders & Shipping)',
                'products' => 'إدارة الكتالوج والمنتجات (Products & Catalog)',
                'filaments' => 'إدارة الخامات وبكرات الفيلمنت (Filaments & Spools)',
                'quotes' => 'إدارة طلبات الطباعة الخاصة (Custom 3D Prints)',
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['super_admin', 'staff'])],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [Rule::in(['orders', 'products', 'filaments', 'quotes'])],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_admin' => true,
            'role' => $data['role'],
            'permissions' => $data['role'] === 'super_admin' ? null : ($data['permissions'] ?? []),
        ]);

        return back()->with('success', "تم إنشاء حساب المشرف {$data['name']} وتحديد صلاحياته بنجاح.");
    }

    public function update(Request $request, User $user)
    {
        abort_unless($user->is_admin, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', Rule::in(['super_admin', 'staff'])],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [Rule::in(['orders', 'products', 'filaments', 'quotes'])],
        ]);

        $updateData = [
            'name' => $data['name'],
            'role' => $data['role'],
            'permissions' => $data['role'] === 'super_admin' ? null : ($data['permissions'] ?? []),
        ];

        if (! empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        return back()->with('success', "تم تحديث بيانات وصلاحيات المشرف {$user->name}.");
    }

    public function destroy(User $user)
    {
        abort_unless($user->is_admin, 404);

        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'لا يمكنك حذف حسابك الحالي الذي تستخدمه لتسجيل الدخول.']);
        }

        if ($user->id === 1) {
            return back()->withErrors(['error' => 'لا يمكن حذف حساب السوبر أدمن الأساسي.']);
        }

        $name = $user->name;
        $user->delete();

        return back()->with('success', "تم حذف حساب المشرف {$name}.");
    }
}
