<?php
namespace App\Config;

use PDO;

class Database
{
    private static ?PDO $pdo = null;
    private static ?string $host = "localhost";
    private static ?string $dbname = "talent_hub";
    private static ?string $username = "root";
    private static ?string $password = "";

    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            self::$pdo = new PDO(
                "mysql:host=" . self::$host . ";dbname=" . self::$dbname . ";charset=utf8",
                self::$username,
                self::$password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]
            );
        }
        return self::$pdo;
    }
}
