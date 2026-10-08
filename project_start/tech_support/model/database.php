<?php
    // Configure the MySQL database connection.
    $dsn = 'mysql:host=localhost;dbname=tech_support';
    $username = 'ts_user';
    $password = 'pa55word';
    // Connect to the database using PDO.
    try {
        $db = new PDO($dsn, $username, $password);
    // Handle database connection errors.
    } catch (PDOException $e) {
        $error_message = $e->getMessage();
        include('../errors/database_error.php');
        exit();
    }
?>