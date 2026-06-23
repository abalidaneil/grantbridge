<?php
session_start();

$host = "localhost";
$dbname = "grantbridge";
$username = "root";
$password = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ],
    );

    // leaving this commented out since i will export the whole db
    // $pdo->exec("CREATE TABLE IF NOT EXISTS applications (
    //     id INT AUTO_INCREMENT PRIMARY KEY,
    //     user_id INT NOT NULL,
    //     grant_id INT NOT NULL,
    //     amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    //     document_path VARCHAR(255) DEFAULT NULL,
    //     notes TEXT DEFAULT NULL,
    //     status VARCHAR(50) NOT NULL DEFAULT 'submitted',
    //     submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    // ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // $required_columns = [
    //     'user_id' => "ALTER TABLE applications ADD COLUMN user_id INT NOT NULL",
    //     'grant_id' => "ALTER TABLE applications ADD COLUMN grant_id INT NOT NULL",
    //     'amount' => "ALTER TABLE applications ADD COLUMN amount DECIMAL(10,2) NOT NULL DEFAULT 0",
    //     'document_path' => "ALTER TABLE applications ADD COLUMN document_path VARCHAR(255) DEFAULT NULL",
    //     'notes' => "ALTER TABLE applications ADD COLUMN notes TEXT DEFAULT NULL",
    //     'status' => "ALTER TABLE applications ADD COLUMN status VARCHAR(50) NOT NULL DEFAULT 'submitted'",
    //     'submitted_at' => "ALTER TABLE applications ADD COLUMN submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
    // ];

    // foreach ($required_columns as $column => $statement) {
    //     try {
    //         $column_check = $pdo->query("SHOW COLUMNS FROM applications LIKE '$column'");
    //         if ($column_check->fetch() === false) {
    //             $pdo->exec($statement);
    //         }
    //     } catch (PDOException $e) {
    //         // Ignore duplicate column errors if the table already has the column.
    //     }
    // }
} catch (PDOException $e) {
    die(
        "Database connection failed. Please import grantbridge.sql into phpMyAdmin first."
    );
}
