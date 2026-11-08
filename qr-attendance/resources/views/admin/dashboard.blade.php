@extends('layouts.app')
@section('title','Admin dashboard')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4"><div><h2 class="fw-bold mb-1">Admin dashboard</h2><p class="text-secondary mb-0">Manage all registered accounts.</p></div><a class="btn btn-primary" href="{{ route('admin.users.create') }}">+ Add account</a></div>
<div class="row g-3 mb-4"><div class="col-md-4"><div class="card p-3"><div class="text-secondary">Total accounts</div><div class="fs-2 fw-bold">{{ $users->total() }}</div></div></div></div>
<div class="card p-3"><div class="table-responsive"><table class="table align-middle"><thead><tr><th>ID</th><th>Name</th><th>Username</th><th>Role</th><th>Email</th><th>Status</th><th>Actions</th></tr></thead><tbody>
@forelse($users as $user)<tr><td>{{ $user->school_id ?? '—' }}</td><td>{{ $user->name }} {{ $user->lastname }}</td><td>{{ $user->username }}</td><td><span class="badge text-bg-info">{{ $user->role }}</span></td><td>{{ $user->email }}</td><td>{{ $user->is_active?'Active':'Inactive' }}</td><td class="text-nowrap"><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.users.edit',$user) }}">Edit</a><form class="d-inline" method="POST" action="{{ route('admin.users.destroy',$user) }}" onsubmit="return confirm('Delete this account?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr>@empty<tr><td colspan="7" class="text-center text-secondary">No accounts found.</td></tr>@endforelse
</tbody></table></div>{{ $users->links() }}</div>
@endsection
