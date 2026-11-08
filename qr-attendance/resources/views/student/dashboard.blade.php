@extends('layouts.app')
@section('title','Student dashboard')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4"><div><h2 class="fw-bold">Student dashboard</h2><p class="text-secondary mb-0">Welcome, {{ auth()->user()->name }} {{ auth()->user()->lastname }}.</p></div><a href="{{ route('student.scan') }}" class="btn btn-primary">Scan attendance QR</a></div>
<div class="row g-3 mb-4"><div class="col-md-4"><div class="card p-3"><div class="text-secondary">Student ID</div><div class="fs-4 fw-bold">{{ auth()->user()->school_id }}</div></div></div><div class="col-md-4"><div class="card p-3"><div class="text-secondary">Course</div><div class="fs-4 fw-bold">{{ auth()->user()->course ?? '—' }}</div></div></div><div class="col-md-4"><div class="card p-3"><div class="text-secondary">Email</div><div class="fw-bold">{{ auth()->user()->email }}</div></div></div></div>
<div class="card p-3"><h5>Attendance history</h5><div class="table-responsive"><table class="table"><thead><tr><th>Date</th><th>Subject</th><th>Course</th><th>Teacher</th><th>Time in</th><th>Time out</th></tr></thead><tbody>
@forelse($attendances as $a)<tr><td>{{ $a->session->session_date->format('M d, Y') }}</td><td>{{ $a->session->subject }}</td><td>{{ $a->session->course }}</td><td>{{ $a->session->teacher->name }} {{ $a->session->teacher->lastname }}</td><td>{{ $a->time_in?->format('h:i:s A') ?? '—' }}</td><td>{{ $a->time_out?->format('h:i:s A') ?? '—' }}</td></tr>@empty<tr><td colspan="6" class="text-center text-secondary">No attendance records yet.</td></tr>@endforelse
</tbody></table></div>{{ $attendances->links() }}</div>
@endsection
