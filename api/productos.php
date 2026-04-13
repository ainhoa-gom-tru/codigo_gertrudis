<?php
require 'db.php';

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
        actualizarProducto($db);
        break;
    case 'DELETE':
        eliminarProducto($db);
        break;
}

//creamos la función para obtener todos los productos almacenados en la base de datos
function obtenerTodosLosProductos($db){
    //declaramos un array para almacenar todos los productos
    $lista_productos = [];
    //sentencia try-catch para que nos indique que ha fallado la insercción (en caso de que se dé)
    try{
        //seleccionamos a todos los productos de la tabla producto
        $stmt = $db->query('SELECT * FROM producto');
        //mientras haya filas de datos, que se inserten en el array creado anteriormente
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $lista_productos[] = $fila;
        }

        echo json_encode($lista_productos);

    } catch (PDOException $e){
        echo json_encode(['error' => 'Error al obtener todos los productos']);
    }
}

//creamos la función de insertar un nuevo producto
function insertarNuevoProducto($db){

    //comprobamos que no nos lleguen campos vacíos
    if (empty($_POST['nombre']) || empty($_POST['descripcion']) || empty($_FILES['foto']['name']) || empty($_POST['precio']) || empty($_POST['unidades'])){
        echo json_encode(['error' => 'Los campos no pueden estar vacíos']);
        return;
    }

    //creamos una variable para almacenar una expresión regular que valide el nombre del producto
    $expresionRegular = "/^[A-ZÁÉÍÓÚÑ][a-zA-Z0-9ÁÉÍÓÚáéíóúÑñ]*(?:\s[a-zA-Z0-9ÁÉÍÓÚáéíóúÑñ]+)*$/";

    //validamos que el nombre cumpla con la expresión regular
    if(!preg_match($expresionRegular, $_POST['nombre'])){
        echo json_encode(['error' => 'El nombre no es válido']);
        return;
    }

    //creamos una expresión regular para la descripción
    $expresionRegularDescripcion = "/^[A-Za-zÁÉÍÓÚáéíóúÑñ0-9 ,.\n]+$/";

    //validamos que la descripción cumpla con la expresión regular
    if(!preg_match($expresionRegularDescripcion, $_POST['descripcion'])){
        echo json_encode(['error' => 'La descripción no es válida']);
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
    				if($ancho == '225' && $alto == '225'){
                        //hasheamos el nombre de la imagen
		    			$nuevo_nombre_foto = time().'-'.rand() . '.'.$extension_foto;
                        //movemos la foto a la carpeta donde se almacenan todas
		    			if(move_uploaded_file($nombre_temporal_foto,"../productos/".$nuevo_nombre_foto)){
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

    //creamos una expresión regular para validar le precio del producto
    $expresionRegularPrecio = "/^\d{1,8}(\.\d{1,2})?$/";

    //compobamos que sea un número decimal, minimo de 1
    if(!preg_match($expresionRegularPrecio, $_POST['precio']) || $_POST['precio'] < 1){
        echo json_encode(['error' => 'El precio no es válido']);
        return;
    }

    //creamos una expresión regular para validar que las unidades
    $expresionRegularUnidades = "/^\d+$/";

    //compobamos que sea un número entero, no mayor de 100 y minimo de 0
    if(!preg_match($expresionRegularUnidades, $_POST['unidades']) || $_POST['unidades'] < 0 || $_POST['unidades'] > 1000){
        echo json_encode(['error' => 'El número de unidades no es válido']);
        return;
    }

    //validamos también que no haya ningún otro producto igual
    $stmt = $db->prepare('SELECT id FROM producto WHERE nombre = :nb');
    $stmt->execute([':nb' => $_POST['nombre']]);

    if($stmt->fetch()){
        echo json_encode(['error' => 'El producto ya existe']);
        return;
    }

    //sentencia try-catch para que nos indique que ha fallado la insercción (en caso de que se dé)
    try{
        //hacemos inserción de datos con sentencia preparada
        $stmt = $db->prepare('INSERT INTO producto
            (nombre, descripcion, foto, precio, unidades) 
            VALUES (:nb, :dc, :ft, :pc, :nd)');
        $stmt->execute(array(
            ':nb' => $_POST['nombre'],
            ':dc' => $_POST['descripcion'],
            ':ft' => $nuevo_nombre_foto,
            ':pc' => $_POST['precio'],
            ':nd' => $_POST['unidades'])  
        );

        echo json_encode(['success' => 'El producto ha sido creado con éxito']);

    } catch (PDOException $e){
        echo json_encode(['error' => 'Error al insertar el producto']);
    }

}

//creamos la funcion para actualizar los datos del producto
function actualizarProducto($db){

    //obtenemos los datos que nos llegan
    $data = json_decode(file_get_contents('php://input'), true);

    //comprobamos que nos llegue el id
    if(empty($data['id'])){
        echo json_encode(['error' => 'ID requerido']);
        return;
    }

    //sentencia try-catch para que nos indique que ha fallado la insercción (en caso de que se dé)
    try{

        //para actualizar el nombre
        if(!empty($data['nombre'])){ 
            //actualizamos el rol
            $stmt = $db->prepare('UPDATE producto SET nombre = :nb WHERE id = :id');
            $stmt->execute([':nb' => $data['nombre'],':id' => $data['id']]);
                
            echo json_encode(['success' => 'El nombre del producto ha sido actualizado con éxito']);

        }

        //para actualizar el descripción
        if(!empty($data['descripcion'])){ 
            //actualizamos el rol
            $stmt = $db->prepare('UPDATE producto SET descripcion = :dc WHERE id = :id');
            $stmt->execute([':dc' => $data['descripcion'],':id' => $data['id']]);
                
            echo json_encode(['success' => 'La descripción del producto ha sido actualizado con éxito']);
            
        }

        //para actualizar el precio
        if(!empty($data['precio'])){ 
            //actualizamos el rol
            $stmt = $db->prepare('UPDATE producto SET precio = :pc WHERE id = :id');
            $stmt->execute([':pc' => $data['precio'],':id' => $data['id']]);
                
            echo json_encode(['success' => 'El precio del producto ha sido actualizado con éxito']);
            
        }

    } catch (PDOException $e){
        echo json_encode(['error' => 'Error al actualizar los datos del productos']);
    }

}

//creamos la función para eliminar un producto
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

        //hacemos un select para obtener la imagen del producto
        $stmt = $db->prepare('SELECT foto FROM producto WHERE id = :id');
        $stmt->execute([':id' => $data['id']]);
        $comprobar_foto = $stmt->fetch();

        //obtenemos la ruta de la imagen
        $ruta_imagen = "../productos/" . $comprobar_foto['foto'];

        //en caso de que en la carpeta esté la imagen, la eliminamos de la carpeta
        if(file_exists($ruta_imagen)){
            unlink($ruta_imagen);
        }

        //eliminamos el producto con el id que nos llega
        $stmt = $db->prepare('DELETE FROM producto WHERE id = :id');
        $stmt->execute([':id' => $data['id']]);

        echo json_encode(['success' => 'El producto ha sido eliminado con éxito']);

    } catch (PDOException $e){
        echo json_encode(['error' => 'Error al eliminar el producto']);
    }

}

?>