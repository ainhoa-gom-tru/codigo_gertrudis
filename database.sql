--  Creamos la base de datos 
CREATE DATABASE IF NOT EXISTS codigo_gertrudis;
USE codigo_gertrudis;

-- todos aquellos datos que vamos a hasear lo pondremos como un varchar de 255 caracteres

-- creamos la tabla de usuarios con los 4 roles (admin, desarrollador, compañera de piso, cliente)
CREATE TABLE IF NOT EXISTS usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(200) NOT NULL,
    usuario VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'desarrollador', 'compi', 'cliente') DEFAULT 'cliente',
    avatar VARCHAR(255) DEFAULT 'avatar1.png',
    validado BOOLEAN DEFAULT 0
);

-- creamos la tabla de productos
-- añadir la fecha de registro aquí nos va a ayudar a infromar al usuario de los nuevos productos
CREATE TABLE IF NOT EXISTS producto(
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(255) UNIQUE NOT NULL,
        descripcion TEXT NOT NULL,
        foto VARCHAR(255) NOT NULL,
        precio DECIMAL(10,2) NOT NULL,
        unidades INT NOT NULL DEFAULT 0,
        fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
);

-- creamos la tabla de pedidos
-- la fecha del pedido la vamos a necesitar para pasarlo a productos recibidos que ya se pueden valorar
CREATE TABLE IF NOT EXISTS pedido(
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        total DECIMAL(10,2) NOT NULL,
        fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        fecha_entrega DATE,
        FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE CASCADE
);

-- creamos una tabla para ir añadiendo todos los productos que seleccione el usuario al carrito
-- ponemos el unique para que si añadimos el mismo producto otra vez no aparezcan como dos diferentes
CREATE TABLE IF NOT EXISTS productos_pedido(
        id INT AUTO_INCREMENT PRIMARY KEY,
        pedido_id INT NOT NULL,
        producto_id INT NOT NULL,
        UNIQUE (producto_id, pedido_id),
        cantidad INT NOT NULL DEFAULT 1,
        precio DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (pedido_id) REFERENCES pedido(id) ON DELETE CASCADE,
        FOREIGN KEY (producto_id) REFERENCES producto(id) ON DELETE CASCADE
);

-- creamos una tabla para valorar los productos que ya han sido comprado por los usuarios
-- un usuario solo puede valorar un producto una vez
CREATE TABLE IF NOT EXISTS valoracion (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        producto_id INT NOT NULL,
        UNIQUE (usuario_id, producto_id),
        cliente_usuario VARCHAR(100) NOT NULL,
        puntuacion INT NOT NULL CHECK (puntuacion BETWEEN 1 AND 5),
        comentario TEXT,
        fecha_valoracion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE CASCADE,
        FOREIGN KEY (producto_id) REFERENCES producto(id) ON DELETE CASCADE
);

-- creamos una tabla para almacenar los juegos que vaya incorporando el desarrollador
CREATE TABLE IF NOT EXISTS juego(
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(150) NOT NULL UNIQUE,
        foto VARCHAR(255) NOT NULL,
        archivo VARCHAR(255) NOT NULL
);

-- creamos la tabla para almacenar las fotos que vayan subiendo las compañeras piso
CREATE TABLE IF NOT EXISTS galeria(
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        foto VARCHAR(255) NOT NULL,
        FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE CASCADE
);

-- creamos la tabla de los blogs
CREATE TABLE IF NOT EXISTS blog(
      id INT AUTO_INCREMENT PRIMARY KEY,
      usuario_id INT NOT NULL,
      titulo VARCHAR(100) NOT NULL,
      texto TEXT NOT NULL,
      categoria ENUM('arte', 'viajes', 'moda', 'música') DEFAULT 'música' NOT NULL,
      foto VARCHAR(255) NOT NULL,
      fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE CASCADE
);
