<?php
//iniciamos la sesión
session_start();

require 'db.php';

// instaciamos la clase Database y hacemos la conexión a la base de datos
$database = new Database();
$db = $database->conexionBaseDeDatos();

//obtenemos el metodo que se le pas (get, post, delete, etc)
$metodo = $_SERVER['REQUEST_METHOD'];

//hacemos un switch dependiendo de qué metodo se utiliza
switch($metodo){
    case 'POST':
        realizarPedido($db);
        break;
}

//funcion en la que confirmamos el pedido
function realizarPedido($db){

    //obtenemos los datos que nos llegan
    $data = json_decode(file_get_contents('php://input'), true);

    //obtenemos el id del usuario
    $usuario_id = $_SESSION['usuario']['id'];

    //comprobamos que hay un usuario logueado
    if (!isset($_SESSION['usuario']['id'])) {
        echo json_encode(['error' => 'El usuario no ha iniciado sesión']);
        return;
    }

    //sentencia try-catch
    try{

        $db->beginTransaction();

        //obtenemos el carrito
        $stmt = $db->prepare('SELECT id FROM pedido WHERE usuario_id = :ud AND total = 0');
        $stmt->execute([':ud' => $usuario_id]);
        $carrito = $stmt->fetch();

        //en caso de que no haya carrito
        if (!$carrito){
            echo json_encode(['error' => 'No hay ningún carrito']);
        }

        //obtenemos el id del pedido
        $pedido_id = $pedido_id['id'];

        //Obtenemos los productos del carrito
        $stmt = $db->prepare('SELECT pd.producto_id, pd.cantidad, p.precio, p.unidades
        FROM productos_pedido pp
        JOIN producto p ON p.id = pd.producto_id,
        WHERE pd.pedido_id = :pd');
        $stmt->execute([':pd' => $pedido_id]);
        $productos = $stmt->fetchAll(PDO:FETCH_ASSOC);

        //asignamos 0 al total
        $total = 0;

        //recorremos todos los productos
        foreach($productos as $producto){

            //comprobamos que queden unidades del producto
            if($producto['unidades'] < $producto['cantidad']){
                echo json_encode(['error' => 'No hay suficientes unidades de este producto' . $producto['id']]);
                return;
            }

            //guardamos el precio del producto (en caso de que haya sufrido modificaciones)
            $stmt = $db->prepare('UPDATE productos_pedido SET precio = :p WHERE pedido_id = :pd AND producto_id = :pi');
            $stmt->execute([':p' => $precio],
                [':pd' => $pedido_id], 
                [':pi' => $producto_id]);
            
            //restamos las unidades del pedido de la tabla producto
            $stmt->prepare('UPDATE producto SET unidades = unidades - :c WHERE id = :id');
            $stmt->execute([':c' => $producto['cantidad']],
                [':id' => $producto['id']]);
            
            //calculamos el precio del pedido (unidades * precio)
            $total += $producto['precio'] * $producto['cantidad'];

            //actualizamos el precio total del pedido
            $stmt = $db->prepare('UPDATE pedido SET total = :t WHERE id = :id');
            $stmt->execute([':t' => $total],
                [':id' => $pedido_id]);
            
            echo json_encode(['success' => 'Pedido realizado correctamente']);

        }
    } catch (Exception $e){
        echo json_encode(['error' => 'No es posible realizar el pedido']);
        return;
    }
    
}

?>