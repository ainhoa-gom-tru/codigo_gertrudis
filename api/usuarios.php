<?php
//iniciamos sesión
session_start();

// instaciamos la clase Database y hacemos la conexión a la base de datos
$database = new Database();
$db = $database->conexionBaseDeDatos();

//obtenemos el metodo que se le pas (get, post, delete, etc)
$metodo = $_SERVER['REQUEST_METHOD'];

/**
 * Vamos a necesitar obtener la ruta de la url para poder diferenciar entre el registro
 * y el login, siendo las rutas del estilo /usuarios (para que un usuario se registre)
 * y /usuarios/login/ (para que un usuario inicie sesión)
 */

//obtenemos la ruta
$ruta = explode('/', trim($_SERVER['PATH_INFO'], '/'));
// obtenemos a que "sección" estamos accediendo (productos, usuarios, etc)
$seccion = $ruta[1] ?? null;

//hacemos un switch dependiendo de qué metodo se utiliza
switch($metodo){
    case 'GET':
        obtenerTodosLosUsuarios($db);
        break;
    case 'POST':
        if ($seccion === 'login'){
            iniciarSesion($db);
        } else {
            insertarNuevoUsuario($db);
        }
        break;
    case 'PUT':
        actualizarUsuario($db);
        break;
    case 'DELETE':
        eliminarUsuario($db);
        break;
}

//creamos la función para obtener todos los usuarios almacenados en la base de datos
function obtenerTodosLosUsuarios($db){
    //declaramos un array para almacenar todos los usuarios
    $lista_usuarios = [];
    //sentencia try-catch para que nos indique que ha fallado la insercción (en caso de que se dé)
    try{
        //seleccionamos a todos los usuarios de la tabla usuario
        $stmt = $db->query('SELECT * FROM usuario');
        //mientras haya filas de datos, que se inserten en el array creado anteriormente
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $lista_usuarios[] = $fila;
        }

        echo json_encode($lista_usuarios);

    } catch (PDOException $e){
        echo json_encode(['error' => 'Error al obtener todos los usuarios']);
    }
}

//creamos la función de insertar un nuevo usuario
function insertarNuevoUsuario($db){

    //obtenemos los datos que nos llegan
    $data = json_decode(file_get_contents('php://input'), true);

    //comprobamos que no nos lleguen campos vacíos
    if (empty($data['nombre']) || empty($data['apellidos']) || empty($data['usuario']) || empty($data['email']) || empty($data['contrasena'])){
        echo json_encode(['error' => 'Los campos no pueden estar vacíos']);
        return;
    }

    //creamos una variable para almacenar una expresión regular que valide el nombre y los apellidos
    $expresionRegular = "/^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+( [A-ZÁÉÍÓÚÑ][a-záéíóúñ]+)*$/";

    //validamos que el nombre y los apellidos cumplan con la expresión regular
    if(!preg_match($expresionRegular, $data['nombre']) || !preg_match($expresionRegular, $data['apellidos'])){
        echo json_encode(['error' => 'El nombre y/o apellidos no son válidos']);
        return;
    }

    //creamos una expresión regular para la contraseña
    $expresionRegularContrasena = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,16}$/";

    //validamos que la cotraseña cumpla con la expresión regular
    if(!preg_match($expresionRegularContrasena, $data['contrasena'])){
        echo json_encode(['error' => 'La contraseña no es segura']);
        return;
    }

    //comprobamos que el email tenga el formato de un correo electrónico
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['error' => 'El email no es válido']);
        return;
    }

    //comprobamos que el nombre de usuario no tenga caracteres especiales
    $expresionRegularUsuario = "/^[a-z0-9_]{3,16}$/";
    
    //validamos que cumple con el formato de la expresión regular
    if(!preg_match($expresionRegularUsuario, $data['usuario'])){
        echo json_encode(['error' => 'El usuario no es válido']);
        return;
    }

    //validamos también que no haya ningún otro usuario igual
    $stmt = $db->prepare('SELECT id FROM usuario WHERE usuario = :sr');
    $stmt->execute([':sr' => $data['usuario']]);

    if($stmt->fetch()){
        echo json_encode(['error' => 'El nombre de usuario ya existe']);
        return;
    }

    //comprobamos que el email que nos están introduciendo, no está ya registrado en la base de datos
    $stmt = $db->prepare('SELECT id FROM usuario WHERE email = :ml');
    $stmt->execute([':ml' => $data['email']]);
    //en caso de que $stmt devuelva algo, es que hay un usuario con ese email
    if($stmt->fetch()){
        echo json_encode(['error' => 'Este usuario ya está registrado']);
        return;
    }

    //sentencia try-catch para que nos indique que ha fallado la insercción (en caso de que se dé)
    try{
        //hacemos inserción de datos con sentencia preparada
        $stmt = $db->prepare('INSERT INTO usuario
            (nombre, apellidos, usuario, email, contrasena, rol, avatar, validado) VALUES (:nb, :pd, :sr, :ml, :ct, :rl, :vt, :vd)');
        $stmt->execute(array(
            ':nb' => $data['nombre'],
            ':pd' => $data['apellidos'],
            ':sr' => $data['usuario'],
            ':ml' => $data['email'],
            ':ct' => password_hash($data['contrasena'], PASSWORD_DEFAULT),
            ':rl' => 'cliente',
            ':vt' => 'avatar1.png',
            ':vd' => 0)
        );

        echo json_encode(['success' => 'El usuario ha sido creado con éxito']);

    } catch (PDOException $e){
        echo json_encode(['error' => 'Error al insertar el usuario', 'detalle' => $e->getMessage()]);
    }

}

//creamos la funcion para iniciar sesión
function iniciarSesion($db){

    //obtenemos los datos que nos llegan
    $data = json_decode(file_get_contents('php://input'), true);

    //comprobamos que nos llegue el correo electrónico o el usuario y la contraseña
    if (empty($data['contrasena']) || (empty($data['usuario']) && empty($data['email']))) {
        echo json_encode(['error' => 'Datos incompletos']);
        return;
    }

    //hacemos dos select distintos en funcion de si nos introduce email o usuario
    //si nos introducen usuario
    if (!empty($data['usuario'])) {
        $stmt = $db->prepare('SELECT * FROM usuario WHERE usuario = :sr');
        $stmt->execute([':sr' => $data['usuario']]);
        //si nos introducen email
    } else {
        $stmt = $db->prepare('SELECT * FROM usuario WHERE email = :ml');
        $stmt->execute([':ml' => $data['email']]);
    }

    //almacenamos en la variable usuario logueado el resultado de ejecutar la sentencia sql
    $usuarioLogeado = $stmt->fetch(PDO::FETCH_ASSOC);

    //en caso de que no se encuentre el usuario o email
    if (!$usuarioLogeado) {
        echo json_encode(['error' => 'No se ha encontrado ningun usuario']);
        return;
    }

    //en caso de que al contraseña no sea correcta
    if (!password_verify($data['contrasena'], $usuarioLogeado['contrasena'])) {
        echo json_encode(['error' => 'La contraseña introducida no es correcta']);
        return;
    }

    //guardamos todos los datos del usuario logueado en la sesion
    $_SESSION['usuario'] = $usuarioLogeado['usuario'];
    echo json_encode($usuarioLogeado);
    
}

//creamos la funcion para validar y actualizar el rol del usuario
function actualizarUsuario($db){

    //obtenemos los datos que nos llegan
    $data = json_decode(file_get_contents('php://input'), true);

    //comprobamos que nos llegue el id
    if(empty($data['id'])){
        echo json_encode(['error' => 'ID requerido']);
        return;
    }

    //comprobamos que el rol sea algunos de los establecidos y no otro
    if (!empty($data['rol'])){
        if ($data['rol'] !== 'admin' && $data['rol'] !== 'desarrollador' && $data['rol'] !== 'compi' && $data['rol'] !== 'cliente') {
            echo json_encode(['error' => 'El rol no es válido']);
            return;
        }
    }

    //sentencia try-catch para que nos indique que ha fallado la insercción (en caso de que se dé)
    try{
        //para validar el usuario
        if(!empty($data['validado'])){
            //validamos el usuario
            $stmt = $db->prepare('UPDATE usuario SET validado = :vd WHERE id = :id');
            $stmt->execute([':vd' => 1,':id' => $data['id']]);

            echo json_encode(['success' => 'El usuario ha sido validado con éxito']);
            return;
            
        } 

        //para actualizar el rol
        if(!empty($data['rol'])){ 
            //actualizamos el rol
            $stmt = $db->prepare('UPDATE usuario SET rol = :rl WHERE id = :id');
            $stmt->execute([':rl' => $data['rol'],':id' => $data['id']]);
            
            echo json_encode(['success' => 'El usuario ha sido actualizado con éxito']);
            return;
        }

        //para actualizar el avatar
        if(!empty($data['avatar'])){ 
            //actualizamos el rol
            $stmt = $db->prepare('UPDATE usuario SET avatar = :vt WHERE id = :id');
            $stmt->execute([':vt' => $data['avatar'],':id' => $data['id']]);
            
            echo json_encode(['success' => 'El usuario ha actualizado su avatar con éxito']);
            return;
        }

    } catch (PDOException $e){
        echo json_encode(['error' => 'Error al actualizar los datos del usuario']);
    }

}

//creamos la función para eliminar un usuario
function eliminarUsuario($db){

    //obtenemos los datos que nos llegan
    $data = json_decode(file_get_contents('php://input'), true);

    //comprobamos que nos llegue el id
    if(empty($data['id'])){
        echo json_encode(['error' => 'ID requerido']);
        return;
    }

    //sentencia try-catch para que nos indique que ha fallado la insercción (en caso de que se dé)
    try{
        //eliminamos el usuario con el id que nos llega
        $stmt = $db->prepare('DELETE FROM usuario WHERE id = :id');
        $stmt->execute([':id' => $data['id']]);

        echo json_encode(['success' => 'El usuario ha sido eliminado con éxito']);

    } catch (PDOException $e){
        echo json_encode(['error' => 'Error al eliminar el usuario']);
    }

}

?>