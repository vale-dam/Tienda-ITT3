<?php

$servidor = "127.0.0.1";
$usuario = "root";
$contraseña = "VALE1234";
$base_datos = "tienda_itt3";

$conexion = new mysqli($servidor, $usuario, $contraseña, $base_datos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");

?>