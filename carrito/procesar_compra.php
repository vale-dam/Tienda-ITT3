<?php

session_start();

require_once "../config/conexion.php";


// Verificar que el usuario haya iniciado sesión

if (!isset($_SESSION["usuario_id"])) {

    header("Location: ../login.php");

    exit;
}


// Verificar que el carrito tenga productos

$carrito = $_SESSION["carrito"] ?? [];

if (empty($carrito)) {

    header("Location: index.php");

    exit;
}


$usuario_id = (int) $_SESSION["usuario_id"];


// Calcular el total

$total = 0;

$productosCompra = [];


foreach ($carrito as $item) {

    $producto_id = (int) $item["producto_id"];

    $variante_id = (int) $item["variante_id"];

    $cantidad = (int) $item["cantidad"];


    if ($producto_id <= 0 || $variante_id <= 0 || $cantidad <= 0) {

        die("Datos del carrito no válidos.");

    }


    $sql = "SELECT
                p.nombre,
                p.precio,
                v.color,
                v.talla,
                v.stock
            FROM productos p

            INNER JOIN variantes v
                ON v.producto_id = p.id

            WHERE p.id = ?
            AND v.id = ?";


    $stmt = $conexion->prepare($sql);

    $stmt->bind_param(
        "ii",
        $producto_id,
        $variante_id
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    $producto = $resultado->fetch_assoc();


    if (!$producto) {

        die("Uno de los productos ya no está disponible.");

    }


    // Verificar stock

    if ($cantidad > $producto["stock"]) {

        die(
            "No hay suficiente stock para: " .
            htmlspecialchars($producto["nombre"])
        );

    }


    $subtotal =
        $producto["precio"] * $cantidad;

    $total += $subtotal;


    $productosCompra[] = [

        "producto_id" => $producto_id,

        "variante_id" => $variante_id,

        "cantidad" => $cantidad,

        "precio" => $producto["precio"],

        "subtotal" => $subtotal

    ];
}


// Iniciar transacción

$conexion->begin_transaction();


try {


    // Crear la venta

    $sqlVenta = "INSERT INTO ventas
                (usuario_id, total, estado)
                VALUES (?, ?, 'Pendiente')";


    $stmtVenta = $conexion->prepare($sqlVenta);

    $stmtVenta->bind_param(
        "id",
        $usuario_id,
        $total
    );

    $stmtVenta->execute();


    $venta_id = $conexion->insert_id;


    // Insertar los productos de la venta

    $sqlDetalle = "INSERT INTO detalle_ventas
                  (
                      venta_id,
                      producto_id,
                      variante_id,
                      cantidad,
                      precio_unitario,
                      subtotal
                  )
                  VALUES (?, ?, ?, ?, ?, ?)";


    $stmtDetalle = $conexion->prepare($sqlDetalle);


    foreach ($productosCompra as $producto) {


        $stmtDetalle->bind_param(
            "iiiidd",
            $venta_id,
            $producto["producto_id"],
            $producto["variante_id"],
            $producto["cantidad"],
            $producto["precio"],
            $producto["subtotal"]
        );


        $stmtDetalle->execute();


        // Descontar stock de la variante

        $sqlStock = "UPDATE variantes
                     SET stock = stock - ?
                     WHERE id = ?
                     AND stock >= ?";


        $stmtStock = $conexion->prepare($sqlStock);


        $stmtStock->bind_param(
            "iii",
            $producto["cantidad"],
            $producto["variante_id"],
            $producto["cantidad"]
        );


        $stmtStock->execute();


        if ($stmtStock->affected_rows === 0) {

            throw new Exception(
                "No se pudo actualizar el stock."
            );

        }

    }


    // Confirmar la transacción

    $conexion->commit();


    // Vaciar carrito

    $_SESSION["carrito"] = [];


    // Guardar ID de la venta

    $_SESSION["venta_id"] = $venta_id;


} catch (Exception $e) {


    // Cancelar cambios si ocurrió un error

    $conexion->rollback();


    die(
        "No se pudo procesar la compra: " .
        htmlspecialchars($e->getMessage())
    );

}

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Compra realizada - Tienda ITT3
    </title>


    <link
        rel="stylesheet"
        href="/assets/css/estilos.css"
    >

</head>


<body>


<header class="encabezado">


    <div class="logo-tienda">


        <img
            src="../assets/img/logos/logo-aguila.png"
            alt="Águila ITT3"
            class="logo-aguila"
        >


        <div class="nombre-tienda">

            <h1>
                Tienda ITT3
            </h1>

            <p>
                Todos somos águilas reales
            </p>

        </div>


        <img
            src="../assets/img/logos/logo-itt3.png"
            alt="Instituto Tecnológico de Tláhuac III"
            class="logo-institucional"
        >


    </div>


</header>



<main>


    <section class="formulario-usuario">


        <h2>
            ¡Compra realizada!
        </h2>


        <p>

            Tu compra se registró correctamente.

        </p>


        <p>

            Número de pedido:

            <strong>
                #<?php echo $venta_id; ?>
            </strong>

        </p>


        <p>

            Total pagado:

            <strong>

                $<?php
                echo number_format(
                    $total,
                    2
                );
                ?>

            </strong>

        </p>


        <br>


        <a
            href="../index.php"
            class="btn-ver-producto"
        >
            Regresar a la tienda
        </a>


    </section>


</main>


</body>

</html>