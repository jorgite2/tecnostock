
<?php
// procesar_movimiento.php
require_once 'conexion.php';

// 1. Verificar que el método sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

// 2. Recibir los datos del formulario
$tipo = trim($_POST['tipo'] ?? '');
$productoId = filter_input(INPUT_POST, 'producto_id', FILTER_VALIDATE_INT);
$cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT);
$observacion = trim($_POST['observacion'] ?? '');
$usuarioId = 1; // Por ahora fijo, después vendrá de la sesión del login

// 3. Validar los datos
$errores = [];

if (!in_array($tipo, ['entrada', 'salida'])) {
    $errores[] = 'Tipo de movimiento inválido.';
}

if ($productoId === false || $productoId === null || $productoId < 1) {
    $errores[] = 'Producto inválido.';
}

if ($cantidad === false || $cantidad === null || $cantidad < 1) {
    $errores[] = 'Cantidad debe ser un número entero mayor a cero.';
}

// 4. Si hay errores de validación, mostrarlos
if ($errores) {
    http_response_code(422);
    echo '<h1>Revise los datos</h1><ul>';
    foreach ($errores as $error) {
        echo '<li>' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</li>';
    }
    echo '</ul>';
    echo '<a href="registrar_movimiento.html">Volver</a>';
    exit;
}

// 5. Verificar que el producto existe y está activo
$sqlProducto = 'SELECT id_producto, nombre, stock_actual, estado FROM producto WHERE id_producto = :id';
$sentencia = $pdo->prepare($sqlProducto);
$sentencia->execute([':id' => $productoId]);
$producto = $sentencia->fetch();

if (!$producto) {
    exit('El producto no existe. <a href="registrar_movimiento.html">Volver</a>');
}

if ($producto['estado'] !== 1) {
    exit('El producto está desactivado. <a href="registrar_movimiento.html">Volver</a>');
}

// 6. Validar que haya stock suficiente si es una salida
if ($tipo === 'salida' && $producto['stock_actual'] < $cantidad) {
    exit('No hay stock suficiente. Stock actual: ' . $producto['stock_actual'] . 
         ', cantidad solicitada: ' . $cantidad . 
         '. <a href="registrar_movimiento.html">Volver</a>');
}

// 7. Calcular el nuevo stock
$nuevoStock = $tipo === 'entrada' 
    ? $producto['stock_actual'] + $cantidad 
    : $producto['stock_actual'] - $cantidad;

// 8. Ejecutar todo dentro de una transacción
try {
    // Iniciar transacción
    $pdo->beginTransaction();

    // 8.1 Insertar el movimiento en la tabla movimiento
    $sqlMovimiento = 'INSERT INTO movimiento (tipo, cantidad, observacion, id_producto, id_usuario) 
                      VALUES (:tipo, :cantidad, :observacion, :id_producto, :id_usuario)';
    $sentenciaMov = $pdo->prepare($sqlMovimiento);
    $sentenciaMov->execute([
        ':tipo' => $tipo,
        ':cantidad' => $cantidad,
        ':observacion' => $observacion !== '' ? $observacion : null,
        ':id_producto' => $productoId,
        ':id_usuario' => $usuarioId,
    ]);

    // 8.2 Actualizar el stock del producto
    $sqlUpdateStock = 'UPDATE producto SET stock_actual = :nuevo_stock WHERE id_producto = :id_producto';
    $sentenciaUpdate = $pdo->prepare($sqlUpdateStock);
    $sentenciaUpdate->execute([
        ':nuevo_stock' => $nuevoStock,
        ':id_producto' => $productoId,
    ]);

    // Confirmar la transacción (todo salió bien)
    $pdo->commit();

    // 9. Redirigir al listado con mensaje de éxito
    header('Location: listar_productos.php?movimiento=1&tipo=' . $tipo);
    exit;

} catch (PDOException $error) {
    // Si algo falló, deshacer todo
    $pdo->rollBack();
    exit('No fue posible registrar el movimiento. Error: ' . $error->getMessage() . 
         ' <a href="registrar_movimiento.html">Volver</a>');
}
?>