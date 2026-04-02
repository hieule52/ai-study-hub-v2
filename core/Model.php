<?php

namespace App\Core;

use App\Core\Database;

class Model
{
    protected $db;
    protected $table = "";

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function query($sql, $params = [])
    {
        $stmt = $this->db->prepare($sql); 
        $stmt->execute($params);
        return $stmt;
    }
}
