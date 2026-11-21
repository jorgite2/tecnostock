
<?php
// conexion.php
$host = '127.0.0.1';
$port = '3307';
$baseDatos = 'tecnostock';
$usuario = 'root';
$clave = '1234';

$dsn = "mysql:host=$host;port=$port;dbname=$baseDatos;charset=utf8mb4";

$opciones = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $usuario, $clave, $opciones);
} catch (PDOException $error) {
    exit('No fue posible conectar con la base de datos.');
}
?>