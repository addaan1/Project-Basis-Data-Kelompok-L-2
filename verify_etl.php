<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$dbname = 'projek_basdat_dashboard';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SELECT * FROM fact_user_daily_metrics ORDER BY date DESC LIMIT 5");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($rows) . " rows in fact table.\n";
    foreach ($rows as $row) {
        echo "Date: {$row['date']}, User: {$row['user_id']}, Role: {$row['role']}, Income: {$row['total_income']}\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
