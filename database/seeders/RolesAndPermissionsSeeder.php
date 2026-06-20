<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // تصفير الكاش تبع الصلاحيات (مهم قبل أي إنشاء)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // إنشاء الأدوار الثلاثة
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $orgAdmin   = Role::firstOrCreate(['name' => 'org_admin']);
        $employee   = Role::firstOrCreate(['name' => 'employee']);

        // إنشاء بعض الصلاحيات الأساسية (نبدأ بسيط، نزيد لاحقًا)
        $permissions = [
            'approve_organizations',
            'reject_organizations',
            'manage_employees',
            'manage_projects',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ربط الصلاحيات بالأدوار
        $superAdmin->givePermissionTo(['approve_organizations', 'reject_organizations']);
        $orgAdmin->givePermissionTo(['manage_employees', 'manage_projects']);
        // employee لسا بدون صلاحيات خاصة، نزيدها لاحقًا حسب الحاجة
    }
}
