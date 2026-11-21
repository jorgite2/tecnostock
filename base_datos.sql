
-- |BASE DE DATOS TECNOSTOCK| --

CREATE DATABASE IF NOT EXISTS tecnostock CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tecnostock;

-- Tabla de usuarios --
CREATE TABLE IF NOT EXISTS usuario 
(
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    clave VARCHAR(255) NOT NULL,
    estado TINYINT(1) DEFAULT 1
   
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de categorías --
CREATE TABLE IF NOT EXISTS categoria 
(
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(60) NOT NULL UNIQUE,
    descripcion VARCHAR(255),
    estado TINYINT(1) DEFAULT 1

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de productos --
CREATE TABLE IF NOT EXISTS producto
(
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255),
    precio DECIMAL(10,2) NOT NULL CHECK (precio >= 0),
    stock_actual INT NOT NULL DEFAULT 0 CHECK (stock_actual >= 0),
    stock_minimo INT NOT NULL DEFAULT 0 CHECK (stock_minimo >= 0),
    estado TINYINT(1) DEFAULT 1,
    id_categoria INT NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_producto_categoria FOREIGN KEY (id_categoria) 
        REFERENCES categoria(id_categoria)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de movimientos --
CREATE TABLE IF NOT EXISTS movimiento 
(
    id_movimiento INT AUTO_INCREMENT PRIMARY KEY,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tipo ENUM('entrada', 'salida') NOT NULL,
    cantidad INT NOT NULL CHECK (cantidad > 0),
    observacion VARCHAR(255),
    id_producto INT NOT NULL,
    id_usuario INT NOT NULL,
    CONSTRAINT fk_movimiento_producto FOREIGN KEY (id_producto) 
        REFERENCES producto(id_producto),
    CONSTRAINT fk_movimiento_usuario FOREIGN KEY (id_usuario) 
        REFERENCES usuario(id_usuario)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



-- |DATOS DE PRUEBA| --

-- Usuario por defecto (clave: 123456) --
INSERT INTO usuario (nombre, correo, clave, estado) VALUES 
('Encargado Inventario', 'encargado@tecnostock.cl', '123456', 1);

-- Categorías --
INSERT INTO categoria (nombre, descripcion, estado) VALUES 
('Accesorios', 'Cables, fundas, cargadores y similares', 1),
('Almacenamiento', 'Discos duros, pendrives, memorias', 1),
('Conectividad', 'Routers, switches, adaptadores de red', 1),
('Periféricos', 'Teclados, mouse, audífonos', 1),
('Audio', 'Parlantes, micrófonos, auriculares', 1);

-- Productos de ejemplo --
INSERT INTO producto (codigo, nombre, descripcion, precio, stock_actual, stock_minimo, estado, id_categoria) VALUES 
('TEC-001', 'Cable HDMI 2m', 'Cable HDMI alta velocidad 2 metros', 5990, 25, 10, 1, 1),
('TEC-002', 'Pendrive 32GB', 'Pendrive USB 3.0 32GB', 8990, 15, 5, 1, 2),
('TEC-003', 'Mouse inalámbrico', 'Mouse ergonómico inalámbrico', 12990, 8, 10, 1, 4),
('TEC-004', 'Teclado mecánico', 'Teclado mecánico RGB', 45990, 5, 3, 1, 4),
('TEC-005', 'Router WiFi 6', 'Router dual band WiFi 6', 89990, 3, 2, 1, 3),
('TEC-006', 'Audífonos Bluetooth', 'Audífonos inalámbricos con cancelación de ruido', 35990, 12, 5, 1, 5),
('TEC-007', 'Cargador USB-C', 'Cargador rápido USB-C 65W', 15990, 20, 8, 1, 1),
('TEC-008', 'Disco SSD 480GB', 'Disco sólido interno SATA', 42990, 7, 4, 1, 2),
('TEC-009', 'Switch 8 puertos', 'Switch Gigabit 8 puertos', 28990, 4, 2, 1, 3),
('TEC-010', 'Funda laptop 15"', 'Funda acolchada para laptop 15 pulgadas', 9990, 18, 6, 1, 1);

-- Movimientos de ejemplo --
INSERT INTO movimiento (tipo, cantidad, observacion, id_producto, id_usuario) VALUES 
('entrada', 25, 'Stock inicial', 1, 1),
('entrada', 15, 'Stock inicial', 2, 1),
('entrada', 8, 'Stock inicial', 3, 1),
('salida', 2, 'Venta a cliente', 1, 1),
('entrada', 10, 'Reposición de stock', 3, 1);
