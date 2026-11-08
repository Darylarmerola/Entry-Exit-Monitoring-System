<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
@vite(['resources/css/app.css', 'resources/js/app.js'])
<title>@yield('title','QR Attendance') · QR Attendance</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{background:#f4f7fb;color:#172033}.navbar{background:#14213d}.navbar-brand,.nav-link{color:#fff!important}
.card{border:0;border-radius:16px;box-shadow:0 6px 24px #1720330c}.btn-primary{background:#2457c5;border-color:#2457c5}
.hero{background:linear-gradient(120deg,#14213d,#2457c5);color:white;border-radius:22px;padding:3rem}
.table thead th{color:#65718a;font-size:.82rem;text-transform:uppercase}
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg mb-4"><div class="container">
<a class="navbar-brand fw-bold" href="{{ route('home') }}">QR Attendance</a>
<div class="ms-auto d-flex gap-3 align-items-center">
@if(auth()->check())
<span class="text-white small">{{ auth()->user()->name }} · {{ ucfirst(auth()->user()->role) }}</span>
<form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-sm btn-outline-light">Log out</button></form>
@else
<a class="nav-link" href="{{ route('login') }}">Log in</a><a class="btn btn-sm btn-light" href="{{ route('register') }}">Register</a>
@endif
</div></div></nav>
<main class="container pb-5">
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@yield('content')
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body></html>
