
<?php
// listar_productos.php
require_once 'conexion.php';

// 1. Obtener el término de búsqueda (si existe)
$busqueda = $_GET['busqueda'] ?? '';

// 2. Construir la consulta SQL base
$sql = 'SELECT p.id_producto, p.codigo, p.nombre, c.nombre AS categoria, p.precio, p.stock_actual, p.stock_minimo
        FROM producto p
        INNER JOIN categoria c ON c.id_categoria = p.id_categoria
        WHERE p.estado = 1';

$params = [];

// 3. Si hay búsqueda, agregar filtros con LIKE
if ($busqueda !== '') {
    $sql .= ' AND (p.nombre LIKE :busqueda1 OR p.codigo LIKE :busqueda2 OR c.nombre LIKE :busqueda3)';
    $params[':busqueda1'] = '%' . $busqueda . '%';
    $params[':busqueda2'] = '%' . $busqueda . '%';
    $params[':busqueda3'] = '%' . $busqueda . '%';
}

$sql .= ' ORDER BY p.id_producto DESC';

// 4. Ejecutar la consulta de forma segura
$sentencia = $pdo->prepare($sql);
$sentencia->execute($params);
$productos = $sentencia->fetchAll();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Listado de productos - TecnoStock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="estilos.css" rel="stylesheet">
</head>
<body class="bg-light">

   <main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="h3 mb-0">Productos registrados</h1>
        <div class="d-flex gap-2 align-items-center">
            <a href="registrar_movimiento.html" class="btn btn-outline-success btn-sm">Registrar movimiento</a>
            <a href="index.html" class="btn btn-primary btn-sm">Registrar nuevo producto</a>
            <a href="cerrar_sesion.php" class="btn btn-outline-danger btn-sm">Cerrar sesión</a>
        </div>
    </div>

        <!-- Mensaje de éxito -->
        <?php if (isset($_GET['creado'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Producto guardado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Mensaje de desactivación -->
        <?php if (isset($_GET['desactivado'])): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                Producto desactivado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- BARRA DE BÚSQUEDA -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="listar_productos.php" method="get" class="row g-2 align-items-center">
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="busqueda" 
                               value="<?= htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8') ?>" 
                               placeholder="Buscar por nombre, código o categoría...">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-outline-primary flex-grow-1">Buscar</button>
                        <a href="listar_productos.php" class="btn btn-outline-secondary">Limpiar</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- TABLA DE PRODUCTOS -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($productos) === 0): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        No se encontraron productos.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($productos as $producto): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($producto['codigo'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($producto['categoria'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td>$<?= number_format((float)$producto['precio'], 0, ',', '.') ?></td>
                                        <td><?= (int)$producto['stock_actual'] ?></td>
                                        <td>
                                            <?php if ($producto['stock_actual'] < $producto['stock_minimo']): ?>
                                                <span class="badge bg-danger">Stock bajo</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Disponible</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="editar_producto.php?id=<?= (int)$producto['id_producto'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                            <a href="desactivar_producto.php?id=<?= (int)$producto['id_producto'] ?>" 
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('¿Seguro que desea desactivar este producto?');">
                                               Desactivar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
</body>
</html>