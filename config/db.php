<?php 
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$db_host = $_ENV['DB_HOST'] ?: 'localhost';
$db_name = $_ENV['DB_NAME'] ?: 'pms';
$db_user = $_ENV['DB_USER'] ?: 'root';
$db_pass = $_ENV['DB_PASS'];

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

   echo "<script>console.log('Connected to the database $db_name successfully!');</script>";

} catch (PDOException $e) {
    die("Could not connect to the database $db_name :" . $e->getMessage());
}
?>
