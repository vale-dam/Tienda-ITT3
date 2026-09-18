<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../index.php");
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
    : 1;

if ($producto_id <= 0 || $variante_id <= 0 || $cantidad <= 0) {
    die("Datos del producto no válidos.");
}

if (!isset($_SESSION["carrito"])) {
    $_SESSION["carrito"] = [];
}

$encontrado = false;

foreach ($_SESSION["carrito"] as &$item) {

    if (
        $item["producto_id"] === $producto_id &&
        $item["variante_id"] === $variante_id
    ) {

        $item["cantidad"] += $cantidad;
        $encontrado = true;

        break;
    }
}

unset($item);

if (!$encontrado) {

    $_SESSION["carrito"][] = [
        "producto_id" => $producto_id,
        "variante_id" => $variante_id,
        "cantidad" => $cantidad
    ];

}

header("Location: index.php");
exit;