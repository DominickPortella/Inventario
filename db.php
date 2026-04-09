<?php
$host = '127.0.0.1'; // Cambia esto por el host de tu servidor
$db = 'db_obra';
$user = 'root'; // Tu usuario de base de datos
$pass = 'admin'; // Tu contraseña de base de datos
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
     // En producción, no mostramos el mensaje $e->getMessage() por seguridad
     die("Error crítico: No se pudo conectar con el sistema de seguridad.");
}
?>