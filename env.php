<?php

function cargarEnv($ruta) {
    if (!file_exists($ruta)) {
        throw new Exception(".env no encontrado");
    }

    $lineas = file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lineas as $linea) {
        // ignorar comentarios
        if (str_starts_with(trim($linea), '#')) {
            continue;
        }

        // separar clave=valor
        list($clave, $valor) = explode('=', $linea, 2);

        $clave = trim($clave);
        $valor = trim($valor);

        // guardar en variables de entorno
        putenv("$clave=$valor");
        $_ENV[$clave] = $valor;
        $_SERVER[$clave] = $valor;
    }
}

function env($key, $default = null) {
    return $_ENV[$key] ?? $default;
}