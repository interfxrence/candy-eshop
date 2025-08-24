<?php
<?php
header('Content-Type: text/html; charset=utf-8');

echo "<h1>Database Connection Test</h1>";

$host = '';     
$dbname = 'sladkostiwzc6552';  
$username = 'sladkostiwzc6552';       
$password = '';  

echo "<p><strong>Connection parameters:</strong></p>";
echo "<ul>";
echo "<li>Host: $host</li>";
echo "<li>Database: $dbname</li>";
echo "<li>Username: $username</li>";
echo "<li>Password: " . (empty($password) ? '(empty)' : '(set)') . "</li>";
echo "</ul>";

try {
    echo "<p>Trying to connect...</p>";
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color: green;'><strong>✅ CONNECTION SUCCESSFUL!</strong></p>";
    
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<p><strong>Found tables:</strong></p>";
    if (count($tables) > 0) {
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
        
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM Product");
            $count = $stmt->fetch()['count'];
            echo "<p>Number of products in the database: <strong>$count</strong></p>";
        } catch (Exception $e) {
            echo "<p style='color: orange;'>The Product table exists, but there is a problem: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ No tables found! You need to run create.sql and insert.sql</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'><strong>❌ CONNECTION ERROR:</strong></p>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
    
    echo "<h3>Possible solutions:</h3>";
    echo "<ol>";
    echo "<li>Check the database name in your hosting control panel</li>";
    echo "<li>Check the MySQL username and password</li>";
    echo "<li>Make sure the database is created</li>";
    echo "<li>Check that the MySQL server is running</li>";
    echo "</ol>";
}

echo "<hr>";
echo "<p><small>File: " . __FILE__ . "</small></p>";
echo "<p><small>Time: " . date('Y-m-d H:i:s') . "</small></p>";