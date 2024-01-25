<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
			["id" => "2","label" => "Admin","have_all_permissions" => "0","show_all_arn_data" => "1","status" => "1"],
			["id" => "3","label" => "Zonal Head","have_all_permissions" => "0","show_all_arn_data" => "1","status" => "1"],
			["id" => "4","label" => "BDM Role","have_all_permissions" => "0","show_all_arn_data" => "0","status" => "1"],
		];

        foreach ($data as $item) {
            DB::table('role_master')
                ->updateOrInsert(
                    ['id' => $item['id']],
                    [
						'have_all_permissions' => $item['have_all_permissions'],
						'show_all_arn_data' => $item['show_all_arn_data'],
						'status' => $item['status'],
						'label' => $item['label'],
					]
                );
        }
    }
}
