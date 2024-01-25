<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $id = DB::table('users')->insertGetId([
            'name' => 'admin',
            'email' => 'admin@rankmf.com',
            'password' => Hash::make('1234'),
			'is_drm_user' => 1
        ]);

        $role_id = DB::table('role_master')->insertGetId([
            'label' => 'Admin',
            'have_all_permissions' => 1,
            'show_all_arn_data' => 1,
            'status' => 1,
        ]);

        DB::table('users_details')->insert([
            'user_id' => $id,
            'role_id' => $role_id,
			'employee_code' => 'E1',
			'designation' => 'admin',
			'mobile_number' => '9876543210',
            'reporting_to' => 1,
            'skip_in_arn_mapping' => 1,
            'status' => 1,
        ]);
    }
}
