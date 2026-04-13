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
    case 'GET':
        obtenerTodosLosArticulos($db);
        break;
    case 'POST':
        insertarNuevoArticulo($db);
        break;
    case 'DELETE':
        eliminarArticulo($db);
        break;
}

//funcion para obtener todos los artículos del blog
function obtenerTodosLosArticulos($db){

    //declaramos un array para almacenar todos los artículos del blog
    $lista_articulos = [];
    //sentencia try-catch para que nos indique que ha fallado la insercción (en caso de que se dé)
    try{
        //seleccionamos a todos los artículos del blog
        $stmt = $db->query('SELECT * FROM blog');
        //mientras haya filas de datos, que se inserten en el array creado anteriormente
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $lista_articulos[] = $fila;
        }

        echo json_encode($lista_articulos);

    } catch (PDOException $e){
        echo json_encode(['error' => 'Error al obtener todos los artículos']);
    }

}

//funcion para insertar una nueva foto
function insertarNuevoArticulo($db){

    //comprobamos que no nos lleguen datos vacíos
    if(empty($_POST['titulo']) || empty($_POST['texto']) || empty($_POST['categoria']) || empty($_FILES['foto'])){
        echo json_encode(['error' => 'Los campos obligatorios no pueden estar vacíos']);
        return;
    }

    //creamos una variable para almacenar una expresión regular que valide el título del artículo
    $expresionRegular = "/^[A-ZÁÉÍÓÚÑ][a-zA-Z0-9ÁÉÍÓÚáéíóúÑñ]*(?:\s[a-zA-Z0-9ÁÉÍÓÚáéíóúÑñ]+)*$/";

    //validamos que el título cumpla con la expresión regular
    if(!preg_match($expresionRegular, $_POST['titulo'])){
        echo json_encode(['error' => 'El título no es válido']);
        return;
    }

    //comprobamos que los datos que nos llegan de categoría son los del enum
    if($_POST['categoria'] !== 'música' || $_POST['categoria'] !== 'arte' || $_POST['categoria'] !== 'viajes' || $_POST['categoria'] !== 'moda'){
        echo json_encode(['error' => 'La categoría introducida no es válida']);
        return;
    }

    // le realizamos las siguientes validaciones a la foto
    if(!empty($_FILES['foto']['name'])){
        //almacenamos el nombre de la imagen con la extensión
    	$nombre_foto = $_FILES['foto']['name'];
        //almacenamos el tipo de imagen (extensión)
    	$tipo_foto = $_FILES['foto']['type'];
        //almacenamos el nombre temporal para la imagen
    	$nombre_temporal_foto = $_FILES['foto']['tmp_name'];
        //tamaño de la foto
    	$tamano_foto = $_FILES['foto']['size'];
        //oseparamos el nombre de la imagen para dividir extesión y nombre
    	$separar_foto = explode('.',$nombre_foto);
        //obtenemos la extension de la foto y lo ponemos en minúscula
    	$extension_foto = strtolower(end($separar_foto));
        //extensiones de las imagenes permitidas
    	$extension = ["webp"];
        //en caso de que la extensión de la foto sea idéntica a la extensión permitida
    	if(in_array($extension_foto, $extension) === true){
            //declaramos un array con el tipo MIME de archivo permitido
    		$tipos = ["image/webp"];
            //en caso de que sean idénticas
    		if(in_array($tipo_foto, $tipos) === true){
                //nos aseuramos que el tamaño de la foto sea menos de 2MB
    			if($tamano_foto <= 2000000){
                    //hasheamos el nombre de la imagen
		    		$nuevo_nombre_foto = time().'-'.rand() . '.'.$extension_foto;
                    //movemos la foto a la carpeta donde se almacenan todas
		    		if(move_uploaded_file($nombre_temporal_foto,"../album/".$nuevo_nombre_foto)){
		    			$datos_formulario['foto'] = $nuevo_nombre_foto;
		    		} else{
		    			echo json_encode(['error' => 'No es posible subir la imagen']);
                        return;
		    		}
	    		} else{
                    echo json_encode(['error' => 'El tamaño de la imagen es superior al establecido']);
                    return;
	    		}
    		} else {
    			echo json_encode(['error' => 'El tipo de la imagen no es válido']);
                return;
    		}
    	} else {
            echo json_encode(['error' => 'Archivo de imagen no válido']);
            return;
    	}
    } else {
    	echo json_encode(['error' => 'Debes de subir una imagen']);
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
        //hacemos inserción de datos con sentencia preparada
        $stmt = $db->prepare('INSERT INTO blog (usuario_id, titulo, texto, categoria, foto)
            VALUES (:ud, :t, :tt, :ct, :ft)');
        $stmt->execute(array(
            ':ud' => $usuario_id,
            ':t' => $_POST['titulo'],
            ':tt' => $_POST['texto'],
            ':ct' => $_POST['categoria'],
            ':ft' => $nuevo_nombre_foto));

        echo json_encode(['success' => 'El artículo ha sido añadida con éxito']);

    } catch (PDOException $e){
        echo json_encode(['error' => 'Error al añadir el artículo']);
    }

}

//funcion para eliminar un artículo
function eliminarArticulo($db){

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
        //compobamos que el artículo pertenece al usuario
        $stmt = $db->prepare('SELECT * FROM blog WHERE id = :id AND usuario_id = :ud');
        $stmt->execute([
            ':id' => $data['id'],
            ':ud' => $usuario_id]);

        //ejecutamos la sentencia sql
        $comprobar_articulo = $stmt->fetch(PDO::FETCH_ASSOC);

        //en caso de que no sea la dueña del artículo, no puede eliminarla
        if(!$comprobar_articulo){
            echo json_encode(['error' => 'No puedes eliminar este artículo']);
            return;
        }

        //en caso de que sí sea la dueña
        //obtenemos la ruta de la imagen
        $ruta_imagen = "../blog/" . $comprobar_articulo['foto'];

        //en caso de que en la carpeta esté la imagen, la eliminamos de la carpeta
        if(file_exists($ruta_imagen)){
            unlink($ruta_imagen);
        }

        //y, finalmente, eliminamos el artículo de la base de datos
        $stmt = $db->prepare('DELETE FROM blog WHERE id = :id AND usuario_id = :ud');
        $stmt->execute([
            ':id' => $data['id'],
            ':ud' => $data['usuario_id']]);

        echo json_encode(['success' => 'El artículo ha sido eliminado con éxito']);

    } catch (PDOException $e){
        echo json_encode(['error' => 'Error al eliminar el artículo']);
    }

}

?>