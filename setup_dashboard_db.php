<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$dbname = 'projek_basdat_dashboard';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbname'");
    if ($stmt->fetchColumn()) {
        echo "Database '$dbname' already exists.\n";
    } else {
        $pdo->exec("CREATE DATABASE $dbname");
        echo "Database '$dbname' created successfully.\n";
    }
    
    // Test connection
    $pdoDb = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    echo "Connection to '$dbname' successful.\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
