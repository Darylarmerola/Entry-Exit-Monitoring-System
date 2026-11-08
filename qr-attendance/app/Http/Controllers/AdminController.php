<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\DB;
use App\Services\UserIdGenerator;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(15);
        return view('admin.dashboard', compact('users'));
    }

    public function create() { return view('admin.user-form', ['user'=>new User()]); }
    public function edit(User $user) { return view('admin.user-form', compact('user')); }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        DB::transaction(function () use ($data) {
            if (in_array($data['role'], ['student', 'teacher'], true)) {
                $data['school_id'] = UserIdGenerator::next($data['role']);
            } else {
                $data['school_id'] = null;
            }
            User::create($data);
        });
        return redirect()->route('admin.dashboard')->with('success','Account created.');
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validated($request, $user);
        if (empty($data['password'])) unset($data['password']);
        DB::transaction(function () use ($data, $user) {
            if ($data['role'] !== $user->role) {
                $data['school_id'] = in_array($data['role'], ['student', 'teacher'], true)
                    ? UserIdGenerator::next($data['role'])
                    : null;
            }
            $user->update($data);
        });
        return redirect()->route('admin.dashboard')->with('success','Account updated.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $unique = fn($column) => Rule::unique('users',$column)->ignore($user?->id);
        return $request->validate([
            'role'=>['required',Rule::in(['admin','teacher','student'])],
            'name'=>['required','string','max:100'],
            'lastname'=>['required','string','max:100'],
            'age'=>['nullable','integer','min:1','max:120'],
            'course'=>['nullable','string','max:120'],
            'gender'=>['nullable',Rule::in(['Male','Female','Other','Prefer not to say'])],
            'email'=>['required','email','max:150',$unique('email')],
            'username'=>['required','alpha_dash','min:3','max:50',$unique('username')],
            'password'=>[$user?'nullable':'required','confirmed',Password::min(8)],
            'is_active'=>['sometimes','boolean'],
        ]);
    }

    public function destroy(User $user)
    {
        abort_if($user->id === auth()->id(), 422, 'You cannot delete your own account.');
        $user->delete();
        return back()->with('success','Account deleted.');
    }
}
