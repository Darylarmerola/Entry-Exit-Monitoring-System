<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\UserIdGenerator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Existing admin accounts
        $admins = [
            [
                'name' => 'Angela',
                'lastname' => 'Admin',
                'username' => 'Angela',
                'email' => 'angela.admin@example.com',
                'password' => 'Olasiman'
            ],
            [
                'name' => 'Nicole',
                'lastname' => 'Admin',
                'username' => 'Nicole',
                'email' => 'nicole.admin@example.com',
                'password' => 'Vacal'
            ],
            [
                'name' => 'Daryl',
                'lastname' => 'Armerola',
                'username' => 'Daryl',
                'email' => 'daryl.admin@example.com',
                'password' => 'Armerola'
            ],
        ];

        foreach ($admins as $admin) {
            User::updateOrCreate(
                ['username' => $admin['username']],
                $admin + [
                    'role' => 'admin',
                    'is_active' => true
                ]
            );
        }

        // Generate 200 students
    $courses = ['BSIT', 'BSCS', 'BSN', 'BSCE'];

    foreach ($courses as $course) {
        for ($i = 0; $i < 50; $i++) {

            DB::transaction(function () use ($course) {

                $schoolId = UserIdGenerator::next('student');

                User::factory()
                    ->student()
                    ->create([
                    'school_id' => $schoolId,
                    'course' => $course,
                ]);
        });
    }
}

        // Generate 10 teachers
        for ($i = 0; $i < 10; $i++) {
            DB::transaction(function () {
                $schoolId = UserIdGenerator::next('teacher');

                User::factory()
                    ->teacher()
                    ->create([
                        'school_id' => $schoolId,
                    ]);
            });
        }

        $this->command->info(
            '200 students and 10 teachers generated successfully!'
        );
    }
}