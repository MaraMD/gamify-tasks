<?php
// Test database connection to Hostinger VPS
// Replace with your actual credentials

$host = 'YOUR_VPS_IP';
$port = 3306;
$database = 'gamificacion';
$username = 'gamify_user';
$password = 'YourStrongPassword123!';

echo "Testing connection to {$host}:{$port}...\n";

try {
    $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    echo "✅ Connection successful!\n";
    echo "Server version: " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n";

    // Test query
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables in database: " . count($tables) . "\n";

} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage() . "\n";
    echo "\nTroubleshooting:\n";
    echo "1. Check firewall allows port 3306\n";
    echo "2. Verify MySQL bind-address is 0.0.0.0\n";
    echo "3. Check credentials are correct\n";
    echo "4. Ensure user has remote access privileges\n";
}
