<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            ['name'=>'Angela','lastname'=>'Admin','username'=>'Angela','email'=>'angela.admin@example.com','password'=>'olasiman'],
            ['name'=>'Nicole','lastname'=>'Admin','username'=>'Nicole','email'=>'nicole.admin@example.com','password'=>'Vacal'],
            ['name'=>'Daryl','lastname'=>'Armerola','username'=>'Daryl','email'=>'daryl.admin@example.com','password'=>'Armerola'],
        ];

        foreach ($admins as $admin) {
            User::updateOrCreate(
                ['username'=>$admin['username']],
                $admin + ['role'=>'admin','is_active'=>true]
            );
        }
    }
}
