<?php

namespace Database\Seeders\Master;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class LocationPermissionSeeder extends Seeder
{
    protected $permissions = [
        ['name' => 'master country view', 'description' => 'Menampilkan list data country', 'guard_name' => 'web'],
        ['name' => 'master country create', 'description' => 'Manambahkan data country', 'guard_name' => 'web'],
        ['name' => 'master country update', 'description' => 'Mengubah data country', 'guard_name' => 'web'],
        ['name' => 'master country delete', 'description' => 'Menghapus data country', 'guard_name' => 'web'],

        ['name' => 'master currency view', 'description' => 'Menampilkan list data currency', 'guard_name' => 'web'],
        ['name' => 'master currency create', 'description' => 'Manambahkan data currency', 'guard_name' => 'web'],
        ['name' => 'master currency update', 'description' => 'Mengubah data currency', 'guard_name' => 'web'],
        ['name' => 'master currency delete', 'description' => 'Menghapus data currency', 'guard_name' => 'web'],

        ['name' => 'master province view', 'description' => 'Menampilkan list data province', 'guard_name' => 'web'],
        ['name' => 'master province create', 'description' => 'Manambahkan data province', 'guard_name' => 'web'],
        ['name' => 'master province update', 'description' => 'Mengubah data province', 'guard_name' => 'web'],
        ['name' => 'master province delete', 'description' => 'Menghapus data province', 'guard_name' => 'web'],

        ['name' => 'master regency view', 'description' => 'Menampilkan list data regency', 'guard_name' => 'web'],
        ['name' => 'master regency create', 'description' => 'Manambahkan data regency', 'guard_name' => 'web'],
        ['name' => 'master regency update', 'description' => 'Mengubah data regency', 'guard_name' => 'web'],
        ['name' => 'master regency delete', 'description' => 'Menghapus data regency', 'guard_name' => 'web'],

        ['name' => 'master district view', 'description' => 'Menampilkan list data district', 'guard_name' => 'web'],
        ['name' => 'master district create', 'description' => 'Manambahkan data district', 'guard_name' => 'web'],
        ['name' => 'master district update', 'description' => 'Mengubah data district', 'guard_name' => 'web'],
        ['name' => 'master district delete', 'description' => 'Menghapus data district', 'guard_name' => 'web'],

        ['name' => 'master village view', 'description' => 'Menampilkan list data village', 'guard_name' => 'web'],
        ['name' => 'master village create', 'description' => 'Manambahkan data village', 'guard_name' => 'web'],
        ['name' => 'master village update', 'description' => 'Mengubah data village', 'guard_name' => 'web'],
        ['name' => 'master village delete', 'description' => 'Menghapus data village', 'guard_name' => 'web'],
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');
        
        $super_admin = Role::findByName('superadmin');
        foreach ($this->permissions as $data) {
            Permission::updateOrCreate(['name' => $data['name']], $data);
            $super_admin->givePermissionTo($data['name']);
        }
    }
}
