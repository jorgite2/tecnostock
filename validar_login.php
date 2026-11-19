
<?php
// validar_login.php
require_once 'conexion.php';

// Iniciar sesión para guardar los datos del usuario
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.html');
    exit;
}

$correo = trim($_POST['correo'] ?? '');
$clave = $_POST['clave'] ?? '';

// 1. Buscar al usuario por su correo
$sql = 'SELECT id_usuario, nombre, correo, clave FROM usuario WHERE correo = :correo AND estado = 1';
$sentencia = $pdo->prepare($sql);
$sentencia->execute([':correo' => $correo]);
$usuario = $sentencia->fetch();

// 2. Validar que el usuario exista y que la contraseña coincida con el hash
if ($usuario && password_verify($clave, $usuario['clave'])) {
    // Guardar los datos en la sesión
    $_SESSION['id_usuario'] = $usuario['id_usuario'];
    $_SESSION['nombre_usuario'] = $usuario['nombre'];
    
    // Redirigir al listado de productos
    header('Location: listar_productos.php');
    exit;
} else {
    // Si falla, volver al login con un mensaje de error
    header('Location: login.html?error=1');
    exit;
}
?>