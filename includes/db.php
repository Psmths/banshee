<?php
function db(): PDO
    {
        static $pdo;

        if (!$pdo) {
            return new PDO(
                sprintf(
                    "mysql:host=%s;dbname=%s;charset=UTF8", 
                    DB_SERVER, 
                    DB_NAME
                ),
                DB_USERNAME,
                DB_PASSWORD,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        }
        return $pdo;
    }
?>