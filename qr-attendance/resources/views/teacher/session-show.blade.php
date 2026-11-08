@extends('layouts.app')
@section('title','Attendance session')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><div><h2 class="fw-bold mb-1">{{ $attendanceSession->subject }}</h2><p class="text-secondary">{{ $attendanceSession->course }} · {{ $attendanceSession->session_date->format('M d, Y') }}</p></div><a class="btn btn-outline-success" href="{{ route('teacher.sessions.export',$attendanceSession) }}">Download Excel CSV</a></div>
<div class="row g-3 mb-4">
<div class="col-md-4"><div class="card p-4 text-center"><h5>Attendance QR code</h5><div id="qrcode" class="d-flex justify-content-center my-3"></div><p class="small text-secondary">Students scan this QR using their student dashboard.</p><span class="badge {{ $attendanceSession->is_active?'text-bg-success':'text-bg-secondary' }}">{{ $attendanceSession->is_active?'Session active':'Session closed' }}</span>
<form method="POST" action="{{ route('teacher.sessions.toggle',$attendanceSession) }}" class="mt-3">@csrf<button class="btn btn-sm btn-outline-primary">{{ $attendanceSession->is_active?'Close session':'Reopen session' }}</button></form></div></div>
<div class="col-md-8"><div class="card p-4"><h5>Session information</h5><p class="mb-1"><b>Course:</b> {{ $attendanceSession->course }}</p><p class="mb-1"><b>Subject:</b> {{ $attendanceSession->subject }}</p><p class="mb-1"><b>Date:</b> {{ $attendanceSession->session_date->format('M d, Y') }}</p><p class="text-secondary small mb-0">When a student scans this QR code, their time-in is added to the attendance list. Students use the Time out button on their phone when they leave. This list refreshes automatically while the session is active.</p></div></div>
</div>
<div class="card p-3"><h5>Attendees ({{ $attendanceSession->attendances->count() }})</h5><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Student ID</th><th>Name</th><th>Course</th><th>Time in</th><th>Time out</th><th></th></tr></thead><tbody>
@forelse($attendanceSession->attendances as $a)<tr><td>{{ $a->student->school_id }}</td><td>{{ $a->student->name }} {{ $a->student->lastname }}</td><td>{{ $a->student->course }}</td><td>{{ $a->time_in?->format('h:i:s A') ?? '—' }}</td><td>{{ $a->time_out?->format('h:i:s A') ?? '—' }}</td><td><form method="POST" action="{{ route('teacher.attendances.remove',[$attendanceSession,$a]) }}" onsubmit="return confirm('Remove this attendee?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Remove</button></form></td></tr>@empty<tr><td colspan="6" class="text-center text-secondary">No students have scanned this session yet.</td></tr>@endforelse
</tbody></table></div></div>
@endsection
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>new QRCode(document.getElementById('qrcode'),{text:@json($qrPayload),width:220,height:220,colorDark:'#14213d',colorLight:'#ffffff',correctLevel:QRCode.CorrectLevel.H});
@if($attendanceSession->is_active)
// Refresh the teacher's attendance list so new student scans appear without manual reload.
window.setTimeout(() => window.location.reload(), 10000);
@endif
</script>
@endpush
