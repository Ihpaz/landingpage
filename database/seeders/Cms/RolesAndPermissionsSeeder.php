<?php
namespace Database\Seeders\Cms;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    protected $permissions = [
        ['name' => 'cms user-management view', 'description' => 'Menampilkan list data user management', 'guard_name' => 'web'],
        ['name' => 'cms user-management create', 'description' => 'Manambahkan data user', 'guard_name' => 'web'],
        ['name' => 'cms user-management update', 'description' => 'Mengubah data user', 'guard_name' => 'web'],
        ['name' => 'cms user-management delete', 'description' => 'Menghapus data user', 'guard_name' => 'web'],
        ['name' => 'cms user-management impersonate', 'description' => 'Melakukan impersonate pada user yang dipilih', 'guard_name' => 'web'],

        ['name' => 'cms user-password reset', 'description' => 'Mereset password user', 'guard_name' => 'web'],
        
        ['name' => 'cms role-management view', 'description' => 'Menampilkan list data user management', 'guard_name' => 'web'],
        ['name' => 'cms role-management create', 'description' => 'Manambahkan data role', 'guard_name' => 'web'],
        ['name' => 'cms role-management update', 'description' => 'Mengubah data role', 'guard_name' => 'web'],
        ['name' => 'cms role-management delete', 'description' => 'Menghapus data role', 'guard_name' => 'web'],

        ['name' => 'cms permission-management view', 'description' => 'Menampilkan list data permission management', 'guard_name' => 'web'],
        ['name' => 'cms permission-management create', 'description' => 'Manambahkan data permission', 'guard_name' => 'web'],
        ['name' => 'cms permission-management update', 'description' => 'Mengubah data permission', 'guard_name' => 'web'],
        ['name' => 'cms permission-management delete', 'description' => 'Menghapus data permission', 'guard_name' => 'web'],

        ['name' => 'cms activity-log view', 'description' => 'Menampilkan list data activiy log', 'guard_name' => 'web'],

        ['name' => 'cms backup-management view', 'description' => 'Menampilkan list data backup management', 'guard_name' => 'web'],
        ['name' => 'cms backup-management create', 'description' => 'Menambahkan data backup', 'guard_name' => 'web'],
        ['name' => 'cms backup-management delete', 'description' => 'Menghapus data backup management', 'guard_name' => 'web'],

        ['name' => 'cms scheduler-management view', 'description' => 'Menampilkan list data task scheduler', 'guard_name' => 'web'],
        ['name' => 'cms scheduler-management run', 'description' => 'Menjalankan task scheduler', 'guard_name' => 'web'],
    ];

    protected $roles = [
        ['name' => 'superadmin','description' => 'DIVSTI & DEVELOPER', 'guard_name' => 'web', 'level' => 1],
        ['name' => 'guest','description' => 'Tamu', 'guard_name' => 'web', 'level' => 9]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        foreach($this->roles as $data) {
            Role::updateOrCreate(['name' => $data['name']], $data);
        }
        
        $super_admin = Role::findByName('superadmin');
        foreach ($this->permissions as $data) {
            Permission::updateOrCreate(['name' => $data['name']], $data);
            $super_admin->givePermissionTo($data['name']);
        }
    }
}
