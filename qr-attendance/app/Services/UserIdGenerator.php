<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use LogicException;

class UserIdGenerator
{
    /**
     * Generate the lowest available role-specific school ID.
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

        // Lock the role's counter row so simultaneous registrations cannot
        // select the same available ID.
        $counter = DB::table('user_id_counters')
            ->where('role', $role)
            ->lockForUpdate()
            ->first();

        if (! $counter) {
            throw new LogicException("The ID counter for role [{$role}] is missing. Run migrations first.");
        }

        $prefix = $formats[$role]['prefix'];
        $width = $formats[$role]['width'];
        $usedNumbers = DB::table('users')
            ->where('role', $role)
            ->whereNotNull('school_id')
            ->pluck('school_id')
            ->map(function ($schoolId) use ($prefix, $width) {
                $pattern = '/^' . preg_quote($prefix, '/') . '(\d{' . $width . '})$/';
                return preg_match($pattern, $schoolId, $matches) ? (int) $matches[1] : null;
            })
            ->filter()
            ->flip();

        // Reuse the first missing number (e.g. STU-000001 after deletion).
        $number = 1;
        while (isset($usedNumbers[$number])) {
            $number++;
        }

        DB::table('user_id_counters')
            ->where('role', $role)
            ->update(['next_number' => $number + 1, 'updated_at' => now()]);

        return $prefix . str_pad((string) $number, $width, '0', STR_PAD_LEFT);
    }
}
