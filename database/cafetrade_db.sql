-- Base de datos para CaféTrade
-- Creación de la base de datos
CREATE DATABASE IF NOT EXISTS cafetrade_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cafetrade_db;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    direccion TEXT,
    rol ENUM('administrador', 'campesino') NOT NULL DEFAULT 'campesino',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE
);

-- Tabla de tipos de café
CREATE TABLE tipos_cafe (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    variedad ENUM('arabica', 'robusta') NOT NULL,
    descripcion TEXT,
    precio_base DECIMAL(10,2) NOT NULL,
    calidad ENUM('premium', 'especial', 'comercial') NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE
);

-- Tabla de cooperativas
CREATE TABLE cooperativas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    nit VARCHAR(20) UNIQUE NOT NULL,
    telefono VARCHAR(20),
    email VARCHAR(100),
    direccion TEXT,
    representante_legal VARCHAR(100),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE
);

-- Tabla de compras (admin compra a campesinos)
CREATE TABLE compras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campesino_id INT NOT NULL,
    tipo_cafe_id INT NOT NULL,
    cantidad DECIMAL(10,2) NOT NULL,
    precio_kg DECIMAL(10,2) NOT NULL,
    total DECIMAL(12,2) GENERATED ALWAYS AS (cantidad * precio_kg) STORED,
    fecha_compra DATE NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'completada', 'cancelada') DEFAULT 'pendiente',
    FOREIGN KEY (campesino_id) REFERENCES usuarios(id),
    FOREIGN KEY (tipo_cafe_id) REFERENCES tipos_cafe(id)
);

-- Tabla de ventas (admin vende a cooperativas/clientes)
CREATE TABLE ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cooperativa_id INT,
    cliente_nombre VARCHAR(150),
    tipo_cafe_id INT NOT NULL,
    cantidad DECIMAL(10,2) NOT NULL,
    precio_kg DECIMAL(10,2) NOT NULL,
    total DECIMAL(12,2) GENERATED ALWAYS AS (cantidad * precio_kg) STORED,
    fecha_venta DATE NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'completada', 'cancelada') DEFAULT 'pendiente',
    FOREIGN KEY (cooperativa_id) REFERENCES cooperativas(id),
    FOREIGN KEY (tipo_cafe_id) REFERENCES tipos_cafe(id)
);

-- Tabla de pagos
CREATE TABLE pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT NOT NULL,
    monto DECIMAL(12,2) NOT NULL,
    metodo_pago ENUM('transferencia', 'efectivo', 'cheque') NOT NULL,
    referencia VARCHAR(100),
    fecha_pago DATE NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'completado', 'fallido') DEFAULT 'completado',
    FOREIGN KEY (venta_id) REFERENCES ventas(id)
);

-- Tabla de facturas
CREATE TABLE facturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT NOT NULL,
    numero_factura VARCHAR(20) UNIQUE NOT NULL,
    fecha_factura DATE NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    impuestos DECIMAL(12,2) DEFAULT 0,
    total DECIMAL(12,2) NOT NULL,
    estado_pago ENUM('pendiente', 'pagada', 'vencida') DEFAULT 'pendiente',
    fecha_vencimiento DATE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (venta_id) REFERENCES ventas(id)
);

-- Tabla de análisis de precios (histórico)
CREATE TABLE precios_historicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_cafe_id INT NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    fecha_precio DATE NOT NULL,
    tipo_operacion ENUM('compra', 'venta') NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tipo_cafe_id) REFERENCES tipos_cafe(id)
);

-- Insertar datos de prueba

-- Usuarios administrador y campesinos
INSERT INTO usuarios (nombre, email, password, telefono, direccion, rol) VALUES
('Administrador Sistema', 'admin@cafetrade.com', 'admin123', '+57 1 234 5678', 'Oficina Central Bogotá', 'administrador'),

-- Crear índices para optimizar consultas
CREATE INDEX idx_usuarios_email ON usuarios(email);
CREATE INDEX idx_usuarios_rol ON usuarios(rol);
CREATE INDEX idx_compras_campesino ON compras(campesino_id);
CREATE INDEX idx_compras_fecha ON compras(fecha_compra);
CREATE INDEX idx_ventas_cooperativa ON ventas(cooperativa_id);
CREATE INDEX idx_ventas_fecha ON ventas(fecha_venta);
CREATE INDEX idx_pagos_venta ON pagos(venta_id);
CREATE INDEX idx_facturas_venta ON facturas(venta_id);
CREATE INDEX idx_precios_fecha ON precios_historicos(fecha_precio);


-- Vista de ventas con detalles
CREATE VIEW vista_ventas_detalle AS
SELECT 
    v.id,
    v.fecha_venta,
    COALESCE(c.nombre, v.cliente_nombre) as cliente,
    tc.nombre as tipo_cafe,
    tc.variedad,
    v.cantidad,
    v.precio_kg,
    v.total,
    v.estado,
    f.numero_factura,
    f.estado_pago
FROM ventas v
LEFT JOIN cooperativas c ON v.cooperativa_id = c.id
LEFT JOIN tipos_cafe tc ON v.tipo_cafe_id = tc.id
LEFT JOIN facturas f ON v.id = f.venta_id;

-- Vista de compras con detalles
CREATE VIEW vista_compras_detalle AS
SELECT 
    c.id,
    c.fecha_compra,
    u.nombre as campesino,
    u.telefono,
    tc.nombre as tipo_cafe,
    tc.variedad,
    c.cantidad,
    c.precio_kg,
    c.total,
    c.estado
FROM compras c
JOIN usuarios u ON c.campesino_id = u.id
JOIN tipos_cafe tc ON c.tipo_cafe_id = tc.id;

-- Vista de análisis de precios
CREATE VIEW vista_analisis_precios AS
SELECT 
    tc.nombre as tipo_cafe,
    tc.variedad,
    ph.precio,
    ph.fecha_precio,
    ph.tipo_operacion,
    YEAR(ph.fecha_precio) as año,
    MONTH(ph.fecha_precio) as mes
FROM precios_historicos ph
JOIN tipos_cafe tc ON ph.tipo_cafe_id = tc.id
ORDER BY ph.fecha_precio DESC;
