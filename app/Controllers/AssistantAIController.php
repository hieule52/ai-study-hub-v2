<?php

namespace App\Controllers;

use App\Core\Controller;

class AssistantAIController extends Controller
{
    public function index()
    {
        return $this->view("assistantai");
    }

    public function chat()
    {
        return $this->view("ai/chat");
    }

  
}
