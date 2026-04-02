<?php

namespace App\Controllers;

use App\Core\Controller;

class GamesController extends Controller
{
    public function index()
    {
        // sau này có thể truyền data game / user
        $this->view('games/indexgames');
    }

    public function memory()
    {
        $this->view('games/memory/memory');
    }
    public function math()
    {
        $this->view('games/math/math');
    }
    public function speed()
    {
        $this->view('games/speed/speed');
    }
    public function samurai()
    {
    require __DIR__ . '/../Views/games/samurai/samurai.php';
    }

}
