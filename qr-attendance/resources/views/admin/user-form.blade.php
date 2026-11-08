@extends('layouts.app')
@section('title', $user->exists?'Edit account':'Add account')
@section('content')
<div class="card p-4"><h2 class="fw-bold">{{ $user->exists?'Edit account':'Add account' }}</h2>
<form method="POST" action="{{ $user->exists?route('admin.users.update',$user):route('admin.users.store') }}">@csrf @if($user->exists)@method('PUT')@endif
<div class="row g-3">
<div class="col-md-6"><label class="form-label">Account ID</label><input class="form-control" value="{{ old('school_id', $user->school_id) }}" readonly placeholder="Generated automatically after saving"><div class="form-text">Student and teacher IDs are generated automatically.</div></div>
@foreach(['name'=>'First name','lastname'=>'Last name','age'=>'Age','course'=>'Course','email'=>'Email','username'=>'Username'] as $field=>$label)
<div class="col-md-6"><label class="form-label">{{ $label }}</label><input name="{{ $field }}" class="form-control" value="{{ old($field,$user->$field) }}" {{ $field==='email'?'type=email':($field==='age'?'type=number':'') }}></div>
@endforeach
<div class="col-md-6"><label class="form-label">Role</label><select name="role" class="form-select">@foreach(['admin','teacher','student'] as $r)<option value="{{ $r }}" @selected(old('role',$user->role)===$r)>{{ ucfirst($r) }}</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label">Gender</label><select name="gender" class="form-select"><option value="">—</option>@foreach(['Male','Female','Other','Prefer not to say'] as $g)<option @selected(old('gender',$user->gender)===$g)>{{ $g }}</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label">Password {{ $user->exists?'(leave blank to keep current)':'' }}</label><input type="password" name="password" class="form-control" {{ $user->exists?'':'required' }}></div>
<div class="col-md-6"><label class="form-label">Confirm password</label><input type="password" name="password_confirmation" class="form-control"></div>
<div class="col-12 form-check ms-2"><input type="checkbox" class="form-check-input" name="is_active" value="1" @checked(old('is_active',$user->exists?$user->is_active:true))><label class="form-check-label">Account active</label></div>
</div><button class="btn btn-primary mt-4">Save account</button> <a class="btn btn-outline-secondary mt-4" href="{{ route('admin.dashboard') }}">Cancel</a></form></div>
@endsection
