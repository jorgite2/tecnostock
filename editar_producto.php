
<?php
// editar_producto.php
require_once 'conexion.php';

// 1. Verificar que venga el id_producto
$idProducto = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($idProducto === false || $idProducto === null) {
    header('Location: listar_productos.php');
    exit;
}

// 2. Buscar el producto en la base de datos
$sql = 'SELECT p.id_producto, p.codigo, p.nombre, p.descripcion, p.precio, 
               p.stock_actual, p.stock_minimo, p.id_categoria,
               c.nombre AS categoria_nombre
        FROM producto p
        INNER JOIN categoria c ON c.id_categoria = p.id_categoria
        WHERE p.id_producto = :id AND p.estado = 1';

$sentencia = $pdo->prepare($sql);
$sentencia->execute([':id' => $idProducto]);
$producto = $sentencia->fetch();

// 3. Si no existe, redirigir
if (!$producto) {
    header('Location: listar_productos.php');
    exit;
}

// 4. Obtener todas las categorías para el select
$categorias = $pdo->query('SELECT id_categoria, nombre FROM categoria WHERE estado = 1 ORDER BY nombre')->fetchAll();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar producto - TecnoStock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <main class="container py-5">
        <div class="card shadow-sm mx-auto" style="max-width: 760px;">
            <div class="card-body p-4">
                <h1 class="h3 mb-3">Editar producto</h1>
                <p class="text-secondary">Modifique los datos del producto.</p>

                <form action="actualizar_producto.php" method="post">
                    <!-- ID oculto -->
                    <input type="hidden" name="id_producto" value="<?= (int)$producto['id_producto'] ?>">

                    <div class="row g-3">
                        
                        <!-- Código (solo lectura) -->
                        <div class="col-md-4">
                            <label for="codigo" class="form-label">Código</label>
                            <input type="text" class="form-control" id="codigo" name="codigo" 
                                   value="<?= htmlspecialchars($producto['codigo'], ENT_QUOTES, 'UTF-8') ?>" 
                                   readonly>
                            <small class="text-muted">El código no se puede modificar.</small>
                        </div>

                        <!-- Nombre -->
                        <div class="col-md-8">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                   value="<?= htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8') ?>" 
                                   minlength="3" maxlength="100" required>
                        </div>

                        <!-- Categoría -->
                        <div class="col-md-6">
                            <label for="categoria_id" class="form-label">Categoría</label>
                            <select class="form-select" id="categoria_id" name="categoria_id" required>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= (int)$cat['id_categoria'] ?>" 
                                        <?= $cat['id_categoria'] == $producto['id_categoria'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nombre'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Precio -->
                        <div class="col-md-6">
                            <label for="precio" class="form-label">Precio</label>
                            <input type="number" class="form-control" id="precio" name="precio" 
                                   value="<?= (float)$producto['precio'] ?>" 
                                   min="0" step="0.01" required>
                        </div>

                        <!-- Stock actual -->
                        <div class="col-md-6">
                            <label for="stock_actual" class="form-label">Stock actual</label>
                            <input type="number" class="form-control" id="stock_actual" name="stock_actual" 
                                   value="<?= (int)$producto['stock_actual'] ?>" 
                                   min="0" step="1" required>
                        </div>

                        <!-- Stock mínimo -->
                        <div class="col-md-6">
                            <label for="stock_minimo" class="form-label">Stock mínimo</label>
                            <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" 
                                   value="<?= (int)$producto['stock_minimo'] ?>" 
                                   min="0" step="1" required>
                        </div>

                        <!-- Descripción -->
                        <div class="col-12">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" 
                                      rows="3" maxlength="255"><?= htmlspecialchars($producto['descripcion'], ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>

                        <!-- Botones -->
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Actualizar producto</button>
                            <a href="listar_productos.php" class="btn btn-outline-secondary">Cancelar</a>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </main>

</body>
</html>