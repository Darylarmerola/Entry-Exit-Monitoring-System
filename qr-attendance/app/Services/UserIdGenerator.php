<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use LogicException;

class UserIdGenerator
{
    /**
     * Generate the next role-specific school ID.
     * Call this from an active database transaction.
     */
    public static function next(string $role): string
    {
        $formats = [
            'student' => ['prefix' => 'STU-', 'width' => 6],
            'teacher' => ['prefix' => 'TEA-', 'width' => 5],
        ];

        if (! isset($formats[$role])) {
            throw new LogicException("No school ID format is configured for role [{$role}].");
        }

        $counter = DB::table('user_id_counters')
            ->where('role', $role)
            ->lockForUpdate()
            ->first();

        if (! $counter) {
            throw new LogicException("The ID counter for role [{$role}] is missing. Run migrations first.");
        }

        $number = (int) $counter->next_number;

        DB::table('user_id_counters')
            ->where('role', $role)
            ->update(['next_number' => $number + 1]);

        return $formats[$role]['prefix'] . str_pad((string) $number, $formats[$role]['width'], '0', STR_PAD_LEFT);
    }
}
