<?php
$logFile = __DIR__ . '/tmp_db_check.log';
file_put_contents($logFile, "start\n");

$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=laundry_real_db', 'root', '');
file_put_contents($logFile, "connected\n", FILE_APPEND);

foreach ($pdo->query('SHOW PROCESSLIST') as $row) {
    file_put_contents($logFile, $row['Id'] . ' ' . $row['Command'] . ' ' . $row['State'] . "\n", FILE_APPEND);
}

file_put_contents($logFile, "before count\n", FILE_APPEND);
file_put_contents($logFile, 'users=' . $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn() . "\n", FILE_APPEND);
