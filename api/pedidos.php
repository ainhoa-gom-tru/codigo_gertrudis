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
        realizarPedido($db);
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
        $stmt = $db->prepare('SELECT 
                pd.id AS pedido_id,
                pd.usuario_id,
                pd.total AS total_pedido,
                pd.fecha_pedido,
                pd.fecha_entrega,
                p.nombre,
                p.precio,
                p.foto,
                pp.cantidad
            FROM productos_pedido pp
            JOIN producto p ON p.id = pp.producto_id
            JOIN pedido pd ON pd.id = pp.pedido_id
            WHERE pd.usuario_id = :ud 
            AND pd.total > 0
        ');
        $stmt->execute([':ud' => $usuario_id]);
        $carrito = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($carrito);

    } catch (PDOException $e){
        echo json_encode(['error' => 'Error al obtener los productos del carrito']);
    }
}

//funcion en la que confirmamos el pedido
function realizarPedido($db){

    //obtenemos los datos que nos llegan
    $data = json_decode(file_get_contents('php://input'), true);

    //comprobamos que hay un usuario logueado
    if (!isset($_SESSION['usuario']['id'])) {
        echo json_encode(['error' => 'El usuario no ha iniciado sesión']);
        return;
    }

    //obtenemos el id del usuario
    $usuario_id = $_SESSION['usuario']['id'];

    //sentencia try-catch
    try{

        $db->beginTransaction();

        //obtenemos el carrito
        $stmt = $db->prepare('SELECT id FROM pedido WHERE usuario_id = :ud AND total = 0');
        $stmt->execute([':ud' => $usuario_id]);
        $carrito = $stmt->fetch();

        //en caso de que no haya carrito
        if (!$carrito){
            $db->rollBack();
            echo json_encode(['error' => 'No hay ningún carrito']);
            return;
        }

        //obtenemos el id del pedido
        $pedido_id = $carrito['id'];

        //Obtenemos los productos del carrito
        $stmt = $db->prepare('SELECT pp.producto_id, pp.cantidad, p.precio, p.unidades
        FROM productos_pedido pp
        JOIN producto p ON p.id = pp.producto_id
        WHERE pp.pedido_id = :pd');
        $stmt->execute([':pd' => $pedido_id]);
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //asignamos 0 al total
        $total = 0;

        //recorremos todos los productos
        foreach($productos as $producto){

            //comprobamos que queden unidades del producto
            if($producto['unidades'] < $producto['cantidad']){
                $db->rollBack();
                echo json_encode(['error' => 'No hay suficientes unidades de este producto']);
                return;
            }

            //guardamos el precio del producto (en caso de que haya sufrido modificaciones)
            $stmt = $db->prepare('UPDATE productos_pedido SET precio = :p WHERE pedido_id = :pd AND producto_id = :pi');
            $stmt->execute([
                ':p' => $producto['precio'],
                ':pd' => $pedido_id,
                ':pi' => $producto['producto_id']
            ]);

            //restamos las unidades del pedido de la tabla producto
            $stmt = $db->prepare('UPDATE producto SET unidades = unidades - :c WHERE id = :id');
            $stmt->execute([
                ':c' => $producto['cantidad'],
                ':id' => $producto['producto_id']
            ]);

            //calculamos el precio del pedido (unidades * precio)
            $total += $producto['precio'] * $producto['cantidad'];

        }

        //actualizamos el precio total del pedido
        $stmt = $db->prepare('UPDATE pedido SET total = :t, fecha_pedido = NOW(), fecha_entrega = DATE_ADD(NOW(), INTERVAL 7 DAY) WHERE id = :id');
        $stmt->execute([
            ':t' => $total,
            ':id' => $pedido_id
        ]);

        $db->commit();

        echo json_encode(['success' => 'Pedido realizado correctamente']);

    } catch (Exception $e){
        $db->rollBack();
        echo json_encode(['error' => 'No es posible realizar el pedido']);
        return;
    }
    
}

?>