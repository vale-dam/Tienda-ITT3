<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$producto_id = isset($_POST["producto_id"])
    ? (int) $_POST["producto_id"]
    : 0;

$variante_id = isset($_POST["variante_id"])
    ? (int) $_POST["variante_id"]
    : 0;

$cantidad = isset($_POST["cantidad"])
    ? (int) $_POST["cantidad"]
    : 0;


/* VALIDAR DATOS */

if (
    $producto_id <= 0 ||
    $variante_id <= 0 ||
    $cantidad <= 0
) {
    header("Location: index.php");
    exit;
}


/* ACTUALIZAR CANTIDAD */

if (isset($_SESSION["carrito"])) {

    foreach ($_SESSION["carrito"] as &$item) {

        if (
            $item["producto_id"] === $producto_id &&
            $item["variante_id"] === $variante_id
        ) {

            $item["cantidad"] = $cantidad;

            break;
        }

    }

    unset($item);
}


/* REGRESAR AL CARRITO */

header("Location: index.php");
exit;

?>