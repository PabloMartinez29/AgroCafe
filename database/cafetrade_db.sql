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
('Juan Pérez', 'campesino@cafetrade.com', 'campesino123', '+57 300 123 4567', 'Finca La Esperanza, Huila', 'campesino'),
('María González', 'maria@email.com', 'maria123', '+57 301 234 5678', 'Finca El Paraíso, Nariño', 'campesino'),
('Carlos Rodríguez', 'carlos@email.com', 'carlos123', '+57 302 345 6789', 'Finca Los Andes, Cauca', 'campesino'),
('Ana Martínez', 'ana@email.com', 'ana123', '+57 303 456 7890', 'Finca San José, Tolima', 'campesino');

-- Tipos de café
INSERT INTO tipos_cafe (nombre, variedad, descripcion, precio_base, calidad) VALUES
('Café Supremo', 'arabica', 'Café de alta montaña con notas frutales y acidez balanceada', 12000.00, 'premium'),
('Café Especial', 'arabica', 'Café de calidad especial con proceso lavado', 10500.00, 'especial'),
('Café Tradicional', 'robusta', 'Café comercial de buena calidad para consumo masivo', 8500.00, 'comercial'),
('Café Orgánico', 'arabica', 'Café certificado orgánico de montaña', 13500.00, 'premium'),
('Café Excelso', 'arabica', 'Café de exportación con certificación de calidad', 11000.00, 'especial');

-- Cooperativas
INSERT INTO cooperativas (nombre, nit, telefono, email, direccion, representante_legal) VALUES
('Cooperativa San José', '900123456-1', '+57 1 234 5678', 'info@coopsanjose.com', 'Calle 10 #15-20, Bogotá', 'Carlos Rodríguez'),
('Exportadora Colombia', '900654321-2', '+57 1 987 6543', 'ventas@exportcol.com', 'Carrera 7 #32-45, Bogotá', 'Ana Martínez'),
('Tostadora Local', '900789123-3', '+57 1 555 0123', 'compras@tostadora.com', 'Avenida 19 #25-30, Medellín', 'Luis García'),
('Café Internacional', '900456789-4', '+57 1 777 8888', 'internacional@cafe.com', 'Zona Franca, Cartagena', 'Patricia López');

-- Compras (admin compra a campesinos)
INSERT INTO compras (campesino_id, tipo_cafe_id, cantidad, precio_kg, fecha_compra, estado) VALUES
(2, 1, 150.00, 12000.00, '2024-05-20', 'completada'),
(3, 2, 200.00, 10500.00, '2024-05-18', 'completada'),
(4, 3, 100.00, 8500.00, '2024-05-15', 'pendiente'),
(5, 4, 80.00, 13500.00, '2024-05-22', 'completada'),
(2, 5, 120.00, 11000.00, '2024-05-25', 'completada');

-- Ventas (admin vende a cooperativas)
INSERT INTO ventas (cooperativa_id, tipo_cafe_id, cantidad, precio_kg, fecha_venta, estado) VALUES
(1, 1, 200.00, 15000.00, '2024-05-22', 'completada'),
(2, 2, 180.00, 13000.00, '2024-05-20', 'completada'),
(3, 3, 100.00, 10000.00, '2024-05-18', 'pendiente'),
(4, 4, 90.00, 16000.00, '2024-05-25', 'completada'),
(1, 5, 150.00, 14000.00, '2024-05-27', 'completada');

-- Pagos
INSERT INTO pagos (venta_id, monto, metodo_pago, referencia, fecha_pago, estado) VALUES
(1, 3000000.00, 'transferencia', 'TRF-2024-001', '2024-05-23', 'completado'),
(2, 2340000.00, 'transferencia', 'TRF-2024-002', '2024-05-21', 'completado'),
(4, 1440000.00, 'transferencia', 'TRF-2024-003', '2024-05-26', 'completado'),
(5, 2100000.00, 'transferencia', 'TRF-2024-004', '2024-05-28', 'completado');

-- Facturas
INSERT INTO facturas (venta_id, numero_factura, fecha_factura, subtotal, impuestos, total, estado_pago, fecha_vencimiento) VALUES
(1, 'F001', '2024-05-22', 3000000.00, 570000.00, 3570000.00, 'pagada', '2024-06-22'),
(2, 'F002', '2024-05-20', 2340000.00, 444600.00, 2784600.00, 'pagada', '2024-06-20'),
(3, 'F003', '2024-05-18', 1000000.00, 190000.00, 1190000.00, 'pendiente', '2024-06-18'),
(4, 'F004', '2024-05-25', 1440000.00, 273600.00, 1713600.00, 'pagada', '2024-06-25'),
(5, 'F005', '2024-05-27', 2100000.00, 399000.00, 2499000.00, 'pagada', '2024-06-27');

-- Precios históricos para análisis
INSERT INTO precios_historicos (tipo_cafe_id, precio, fecha_precio, tipo_operacion) VALUES
-- Enero 2024
(1, 11500.00, '2024-01-15', 'compra'), (1, 14500.00, '2024-01-15', 'venta'),
(2, 10000.00, '2024-01-15', 'compra'), (2, 12500.00, '2024-01-15', 'venta'),
(3, 8000.00, '2024-01-15', 'compra'), (3, 9500.00, '2024-01-15', 'venta'),
-- Febrero 2024
(1, 11800.00, '2024-02-15', 'compra'), (1, 14800.00, '2024-02-15', 'venta'),
(2, 10200.00, '2024-02-15', 'compra'), (2, 12700.00, '2024-02-15', 'venta'),
(3, 8200.00, '2024-02-15', 'compra'), (3, 9700.00, '2024-02-15', 'venta'),
-- Marzo 2024
(1, 12000.00, '2024-03-15', 'compra'), (1, 15000.00, '2024-03-15', 'venta'),
(2, 10500.00, '2024-03-15', 'compra'), (2, 13000.00, '2024-03-15', 'venta'),
(3, 8500.00, '2024-03-15', 'compra'), (3, 10000.00, '2024-03-15', 'venta'),
-- Abril 2024
(1, 12200.00, '2024-04-15', 'compra'), (1, 15200.00, '2024-04-15', 'venta'),
(2, 10700.00, '2024-04-15', 'compra'), (2, 13200.00, '2024-04-15', 'venta'),
(3, 8700.00, '2024-04-15', 'compra'), (3, 10200.00, '2024-04-15', 'venta'),
-- Mayo 2024 (actual)
(1, 12000.00, '2024-05-15', 'compra'), (1, 15000.00, '2024-05-15', 'venta'),
(2, 10500.00, '2024-05-15', 'compra'), (2, 13000.00, '2024-05-15', 'venta'),
(3, 8500.00, '2024-05-15', 'compra'), (3, 10000.00, '2024-05-15', 'venta');

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

-- Crear vistas para consultas frecuentes

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
