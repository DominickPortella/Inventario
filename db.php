<?php
$host = '127.0.0.1'; // Cambia localhost por 127.0.0.1 para evitar demoras
$db = 'db_obra';
$user = 'root';
$pass = 'admin11'; // Si en tu Workbench usas contraseña para entrar, ponla aquí. 
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
     PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
     PDO::ATTR_EMULATE_PREPARES => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int) $e->getCode());
}
?>