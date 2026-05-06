<?php

class Database {

    private $conexion;

    public function conexionBaseDeDatos(){

        $host = env('DB_HOST');
        $dbname = env('DB_NAME');
        $user = env('DB_USER');
        $pass = env('DB_PASS');

        try {
            $this->conexion = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $user,
                $pass
            );

            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch (PDOException $e){
            echo json_encode(['error' => 'Error de conexión']);
            exit;
        }

        return $this->conexion;
    }
}