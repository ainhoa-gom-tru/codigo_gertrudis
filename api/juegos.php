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
        obtenerTodosLosJuegos($db);
        break;
    case 'POST':
        insertarNuevoJuego($db);
        break;
    case 'PUT':
        actualizarJuego($db);
        break;
    case 'DELETE':
        eliminarJuego($db);
        break;
}

//funcion para obtener todos los juegos de la base de datos
function obtenerTodosLosJuegos($db){

    //declaramos un array para almacenar todos los juegos
    $lista_juegos = [];
    //sentencia try-catch para que nos indique que ha fallado la insercción (en caso de que se dé)
    try{
        //seleccionamos a todos los juegos de la tabla juego
        $stmt = $db->query('SELECT * FROM juego');
        //mientras haya filas de datos, que se inserten en el array creado anteriormente
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $lista_juegos[] = $fila;
        }

        echo json_encode($lista_juegos);

    } catch (PDOException $e){
        echo json_encode(['error' => 'Error al obtener todos los juegos']);
    }

}

//funciton para insertar un nuevo juego
function insertarNuevoJuego($db){

    //comprobamos que no nos lleguen campos vacíos
    if (empty($_POST['nombre']) || empty($_FILES['foto']['name']) || empty($_FILES['archivo']['name'])){
        echo json_encode(['error' => 'Los campos no pueden estar vacíos']);
        return;
    }

    /** creamos una expresión regular para validar que sea un título y que la primera letra
     * sea mayúscula, puede tener espacios y tildes, no números ni caracteres especiales
    */
    $expresionRegular = "/^[A-ZÁÉÍÓÚÑÜ][a-záéíóúñü]+( [A-ZÁÉÍÓÚÑÜ]?[a-záéíóúñü]+)*$/";

    //comprobamos que el título cumple la expresión regular
    if(!preg_match($expresionRegular, $_POST['nombre'])){
        echo json_encode(['error' => 'El título no es válido']);
        return;
    }

    //validamos también que no haya ningún otro nombre de juego igual
    $stmt = $db->prepare('SELECT id FROM juego WHERE nombre = :nb');
    $stmt->execute([':nb' => $_POST['nombre']]);

    if($stmt->fetch()){
        echo json_encode(['error' => 'El juego ya existe']);
        return;
    }

    //a la imagen le realizamos las siguientes validaciones
    if(!empty($_FILES['foto']['name'])){
        //almacenamos el nombre de la imagen con la extensión
    	$nombre_foto = $_FILES['foto']['name'];
        //almacenamos el tipo de imagen (extensión)
    	$tipo_foto = $_FILES['foto']['type'];
        //almacenamos el nombre temporal para la imagen
    	$nombre_temporal_foto = $_FILES['foto']['tmp_name'];
        //cogemos la anchura y altura de la foto
    	$info = @getimagesize($_FILES["foto"]["tmp_name"]);
    	$ancho = $info[0];
    	$alto = $info[1];
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
                    //si la imagen cumple con el ancho y alto establecido
    				if($ancho == '700' && $alto == '700'){
                        //hasheamos el nombre de la imagen
		    			$nuevo_nombre_foto = time().'-'.rand() . '.'.$extension_foto;
                        //movemos la foto a la carpeta donde se almacenan todas
		    			if(move_uploaded_file($nombre_temporal_foto,"miniaturas/".$nuevo_nombre_foto)){
		    				$datos_formulario['foto'] = $nuevo_nombre_foto;
		    			} else{
		    				echo json_encode(['error' => 'No es posible subir la imagen']);
                            return;
		    			}
		    		} else{
		    			echo json_encode(['error' => 'Las dimensiones de la imagen no son correctas']);
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
    
    //al archivo le realizamos las siguientes validaciones
    if(!empty($_FILES['archivo']['name'])){
        //almacenamos el nombre del archivo con la extensión
    	$nombre_archivo = $_FILES['archivo']['name'];
        //almacenamos el tipo de archivo (extensión)
    	$tipo_archivo = $_FILES['archivo']['type'];
        //almacenamos el nombre temporal para la archivo
    	$nombre_temporal = $_FILES['archivo']['tmp_name'];
        //tamaño del archivo
    	$tamano_archivo = $_FILES['archivo']['size'];
        //separamos el nombre del archivo para dividir extesión y nombre
    	$separar_archivo = explode('.',$nombre_archivo);
        //obtenemos la extension del archivo y lo ponemos en minúscula
    	$extension_archivo = strtolower(end($separar_archivo));
        //extensiones de las archivos permitidos
    	$extension = ["html"];
        //en caso de que la extensión del archivo sea idéntica a la extensión permitida
    	if(in_array($extension_archivo, $extension) === true){
            //declaramos un array con el tipo MIME de archivo permitido
    		$tipo = ["text/html"];
            //en caso de que sean idénticas
    		if(in_array($tipo_archivo, $tipo) === true){
                //nos aseuramos que el tamaño del archivo sea menos de 200 KB
    			if($tamano_archivo <= 200000){
                    //hasheamos el nombre del archivo
		    		$nuevo_nombre_archivo = time().'-'.rand() . '.'.$extension_archivo;
                    //movemos el archivo a la carpeta donde se almacenan todos
		    		if(move_uploaded_file($nombre_temporal,"juegos/".$nuevo_nombre_archivo)){
		    			$datos_formulario['archivo'] = $nuevo_nombre_archivo;
		    		} else{
		    			echo json_encode(['error' => 'No es posible subir el archivo']);
                        return;
		    		}
	    		} else{
                    echo json_encode(['error' => 'El tamaño del archivo es superior al establecido']);
                    return;
	    		}
    		} else {
    			echo json_encode(['error' => 'El tipo de archivo no es válido']);
                return;
    		}
    	} else {
            echo json_encode(['error' => 'Archivo no válido']);
            return;
    	}
    } else {
    	echo json_encode(['error' => 'Debes de subir un archivo']);
        return;
    }

    //sentencia try-catch para que nos indique que ha fallado la insercción (en caso de que se dé)
    try{
        //hacemos inserción de datos con sentencia preparada
        $stmt = $db->prepare('INSERT INTO juego
            (nombre, foto, archivo) VALUES (:nb, :ft, :rc)');
        $stmt->execute(array(
            ':nb' => $_POST['nombre'],
            ':ft' => $nuevo_nombre_foto,
            ':rc' => $nuevo_nombre_archivo)
        );

        echo json_encode(['success' => 'El juego ha sido creado con éxito']);

    } catch (PDOException $e){
        echo json_encode(['error' => 'Error al insertar el juego']);
    }

}

//funcion para actualizar los datos de un juego
function actualizarJuego($db){

    //obtenemos los datos que nos llegan
    $data = json_decode(file_get_contents('php://input'), true);

    //comprobamos que nos llegue el id
    if(empty($data['id'])){
        echo json_encode(['error' => 'ID requerido']);
        return;
    }

    //comprobamos que nos llegue el nombre
    if(empty($data['nombre'])){
        echo json_encode(['error' => 'Nombre requerido']);
        return;
    }

    /** creamos una expresión regular para validar que sea un título y que la primera letra
     * sea mayúscula, puede tener espacios y tildes, no números ni caracteres especiales
    */
    $expresionRegular = "/^[A-ZÁÉÍÓÚÑÜ][a-záéíóúñü]+( [A-ZÁÉÍÓÚÑÜ]?[a-záéíóúñü]+)*$/";

    //comprobamos que el nuwov nombre que se escribe pase la validación
    if(!preg_match($expresionRegular, $data['nombre'])){
        echo json_encode(['error' => 'El nombre no es válido']);
        return;
    }

    //sentencia try-catch para que nos indique que ha fallado la insercción (en caso de que se dé)
    try{
        //actualizar el nombre del juego
        if(!empty($data['nombre'])){ 
            //actualizamos el nombre
            $stmt = $db->prepare('UPDATE juego SET nombre = :nb WHERE id = :id');
            $stmt->execute([':nb' => $data['nombre'],':id' => $data['id']]);
            
            echo json_encode(['success' => 'El juego ha sido actualizado con éxito']);
            return;
        }
    } catch (PDOException $e){
        echo json_encode(['error' => 'Error al actualizar los datos del juego']);
    }

}

//funcin para eliminar un juego de la base de datos
function eliminarJuego($db){

    //obtenemos los datos que nos llegan
    $data = json_decode(file_get_contents('php://input'), true);

    //comprobamos que nos llegue el id
    if(empty($data['id'])){
        echo json_encode(['error' => 'ID requerido']);
        return;
    }

    //sentencia try-catch para que nos indique que ha fallado la insercción (en caso de que se dé)
    try{

        $stmt = $db->prepare('SELECT foto, archivo FROM juego WHERE id = :id');
        $stmt->execute([':id' => $data['id']]);
        $comprobar_juego = $stmt->fetch(PDO::FETCH_ASSOC);

        //obtenemos la ruta de la imagen
        $ruta_imagen = "miniaturas/" . $comprobar_juego['foto'];

        //obtenemos la ruta del archivo
        $ruta_archivo = "juegos/" . $comprobar_juego['archivo'];

        //en caso de que en la carpeta esté la imagen, la eliminamos de la carpeta
        if(file_exists($ruta_imagen)){
            unlink($ruta_imagen);
        }

        //en caso de que en la carpeta esté el archivo, lo eliminamos de la carpeta
        if(file_exists($ruta_archivo)){
            unlink($ruta_archivo);
        }
        
        //eliminamos el juego con el id que nos llega
        $stmt = $db->prepare('DELETE FROM juego WHERE id = :id');
        $stmt->execute([':id' => $data['id']]);

        echo json_encode(['success' => 'El juego ha sido eliminado con éxito']);

    } catch (PDOException $e){
        echo json_encode(['error' => 'Error al eliminar el juego']);
    }

}

?>