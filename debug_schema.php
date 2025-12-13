<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=db_warungpadi', 'root', '');
    $stmt = $pdo->query('SHOW CREATE TABLE transaksis');
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    file_put_contents('schema_dump.txt', print_r($res, true));
    echo "Schema dump saved to schema_dump.txt\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
