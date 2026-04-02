<?php

namespace App\Controllers;

use App\Core\Controller;

class AiImagesController extends Controller
{
    public function index()
    {
        // Chỉ render view, image generation chạy client-side với Pollinations.ai API
        return $this->view("ai/image-generator");
    }
}
