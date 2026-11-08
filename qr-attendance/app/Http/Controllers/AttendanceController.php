<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceSession;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /** Record the student's time-in when they scan the teacher's active QR code. */
    public function scan(Request $request)
    {
        $data = $request->validate(['token' => ['required', 'string', 'size:48']]);

        $session = AttendanceSession::where('token', $data['token'])
            ->where('is_active', true)
            ->first();

        if (! $session) {
            return response()->json([
                'message' => 'This QR code is invalid or the attendance session is closed.',
            ], 422);
        }

        $now = now('Asia/Manila');
        $attendance = Attendance::firstOrCreate(
            [
                'attendance_session_id' => $session->id,
                'student_id' => auth()->id(),
            ],
            ['time_in' => $now]
        );

        if ($attendance->wasRecentlyCreated) {
            return response()->json([
                'status' => 'timed_in',
                'can_time_out' => true,
                'time_in' => $attendance->time_in?->timezone('Asia/Manila')->format('h:i:s A'),
                'message' => 'Time-in recorded successfully. You are now on the attendance list. Use the Time out button when you are leaving.',
            ]);
        }

        if ($attendance->time_out) {
            return response()->json([
                'status' => 'completed',
                'can_time_out' => false,
                'time_in' => $attendance->time_in?->timezone('Asia/Manila')->format('h:i:s A'),
                'time_out' => $attendance->time_out->timezone('Asia/Manila')->format('h:i:s A'),
                'message' => 'Your time-in and time-out have already been recorded for this session.',
            ]);
        }

        return response()->json([
            'status' => 'already_timed_in',
            'can_time_out' => true,
            'time_in' => $attendance->time_in?->timezone('Asia/Manila')->format('h:i:s A'),
            'message' => 'Your time-in is already recorded. Press Time out when you are leaving.',
        ]);
    }

    /** Record time-out only after the student explicitly presses the button. */
    public function timeOut(Request $request)
    {
        $data = $request->validate(['token' => ['required', 'string', 'size:48']]);

        // Do not require the session to remain active: a teacher may close it
        // after class while students are still signing out.
        $session = AttendanceSession::where('token', $data['token'])->first();

        if (! $session) {
            return response()->json(['message' => 'The attendance session could not be found.'], 404);
        }

        $attendance = Attendance::where('attendance_session_id', $session->id)
            ->where('student_id', auth()->id())
            ->first();

        if (! $attendance) {
            return response()->json([
                'message' => 'You must scan the teacher QR code and record your time-in before timing out.',
            ], 422);
        }

        if ($attendance->time_out) {
            return response()->json([
                'status' => 'completed',
                'can_time_out' => false,
                'time_out' => $attendance->time_out->timezone('Asia/Manila')->format('h:i:s A'),
                'message' => 'Your time-out has already been recorded.',
            ]);
        }

        $attendance->update(['time_out' => now('Asia/Manila')]);
        $attendance->refresh();

        return response()->json([
            'status' => 'completed',
            'can_time_out' => false,
            'time_out' => $attendance->time_out?->timezone('Asia/Manila')->format('h:i:s A'),
            'message' => 'Time-out recorded successfully. Have a great day!',
        ]);
    }
}
