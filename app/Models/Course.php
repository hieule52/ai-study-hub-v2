<?php

namespace App\Models;

class Course
{
    public int $id;
    public int $teacher_id;
    public string $title;
    public ?string $description;
    public ?string $thumbnail;
    public float $price;
    public int $is_premium;
    public string $status;
    public int $total_lessons;

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = $data['id'] ?? 0;
            $this->teacher_id = $data['teacher_id'] ?? 0;
            $this->title = $data['title'] ?? '';
            $this->description = $data['description'] ?? null;
            $this->thumbnail = $data['thumbnail'] ?? null;
            $this->price = (float)($data['price'] ?? 0.0);
            $this->is_premium = $data['is_premium'] ?? 0;
            $this->status = $data['status'] ?? 'draft';
            $this->total_lessons = $data['total_lessons'] ?? 0;
        }
    }
}
