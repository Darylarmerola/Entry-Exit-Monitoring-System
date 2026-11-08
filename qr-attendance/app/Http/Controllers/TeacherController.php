<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeacherController extends Controller
{
    public function index()
    {
        $sessions = auth()->user()->attendanceSessions()->withCount('attendances')->latest()->get();
        return view('teacher.dashboard', compact('sessions'));
    }

    public function createSession() { return view('teacher.session-form'); }

    public function storeSession(Request $request)
    {
        $data = $request->validate([
            'course'=>['required','string','max:120'],
            'subject'=>['required','string','max:120'],
            'session_date'=>['required','date'],
        ]);
        $session = auth()->user()->attendanceSessions()->create($data + [
            'token'=>Str::random(48), 'is_active'=>true
        ]);
        return redirect()->route('teacher.sessions.show',$session)->with('success','Attendance session created.');
    }

    public function showSession(AttendanceSession $attendanceSession)
    {
        $this->authorizeSession($attendanceSession);
        $attendanceSession->load('attendances.student');
        $scanUrl = route('student.scan');
        $qrPayload = route('student.scan').'?token='.$attendanceSession->token;
        return view('teacher.session-show', compact('attendanceSession','scanUrl','qrPayload'));
    }

    public function toggleSession(AttendanceSession $attendanceSession)
    {
        $this->authorizeSession($attendanceSession);
        $attendanceSession->update(['is_active'=>!$attendanceSession->is_active]);
        return back()->with('success','Session status updated.');
    }

    public function removeAttendance(AttendanceSession $attendanceSession, Attendance $attendance)
    {
        $this->authorizeSession($attendanceSession);
        abort_unless($attendance->attendance_session_id === $attendanceSession->id,404);
        $attendance->delete();
        return back()->with('success','Attendee removed.');
    }

    public function export(AttendanceSession $attendanceSession)
    {
        $this->authorizeSession($attendanceSession);
        $rows = $attendanceSession->attendances()->with('student')->get();
        $filename = 'attendance-session-'.$attendanceSession->id.'.csv';
        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output','w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out,['Student ID','Name','Last name','Course','Time in','Time out']);
            foreach ($rows as $row) {
                fputcsv($out,[$row->student->school_id,$row->student->name,$row->student->lastname,
                    $row->student->course,$row->time_in?->format('Y-m-d H:i:s'),$row->time_out?->format('Y-m-d H:i:s')]);
            }
            fclose($out);
        },$filename,['Content-Type'=>'text/csv; charset=UTF-8']);
    }

    private function authorizeSession(AttendanceSession $session): void
    {
        abort_unless($session->teacher_id === auth()->id(),403);
    }
}
