<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $roles = [
            'Admin',
            'candidate',
            'CME',
            'Coach',
            'administrative'
        ];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }
    }
}
