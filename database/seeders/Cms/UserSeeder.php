<?php
namespace Database\Seeders\Cms;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(User::count() != 0) {
            return;
        }

        $user = new User;
        $user->fullname = 'Administrator';
        $user->email = 'admin@admin.com';
        $user->password = bcrypt('admin123');
        $user->status = 'ACTV';
        $user->disableLogging();
        $user->save();

        $user->assignRole('superadmin');

    }
}
