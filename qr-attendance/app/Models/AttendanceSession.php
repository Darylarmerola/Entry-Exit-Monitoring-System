<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceSession extends Model
{
    protected $fillable = ['teacher_id','course','subject','session_date','token','is_active'];

    protected function casts(): array
    {
        return ['session_date'=>'date','is_active'=>'boolean'];
    }

    public function teacher(): BelongsTo { return $this->belongsTo(User::class, 'teacher_id'); }
    public function attendances(): HasMany { return $this->hasMany(Attendance::class); }
}
