<?php

namespace Modules\ITAdmin\database\seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ITAdminDatabaseSeeder extends Seeder
{
    /**
     * تشغيل وتغذية قاعدة البيانات بإنشاء أول منظمة وأول مستخدم مسؤول عنها.
     */
    public function run(): void
    {
        DB::transaction(function () {

            $organizationId = 1;
            $orgExists = DB::table('organizations')->where('id', $organizationId)->exists();

            if (!$orgExists) {
                // تمرير حقل الـ email لمنع خرق قيد NOT NULL الخاص بجدول المنظمات لديك
                DB::table('organizations')->insert([
                    'id'         => $organizationId,
                    'name'       => 'منظمة مسار التجريبية الأولى',
                    'email'      => 'info@masar-demo.com', // تلبية القيد البرمجي الإلزامي
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // التحقق من عدم تكرار المستخدم المسؤول قبل عملية الحقن
            $adminEmail = 'tenant.admin@masar.com';
            $adminExists = User::query()->where('email', $adminEmail)->exists();

            if (!$adminExists) {
                User::query()->create([
                    'organization_id'      => $organizationId,
                    'name'                 => 'أحمد لؤي (مسؤول النظام)',
                    'email'                => $adminEmail,
                    'password'             => Hash::make('password123'), // كلمة مرور التجربة في بوسطمان
                    'is_active'            => true,
                    'must_change_password' => false,
                ]);
            }
        });
    }
}
