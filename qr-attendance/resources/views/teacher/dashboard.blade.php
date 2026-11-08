@extends('layouts.app')
@section('title','Teacher dashboard')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4"><div><h2 class="fw-bold">Teacher dashboard</h2><p class="text-secondary mb-0">Create and manage attendance sessions.</p></div><a href="{{ route('teacher.sessions.create') }}" class="btn btn-primary">+ Create attendance</a></div>
<div class="card p-3 mb-4"><div class="text-secondary">Teacher ID</div><div class="fs-4 fw-bold">{{ auth()->user()->school_id }}</div></div>
<div class="card p-3"><h5 class="mb-3">Your attendance sessions</h5><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Date</th><th>Course</th><th>Subject</th><th>Attendees</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($sessions as $session)<tr><td>{{ $session->session_date->format('M d, Y') }}</td><td>{{ $session->course }}</td><td>{{ $session->subject }}</td><td>{{ $session->attendances_count }}</td><td>{{ $session->is_active?'Active':'Closed' }}</td><td><a class="btn btn-sm btn-outline-primary" href="{{ route('teacher.sessions.show',$session) }}">Manage</a></td></tr>@empty<tr><td colspan="6" class="text-center text-secondary">No sessions created yet.</td></tr>@endforelse
</tbody></table></div></div>
@endsection
