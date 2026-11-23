<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserIdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    // Display the admin dashboard and account statistics.
    public function index()
    {
        // Show accounts in role order, then by lowest school ID first.
        // Admin accounts have no school ID, so they appear first.
        $users = User::orderByRaw("
            CASE
                WHEN role = 'admin' THEN 1
                WHEN role = 'teacher' THEN 2
                WHEN role = 'student' THEN 3
                ELSE 4
            END
        ")
        ->orderBy('school_id')
        ->orderBy('id')
        ->paginate(15);

        // Count all accounts, including admin accounts.
        $totalAccounts = User::count();

        // Count teacher accounts only.
        $teacherAccounts = User::where('role', 'teacher')->count();

        // Count student accounts only.
        $studentAccounts = User::where('role', 'student')->count();

        return view('admin.dashboard', compact(
            'users',
            'totalAccounts',
            'teacherAccounts',
            'studentAccounts'
        ));
    }

    // Open the form for creating a new account.
    public function create()
    {
        return view('admin.user-form', ['user' => new User()]);
    }

    // Open the form for editing an existing account.
    public function edit(User $user)
    {
        return view('admin.user-form', compact('user'));
    }

    // Save a newly created account.
    public function store(Request $request)
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data) {
            // Generate school IDs only for students and teachers.
            // Admin accounts do not receive a school ID.
            if (in_array($data['role'], ['student', 'teacher'], true)) {
                $data['school_id'] = UserIdGenerator::next($data['role']);
            } else {
                $data['school_id'] = null;
            }

            User::create($data);
        });

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Account created.');
    }

    // Update an existing account.
    public function update(Request $request, User $user)
    {
        $data = $this->validated($request, $user);

        // Keep the current password if the password field is blank.
        if (empty($data['password'])) {
            unset($data['password']);
        }

        DB::transaction(function () use ($data, $user) {
            // Generate an ID if the account changes to student or teacher.
            if ($data['role'] !== $user->role) {
                $data['school_id'] = in_array(
                    $data['role'],
                    ['student', 'teacher'],
                    true
                )
                    ? UserIdGenerator::next($data['role'])
                    : null;
            } else {
                // Keep the existing school ID if the role did not change.
                unset($data['school_id']);
            }

            $user->update($data);
        });

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Account updated.');
    }

    // Validate account information before saving it.
    private function validated(Request $request, ?User $user = null): array
    {
        $unique = fn($column) => Rule::unique('users', $column)
            ->ignore($user?->id);

        return $request->validate([
            'role' => ['required', Rule::in(['admin', 'teacher', 'student'])],
            'name' => ['required', 'string', 'max:100'],
            'lastname' => ['required', 'string', 'max:100'],
            'age' => ['nullable', 'integer', 'min:1', 'max:120'],
            'course' => ['nullable', 'string', 'max:120'],
            'gender' => [
                'nullable',
                Rule::in(['Male', 'Female', 'Other', 'Prefer not to say'])
            ],
            'email' => ['required', 'email', 'max:150', $unique('email')],
            'username' => ['required', 'alpha_dash', 'min:3', 'max:50', $unique('username')],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::min(8)],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }

    // Delete an account, but prevent the admin from deleting their own account.
    public function destroy(User $user)
    {
        abort_if(
            $user->id === auth()->id(),
            422,
            'You cannot delete your own account.'
        );

        $user->delete();

        return back()->with('success', 'Account deleted.');
    }
}