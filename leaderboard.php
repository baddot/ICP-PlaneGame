<?php
// leaderboard.php

// Database connection settings
$host = 'localhost';
$db = 'reports_db';
$user = 'ckuser';
$pass = 'lalapassword';
$charset = 'utf8mb4';

// Set up the database connection using PDO
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle GET request to fetch leaderboard data
    $stmt = $pdo->query('SELECT name, score FROM leaderboard ORDER BY score DESC LIMIT 10');
    $leaderboard = $stmt->fetchAll();
    
    // Set the content type to JSON and return the leaderboard data
    header('Content-Type: application/json');
    echo json_encode($leaderboard);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle POST request to update leaderboard data
    $name = $_POST['name'] ?? '';
    $score = $_POST['score'] ?? 0;

    if (!empty($name) && is_numeric($score)) {
        // Insert the new score into the leaderboard
        $stmt = $pdo->prepare('INSERT INTO leaderboard (name, score) VALUES (:name, :score)');
        $stmt->execute(['name' => $name, 'score' => $score]);

        // Return a success message
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Score submitted successfully.']);
    } else {
        // Return an error message if input is invalid
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
    }
}