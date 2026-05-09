<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../env.php';
cargarEnv(__DIR__ . '/../.env');
//añadimos las cabeceras necesarias
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true"); //estas dos son para poder pasar la cookie de session php
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE'); // añadimos los métodos
header('Access-Control-Allow-Headers: Content-Type');
header("Content-Type: application/json"); //para json

//en caso de que se seleccione una opcción, damos respuesta 200
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

//para realizar una petición u otra dependiendo de lo que nos llegue por la ruta
$ruta = explode('/', trim($_SERVER['PATH_INFO'], '/')); //obtenemos la ruta
$seccion = $ruta[0] ?? null; // obtenemos a que "sección" estamos accediendo (productos, usuarios, etc)
$metodo = $_SERVER['REQUEST_METHOD'];

//switch para realizar una petición u otra dependiendo de la ruta
switch ($seccion) {
    case 'usuarios':
        include 'usuarios.php';
        break;
    case 'productos':
        include 'productos.php';
        break;
    case 'valoraciones':
        include 'valoraciones.php';
        break;
    case 'juegos':
        include 'juegos.php';
        break;
    case 'galeria':
        include 'galeria.php';
        break;
    case 'blog':
        include 'blog.php';
        break;
    case 'carrito':
        include 'carrito.php';
        break;
    case 'pedidos':
        include 'pedidos.php';
        break;
    default:
        echo json_encode(['error' => 'No se ha encontrado la sección que buscas']);
        break;
}

?>