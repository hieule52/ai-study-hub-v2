<?php

namespace App\Controllers;

use App\Core\Controller;

class AboutController extends Controller
{
    public function about()
    {
        return $this->view("about");
    }
}
