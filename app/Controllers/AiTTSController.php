<?php

namespace App\Controllers;

use App\Core\Controller;

class AiTTSController extends Controller
{
    public function index()
    {
        // Chỉ render view, không cần xử lý POST vì TTS chạy client-side
        return $this->view("ai/text-to-speech");
    }
}
