
<?php
// desactivar_producto.php
require_once 'conexion.php';

// 1. Verificar que venga el id_producto
$idProducto = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($idProducto === false || $idProducto === null) {
    header('Location: listar_productos.php');
    exit;
}

// 2. Verificar que el producto exista y esté activo
$sqlVerificar = 'SELECT id_producto, nombre, estado FROM producto WHERE id_producto = :id';
$sentencia = $pdo->prepare($sqlVerificar);
$sentencia->execute([':id' => $idProducto]);
$producto = $sentencia->fetch();

if (!$producto) {
    exit('El producto no existe. <a href="listar_productos.php">Volver</a>');
}

if ($producto['estado'] === 0) {
    exit('El producto ya está desactivado. <a href="listar_productos.php">Volver</a>');
}

// 3. Desactivar el producto (cambiar estado a 0)
$sqlUpdate = 'UPDATE producto SET estado = 0 WHERE id_producto = :id AND estado = 1';
$sentencia = $pdo->prepare($sqlUpdate);
$sentencia->execute([':id' => $idProducto]);

// 4. Redirigir al listado con mensaje de confirmación
header('Location: listar_productos.php?desactivado=1');
exit;
?>