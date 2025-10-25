<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'category' => $this->category->name,
            'instructor' => optional($this->instructor)->name,
            'average_rating' => round($this->average_rating, 1),
            'lessons_count' => $this->lessons->count(),
            'students_count' => $this->enrollments->count(),
            'created_at' => $this->created_at->format('Y-m-d'),

        ];
    }
}
