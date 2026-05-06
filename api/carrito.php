<?php
//iniciamos la sesión
session_start();

// instaciamos la clase Database y hacemos la conexión a la base de datos
$database = new Database();
$db = $database->conexionBaseDeDatos();

//obtenemos el metodo que se le pas (get, post, delete, etc)
$metodo = $_SERVER['REQUEST_METHOD'];

//hacemos un switch dependiendo de qué metodo se utiliza
switch($metodo){
    case 'GET':
        obtenerTodosLosProductos($db);
        break;
    case 'POST':
        insertarNuevoProducto($db);
        break;
    case 'PUT':
        actualizarUnidadesProducto($db);
        break;
    case 'DELETE':
        eliminarProducto($db);
        break;
}

//creamos la función para obtener todos los productos almacenados en la base de datos
function obtenerTodosLosProductos($db){

    //comprobamos que hay usuario logeado
    if (!isset($_SESSION['usuario']['id'])) {
        echo json_encode(['error' => 'El usuario no ha iniciado sesión']);
        return;
    }

    //obtenemos el id del usuario logeado
    $usuario_id = $_SESSION['usuario']['id'];

    //sentencia try-catch para que nos indique que ha fallado la insercción (en caso de que se dé)
    try{
        //seleccionamos a todos los productos que ha añadido al carrito el usuario
        $stmt = $db->prepare('SELECT pp.id, p.nombre, p.precio, p.descripcion, p.unidades, p.foto, pp.cantidad, (p.precio * pp.cantidad) AS total
            FROM productos_pedido pp
            JOIN producto p ON p.id = pp.producto_id
            JOIN pedido pd ON pd.id = pp.pedido_id
            WHERE pd.usuario_id = :ud AND pd.total = 0');
        $stmt->execute([':ud' => $usuario_id]);
        $carrito = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($carrito);

    } catch (PDOException $e){
        echo json_encode(['error' => 'Error al obtener los productos del carrito']);
    }
}

//creamos la función de insertar un nuevo producto dentro del carrito
function insertarNuevoProducto($db){

    //comprobamos que hay un usuario logueado
    if (!isset($_SESSION['usuario']['id'])) {
        echo json_encode(['error' => 'El usuario no ha iniciado sesión']);
        return;
    }

    //obtenemos el id del usuario
    $usuario_id = $_SESSION['usuario']['id'];

    $data = json_decode(file_get_contents('php://input'), true);

    $producto_id = $data['producto_id'] ?? null;
    $unidades_producto = $data['cantidad'] ?? 1;

    //para que nos de error si no llega
    if (!$producto_id){
        echo json_encode(['error' => 'El id del producto es necesario']);
        return;
    }

    //en caso de que no llegue nada, se introducirá solo una unidad
    if (!$unidades_producto){
        $unidades_producto = 1;
    }

    //necesitamos seleccionar el pedido que no todavía no esta finalizado, para ello, try-catch
    try {
        //buscamos el carrito 
        $stmt = $db->prepare('SELECT id FROM pedido WHERE usuario_id = :ud AND total = 0');
        $stmt->execute([':ud' => $usuario_id]);
        $pedido = $stmt->fetch();

        //si no se ha añadido ningún producto todavía, "creamos" el carrito
        if(!$pedido){
            $stmt = $db->prepare('INSERT INTO pedido (usuario_id, total) VALUES (:ud, 0)');
            $stmt->execute([':ud' => $usuario_id]);
            //obtenemos el id del carrito que acabamos de crear
            $pedido_id = $db->lastInsertId();
        } else {
            $pedido_id = $pedido['id'];
        }

        //comprobamos si el producto ya existe en nuestro carrito
        $stmt = $db->prepare('SELECT id, cantidad FROM productos_pedido WHERE pedido_id = :pd AND producto_id = :pi');
        $stmt->execute([
            ':pd' => $pedido_id,
            ':pi' => $producto_id
        ]);
        
        $comprobar_producto = $stmt->fetch();

        //en caso de que si exista, simplemente actualizamos las unidades
        if($comprobar_producto){
            $stmt = $db->prepare('UPDATE productos_pedido SET cantidad = cantidad + :ct WHERE id = :id');
            $stmt->execute([
                ':ct' => $unidades_producto,
                ':id' => $comprobar_producto['id']
            ]);
        //en caso de que no exista, añadimos la cantidad de unidades
        } else {
            $stmt = $db->prepare('INSERT INTO productos_pedido (pedido_id, producto_id, cantidad, precio)
                VALUES (:pd, :pi, :ct, 0)');
            $stmt->execute([
                ':pd' => $pedido_id,
                ':pi' => $producto_id,
                ':ct' => $unidades_producto
            ]);
        }

    } catch (PDOException $e){
        echo json_encode(['error' => 'Error al añadir el producto en el carrito']);
    }

    echo json_encode(['success' => 'Producto añadido al carrito']);

}

//creamos la funcion para actualizar los datos del producto
function actualizarUnidadesProducto($db){

    //obtenemos los datos que nos llegan
    $data = json_decode(file_get_contents('php://input'), true);

    //comprobamos que nos llegue el id
    if(empty($data['id'])){
        echo json_encode(['error' => 'ID requerido']);
        return;
    }

    //comprobamos que nos llegue las unidades del producto
    if(empty($data['cantidad'])){
        echo json_encode(['error' => 'La cantidad es requerida']);
        return;
    }

    //sentencia try-catch para que nos indique que ha fallado la insercción (en caso de que se dé)
    try{
        //actualizamos las unidades del producto
        $stmt = $db->prepare('UPDATE productos_pedido SET cantidad = :ct WHERE id = :id');
        $stmt->execute([
            ':ct' => $data['cantidad'],
            ':id' => $data['id']
        ]);
        
        echo json_encode(['success' => 'Las unidades del producto han sido actualizadas con éxito']);

    } catch (PDOException $e){
        echo json_encode(['error' => 'Error al actualizar las unidades del producto']);
    }

}

//creamos la función para eliminar un producto del carrito
function eliminarProducto($db){

    //obtenemos los datos que nos llegan
    $data = json_decode(file_get_contents('php://input'), true);

    //comprobamos que nos llegue el id
    if(empty($data['id'])){
        echo json_encode(['error' => 'ID requerido']);
        return;
    }

    //sentencia try-catch para que nos indique que ha fallado la insercción (en caso de que se dé)
    try{
        //eliminamos el producto con el id que nos llega
        $stmt = $db->prepare('DELETE FROM productos_pedido WHERE id = :id');
        $stmt->execute([':id' => $data['id']]);

        echo json_encode(['success' => 'El producto ha sido eliminado del carrito con éxito']);
    } catch (PDOException $e){
        echo json_encode(['error' => 'Error al eliminar el producto del carrito']);
    }

}
?>