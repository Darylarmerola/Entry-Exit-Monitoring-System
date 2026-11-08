<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AttendanceController;

Route::get('/', fn () => auth()->check()
    ? redirect()->route(auth()->user()->role . '.dashboard')
    : view('welcome'))->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [RegistrationController::class, 'show'])->name('register');
    Route::post('/register', [RegistrationController::class, 'store'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/users/create', [AdminController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('users.destroy');
});

Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherController::class, 'index'])->name('dashboard');
    Route::get('/sessions/create', [TeacherController::class, 'createSession'])->name('sessions.create');
    Route::post('/sessions', [TeacherController::class, 'storeSession'])->name('sessions.store');
    Route::get('/sessions/{attendanceSession}', [TeacherController::class, 'showSession'])->name('sessions.show');
    Route::post('/sessions/{attendanceSession}/toggle', [TeacherController::class, 'toggleSession'])->name('sessions.toggle');
    Route::delete('/sessions/{attendanceSession}/attendances/{attendance}', [TeacherController::class, 'removeAttendance'])->name('attendances.remove');
    Route::get('/sessions/{attendanceSession}/export', [TeacherController::class, 'export'])->name('sessions.export');
});

Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'index'])->name('dashboard');
    Route::get('/scan', [StudentController::class, 'scan'])->name('scan');
    Route::post('/attendance/scan', [AttendanceController::class, 'scan'])->name('attendance.scan');
    Route::post('/attendance/time-out', [AttendanceController::class, 'timeOut'])->name('attendance.time-out');
});
