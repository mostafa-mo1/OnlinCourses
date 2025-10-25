<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'image',
        'category_id',
        'price',
        'user_id',
    ];
    // relationships here....\\
    public function category()
    {
        return $this->belongsTo(Category::class);   
    }
    // public function instructor()
    // {
    //     return $this->belongsTo(User::class, 'instructor_id');   
    // }
    public function instructor()
{
    return $this->belongsTo(User::class, 'user_id', 'id');
}

    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments');
    }
    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
