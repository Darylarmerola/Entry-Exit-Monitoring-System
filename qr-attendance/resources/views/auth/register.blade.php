@extends('layouts.app')
@section('title','Register')
@section('content')
<div class="row justify-content-center"><div class="col-lg-8"><div class="card p-4"><h2 class="fw-bold">Create an account</h2><p class="text-secondary">Register as a student or teacher. Administrator accounts are managed by an existing admin.</p>
<form method="POST" action="{{ route('register.post') }}">@csrf
<div class="row g-3">
<div class="col-md-6"><label class="form-label">Register as</label><select name="role" id="role" class="form-select" required><option value="student">Student</option><option value="teacher" @selected(old('role')==='teacher')>Teacher</option></select></div>
<div class="col-md-6" id="school-id-field"><label class="form-label">Student ID</label><input id="school_id" class="form-control" value="" readonly placeholder="Generated automatically after registration"><div class="form-text">Your ID will be generated automatically after registration.</div></div>
<div class="col-md-6"><label class="form-label">First name</label><input name="name" class="form-control" value="{{ old('name') }}" required></div>
<div class="col-md-6"><label class="form-label">Last name</label><input name="lastname" class="form-control" value="{{ old('lastname') }}" required></div>
<div class="col-md-6"><label class="form-label">Age</label><input type="number" min="1" max="120" name="age" class="form-control" value="{{ old('age') }}" required></div>
<div class="col-md-6 student-field"><label class="form-label">Course</label><input name="course" class="form-control" value="{{ old('course') }}"></div>
<div class="col-md-6 student-field"><label class="form-label">Gender</label><select name="gender" class="form-select"><option value="">Select</option>@foreach(['Male','Female','Other','Prefer not to say'] as $g)<option>{{ $g }}</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email') }}" required></div>
<div class="col-md-6"><label class="form-label">Username</label><input name="username" class="form-control" value="{{ old('username') }}" required></div>
<div class="col-md-6"><label class="form-label">Password (minimum 8 characters)</label><input type="password" name="password" class="form-control" required></div>
<div class="col-md-6"><label class="form-label">Confirm password</label><input type="password" name="password_confirmation" class="form-control" required></div>
</div><button class="btn btn-primary mt-4">Register</button></form></div></div></div>
@endsection
@push('scripts')
<script>const role=document.getElementById('role');function fields(){const isStudent=role.value==='student';document.querySelectorAll('.student-field').forEach(e=>e.style.display=isStudent?'block':'none');const idField=document.getElementById('school-id-field');idField.querySelector('label').textContent=isStudent?'Student ID':'Teacher ID';idField.querySelector('input').readOnly=true;idField.querySelector('input').required=false;idField.querySelector('input').placeholder=isStudent?'STU-000001, generated after registration':'TEA-00001, generated after registration';idField.querySelector('.form-text').textContent=isStudent?'A unique student ID will be generated automatically after registration.':'A unique teacher ID will be generated automatically after registration.'}role.addEventListener('change',fields);fields();</script>
@endpush
