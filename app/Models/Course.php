<?php

namespace App\Models;

class Course
{
    public int $id;
    public int $teacher_id;
    public ?int $category_id;
    public string $level;
    public int $estimated_duration;
    public string $title;
    public ?string $description;
    public ?string $thumbnail;
    public float $price;
    public int $is_premium;
    public string $status;
    public int $total_lessons;

    public ?string $category_name;
    public ?string $category_slug;
    public ?string $teacher_name;
    public ?string $teacher_email;

    // AI Learning Context fields
    public ?string $ai_course_summary;
    public ?string $ai_keywords;
    public ?string $ai_focus;

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = $data['id'] ?? 0;
            $this->teacher_id = $data['teacher_id'] ?? 0;
            $this->category_id = $data['category_id'] ?? null;
            $this->level = $data['level'] ?? 'beginner';
            $this->estimated_duration = $data['estimated_duration'] ?? 0;
            $this->title = $data['title'] ?? '';
            $this->description = $data['description'] ?? null;
            $this->thumbnail = $data['thumbnail'] ?? null;
            $this->price = (float)($data['price'] ?? 0.0);
            $this->is_premium = $data['is_premium'] ?? 0;
            $this->status = $data['status'] ?? 'draft';
            $this->total_lessons = $data['total_lessons'] ?? 0;

            // Joined fields
            $this->category_name = $data['category_name'] ?? null;
            $this->category_slug = $data['category_slug'] ?? null;
            $this->teacher_name = $data['teacher_name'] ?? null;
            $this->teacher_email = $data['teacher_email'] ?? null;

            // AI Learning Context fields
            $this->ai_course_summary = $data['ai_course_summary'] ?? null;
            $this->ai_keywords = $data['ai_keywords'] ?? null;
            $this->ai_focus = $data['ai_focus'] ?? null;
        }
    }
}
