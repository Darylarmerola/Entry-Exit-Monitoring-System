<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_id_counters', function (Blueprint $table) {
            $table->string('role')->primary();
            $table->unsignedBigInteger('next_number')->default(1);
            $table->timestamps();
        });

        // Normalize existing student/teacher IDs in account creation order.
        // Internal relationships use users.id, so changing school_id does not
        // change attendance ownership or foreign-key relationships.
        DB::table('users')->whereIn('role', ['student', 'teacher'])->update(['school_id' => null]);

        $students = DB::table('users')->where('role', 'student')->orderBy('id')->pluck('id');
        foreach ($students as $index => $id) {
            DB::table('users')->where('id', $id)->update([
                'school_id' => 'STU-' . str_pad((string) ($index + 1), 6, '0', STR_PAD_LEFT),
            ]);
        }

        $teachers = DB::table('users')->where('role', 'teacher')->orderBy('id')->pluck('id');
        foreach ($teachers as $index => $id) {
            DB::table('users')->where('id', $id)->update([
                'school_id' => 'TEA-' . str_pad((string) ($index + 1), 5, '0', STR_PAD_LEFT),
            ]);
        }

        $now = now();
        DB::table('user_id_counters')->insert([
            ['role' => 'student', 'next_number' => $students->count() + 1, 'created_at' => $now, 'updated_at' => $now],
            ['role' => 'teacher', 'next_number' => $teachers->count() + 1, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('user_id_counters');
    }
};
