<?php

//creamos una clase Database
class Database{
    //le añadimos los atributos para realizar la conexión a una base de datso
    private $usuario;
    private $contrasena;
    private $baseDeDatos;
    private $host;
    public $conexion;

    //constructor de la clase
    public function __construct(){
        $this->usuario = "";
        $this->contrasena = "";
        $this->baseDeDatos = "codigo_gertrudis";
        $this->host = "localhost";
    }

    //creamos una funcion para hacer la conexion a la base de datos
    public function conexionBaseDeDatos(){
        $this->conexion = null;

        //hacemos un bloque try-catch para manejar errores
        try{
            $this->conexion = new PDO("mysql:host={$this->host};dbname={$this->baseDeDatos};charset=utf8mb4", $this->usuario, $this->contrasena);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $excepcion){
            echo "Error de conexion a la base de datos";
        }

        return $this->conexion;

    }
    
}
?>