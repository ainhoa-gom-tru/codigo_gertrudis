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
        obtenerTodasLasValoraciones($db);
        break;
    case 'POST':
        insertarNuevaValoracion($db);
        break;
    case 'PUT':
        actualizarValoracion($db);
        break;
    case 'DELETE':
        eliminarValoracion($db);
        break;
}

//funcion para obtener todas las valoraciones
function obtenerTodasLasValoraciones($db){
    //declaramos un array para almacenar todas las valoraciones
    $lista_valoraciones = [];
    //sentencia try-catch para que nos indique que ha fallado la insercción (en caso de que se dé)
    try{
        //seleccionamos a todas las valoraciones
        $stmt = $db->query('SELECT * FROM valoracion');
        //mientras haya filas de datos, que se inserten en el array creado anteriormente
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $lista_valoraciones[] = $fila;
        }

        echo json_encode($lista_valoraciones);

    } catch (PDOException $e){
        echo json_encode(['error' => 'Error al obtener todas las valoraciones']);
    }
}

//funcion para valorar un producto
function insertarNuevaValoracion($db){

    //obtenemos los datos que nos llegan
    $data = json_decode(file_get_contents('php://input'), true);

    //comprobamos que nos llegue el id
    if(empty($data['id'])){
        echo json_encode(['error' => 'ID requerido']);
        return;
    }

    //obtenemos el id del usuario
    $usuario_id = $_SESSION['usuario']['id'];

    //comprobamos que hay un usuario logueado
    if (!isset($_SESSION['usuario']['id'])) {
        echo json_encode(['error' => 'El usuario no ha iniciado sesión']);
        return;
    }

    //comprobamos que el campo de puntuación no nos llegue vacío, y esté entre 1 y 5
    if(empty($data['puntuacion']) || $data['puntuacion'] < 1 || $data['puntuacion'] > 5){
        echo json_encode(['error' => 'La puntuación no es válida']);
        return;
    }

    //en caso de que esté vacío el comentario, lo dejamos a null
    if(empty($data['comentario'])){
        $data['comentario'] = NULL;
    }

    //comprobamos que no se haya realizado una valoración antes
    $stmt = $db->prepare('SELECT id FROM valoracion WHERE usuario_id = :ud AND producto_id = :pd');
    $stmt->execute([':ud' => $usuario_id,
        ':pd' => $data['id']]);
    
    if($stmt->fetch()){
        echo json_encode(['error' => 'Ya has valorado este producto']);
        return;
    }

    //sentencia try-catch para que nos indique que ha fallado la insercción (en caso de que se dé)
    try{
        //hacemos inserción de datos con sentencia preparada
        $stmt = $db->prepare('INSERT INTO valoracion
            (usuario_id, producto_id, puntuacion, comentario) VALUES (:ud, :pd, :p, :c)');
        $stmt->execute(array(
            ':ud' => $usuario_id,
            ':pd' => $data['id'],
            ':p' => $data['puntuacion'],
            ':c' => $data['comentario'])
        );

        echo json_encode(['success' => 'La valoración ha sido creado con éxito']);

    } catch (PDOException $e){
        echo json_encode(['error' => 'Error al insertar la valoración']);
    }

}

//funcion para modificicar la puntuación o el comentario de la valoración
function actualizarValoracion($db){

    //obtenemos los datos que nos llegan
    $data = json_decode(file_get_contents('php://input'), true);

    //comprobamos que nos llegue el id
    if(empty($data['id'])){
        echo json_encode(['error' => 'ID requerido']);
        return;
    }

    //obtenemos el id del usuario
    $usuario_id = $_SESSION['usuario']['id'];

    //comprobamos que hay un usuario logueado
    if (!isset($_SESSION['usuario']['id'])) {
        echo json_encode(['error' => 'El usuario no ha iniciado sesión']);
        return;
    }

    //sentencia try-catch para que nos indique que ha fallado la insercción (en caso de que se dé)
    try{

        //para actualizar la puntuación
        if(!empty($data['puntuacion'])){ 
            //actualizamos la puntuación
            $stmt = $db->prepare('UPDATE valoracion SET puntuacion = :pt WHERE id = :id AND usuario_id = :ud');
            $stmt->execute([':pt' => $data['puntuacion'],
                ':id' => $data['id'],
                ':ud' => $usuario_id]);
                
            echo json_encode(['success' => 'La puntuación de la valoración ha sido actualizado con éxito']);

        }

        //para actualizar el texto
        if(!empty($data['comentario'])){ 
            //actualizamos el comentario
            $stmt = $db->prepare('UPDATE valoracion SET comentario = :ct WHERE id = :id AND usuario_id = :ud');
            $stmt->execute([':ct' => $data['comentario'],
                ':id' => $data['id'],
                ':ud' => $usuario_id]);
                
            echo json_encode(['success' => 'El comentario de la valoración ha sido actualizado con éxito']);
            
        }

    } catch (PDOException $e){
        echo json_encode(['error' => 'Error al actualizar los datos de la valoración']);
    }

}

//funcion para una eliminar una valoracion
function eliminarValoracion($db){

    //obtenemos los datos que nos llegan
    $data = json_decode(file_get_contents('php://input'), true);

    //comprobamos que nos llegue el id
    if(empty($data['id'])){
        echo json_encode(['error' => 'ID requerido']);
        return;
    }

    //obtenemos el id del usuario
    $usuario_id = $_SESSION['usuario']['id'];

    //comprobamos que hay un usuario logueado
    if (!isset($_SESSION['usuario']['id'])) {
        echo json_encode(['error' => 'El usuario no ha iniciado sesión']);
        return;
    }
    
    //sentencia try-catch para que nos indique que ha fallado la insercción (en caso de que se dé)
    try{
        //compobamos que la valoracion pertenece al usuario
        $stmt = $db->prepare('SELECT * FROM valoracion WHERE id = :id AND usuario_id = :ud');
        $stmt->execute([
            ':id' => $data['id'],
            ':ud' => $usuario_id]);

        //ejecutamos la sentencia sql
        $comprobar_valoracion = $stmt->fetch(PDO::FETCH_ASSOC);

        //en caso de que no sea el dueño de la valoración, no puede eliminarla
        if(!$comprobar_valoracion){
            echo json_encode(['error' => 'No puedes eliminar esta valoración']);
            return;
        }

        //en caso de que sí sea
        //eliminamos la valoracion de la base de datos
        $stmt = $db->prepare('DELETE FROM valoracion WHERE id = :id AND usuario_id = :ud');
        $stmt->execute([
            ':id' => $data['id'],
            ':ud' => $usuario_id]);

        echo json_encode(['success' => 'La valoración ha sido eliminada con éxito']);

    } catch (PDOException $e){
        echo json_encode(['error' => 'Error al eliminar la valoración']);
    }

}

?>