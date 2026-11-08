@extends('layouts.app')
@section('title','Log in')
@section('content')
<div class="row justify-content-center"><div class="col-md-5"><div class="card p-4"><h2 class="fw-bold mb-1">Welcome back</h2><p class="text-secondary">Log in to your attendance account.</p>
<form method="POST" action="{{ route('login.post') }}">@csrf
<div class="mb-3"><label class="form-label">Username</label><input name="username" class="form-control" value="{{ old('username') }}" required autofocus></div>
<div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
<div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="remember" value="1" id="remember"><label for="remember">Remember me</label></div>
<button class="btn btn-primary w-100">Log in</button></form><p class="mt-3 mb-0 text-center">No account? <a href="{{ route('register') }}">Register</a></p></div></div></div>
@endsection
