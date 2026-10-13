<?php

namespace Config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection  = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $host = 'localhost';
            $dbname = 'ReaDaLL';
            $username = 'root'; 
            $password = '';
            
   try {
        self:: $connection = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage() . PHP_EOL);
    }
   
}return self::$connection;
    }
}