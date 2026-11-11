@extends('layouts.app')
@section('content')
<div class="hero mb-4"><span class="badge bg-light text-primary mb-3">Laravel 12 · QR-based</span><h1 class="display-5 fw-bold">Entry and Exit Monitoring System</h1><p class="lead">A simple web based attendance system using a QR code.</p><div class="d-flex gap-2"><a class="btn btn-light btn-lg" href="{{ route('login') }}">Log in</a><a class="btn btn-outline-light btn-lg" href="{{ route('register') }}">Create account</a></div></div>

@endsection
