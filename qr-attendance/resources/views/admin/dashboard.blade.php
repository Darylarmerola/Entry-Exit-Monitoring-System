@extends('layouts.app')

@section('title', 'Admin dashboard')

@section('content')

{{-- Dashboard heading and Add Account button. --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Admin dashboard</h2>
        <p class="text-secondary mb-0">Manage all registered accounts.</p>
    </div>

    <a class="btn btn-primary" href="{{ route('admin.users.create') }}">
        + Add account
    </a>
</div>

{{-- Account summary cards. --}}
<div class="row g-3 mb-4">

    {{-- Total number of registered accounts. --}}
    <div class="col-md-4">
        <div class="card p-3">
            <div class="text-secondary">Total accounts</div>
            <div class="fs-2 fw-bold">{{ $totalAccounts }}</div>
        </div>
    </div>

    {{-- Total number of teacher accounts. --}}
    <div class="col-md-4">
        <div class="card p-3">
            <div class="text-secondary">Teacher accounts</div>
            <div class="fs-2 fw-bold">{{ $teacherAccounts }}</div>
        </div>
    </div>

    {{-- Total number of student accounts. --}}
    <div class="col-md-4">
        <div class="card p-3">
            <div class="text-secondary">Student accounts</div>
            <div class="fs-2 fw-bold">{{ $studentAccounts }}</div>
        </div>
    </div>

</div>

{{-- Account list table. --}}
<div class="card p-3">
    <div class="table-responsive">
        <table class="table align-middle">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($users as $user)
                    <tr>
                        {{-- Admin accounts do not have a school ID. --}}
                        <td>{{ $user->school_id ?? '—' }}</td>

                        <td>{{ $user->name }} {{ $user->lastname }}</td>
                        <td>{{ $user->username }}</td>

                        <td>
                            <span class="badge text-bg-info">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>

                        <td>{{ $user->email }}</td>

                        <td>
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </td>

                        <td class="text-nowrap">

                            {{-- Edit account details. --}}
                            <a class="btn btn-sm btn-outline-primary"
                               href="{{ route('admin.users.edit', $user) }}">
                                Edit
                            </a>

                            {{-- Delete an account after confirmation. --}}
                            <form class="d-inline"
                                  method="POST"
                                  action="{{ route('admin.users.destroy', $user) }}"
                                  onsubmit="return confirm('Delete this account?')">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-sm btn-outline-danger">
                                    Delete
                                </button>
                            </form>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-secondary">
                            No accounts found.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>

    {{-- Pagination links. --}}
    {{ $users->links() }}
</div>

@endsection