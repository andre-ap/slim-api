<?php

declare(strict_types=1);

namespace App;

use PDO;

class Database
{
    public function getConnection(): PDO
    {
        $dns = "mysql:host=127.0.0.1;dbname=slim_products;charset=utf8";

        $pdo = new PDO($dns, 'root', 'root', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        return $pdo;
    }
}
