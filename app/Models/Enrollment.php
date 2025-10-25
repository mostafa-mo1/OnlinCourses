<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'course_id',
        'enrolled_at',
    ];  
    // relationships here....\\
    public function instructor()
{
    return $this->belongsTo(User::class, 'user_id');
}

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
