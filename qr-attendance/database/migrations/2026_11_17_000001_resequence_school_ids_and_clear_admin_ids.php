<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Normalize existing accounts so admins have no school ID and student/teacher
     * IDs are consecutive, even when the ID-counter migration was run earlier.
     */
    public function up(): void
    {
        // Admins never receive a public school ID.
        DB::table('users')
            ->where('role', 'admin')
            ->update(['school_id' => null]);

        $formats = [
            'student' => ['prefix' => 'STU-', 'width' => 6],
            'teacher' => ['prefix' => 'TEA-', 'width' => 5],
        ];

        foreach ($formats as $role => $format) {
            // Clear the existing values first to avoid unique-key conflicts.
            DB::table('users')
                ->where('role', $role)
                ->update(['school_id' => null]);

            // Preserve account creation order using the internal database ID.
            $userIds = DB::table('users')
                ->where('role', $role)
                ->orderBy('id')
                ->pluck('id');

            foreach ($userIds as $index => $userId) {
                $schoolId = $format['prefix']
                    . str_pad((string) ($index + 1), $format['width'], '0', STR_PAD_LEFT);

                DB::table('users')
                    ->where('id', $userId)
                    ->update(['school_id' => $schoolId]);
            }

            // Store the next available number for future registrations.
            DB::table('user_id_counters')
                ->where('role', $role)
                ->update([
                    'next_number' => $userIds->count() + 1,
                    'updated_at' => now(),
                ]);
        }
    }

    /**
     * The previous school IDs cannot be reconstructed safely, so this data
     * normalization migration intentionally has no reverse data operation.
     */
    public function down(): void
    {
        // No rollback action: IDs are generated from the current account order.
    }
};
