<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $attendances = auth()->user()->attendances()->with('session.teacher')->latest()->paginate(15);
        return view('student.dashboard', compact('attendances'));
    }

    public function scan(Request $request)
    {
        return view('student.scan', ['token'=>$request->query('token','')]);
    }
}
