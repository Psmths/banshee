<?php
function db(): PDO
{
    static $pdo;

    if (!$pdo) {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=UTF8",
                DB_USERNAME,
                DB_PASSWORD,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_EMULATE_PREPARES => false, // Disable emulated prepared statements
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Set default fetch mode
                    PDO::ATTR_TIMEOUT => 3, // Set a timeout in seconds (adjust as needed)
                ]
            );
        } catch (PDOException $e) {
            // Handle database connection errors gracefully
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    return $pdo;
}
?>