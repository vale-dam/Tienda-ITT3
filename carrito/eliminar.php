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


if ($producto_id <= 0 || $variante_id <= 0) {
    header("Location: index.php");
    exit;
}


if (isset($_SESSION["carrito"])) {

    foreach ($_SESSION["carrito"] as $indice => $item) {

        if (
            $item["producto_id"] === $producto_id &&
            $item["variante_id"] === $variante_id
        ) {

            unset($_SESSION["carrito"][$indice]);

            break;
        }

    }


    $_SESSION["carrito"] =
        array_values($_SESSION["carrito"]);

}


header("Location: index.php");
exit;

?>