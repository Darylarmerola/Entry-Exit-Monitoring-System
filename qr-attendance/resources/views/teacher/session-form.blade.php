@extends('layouts.app')
@section('title','Create attendance')
@section('content')
<div class="row justify-content-center"><div class="col-lg-7"><div class="card p-4"><h2 class="fw-bold">Create attendance session</h2><form method="POST" action="{{ route('teacher.sessions.store') }}">@csrf
<div class="mb-3"><label class="form-label">Course / Class</label><input name="course" class="form-control" placeholder="e.g. BSIT 2A" required></div>
<div class="mb-3"><label class="form-label">Subject</label><input name="subject" class="form-control" placeholder="e.g. Web Development" required></div>
<div class="mb-3"><label class="form-label">Date</label><input type="date" name="session_date" class="form-control" value="{{ now()->toDateString() }}" required></div>
<button class="btn btn-primary">Create session and QR</button></form></div></div></div>
@endsection
