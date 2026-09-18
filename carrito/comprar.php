<?php

session_start();

require_once "../config/conexion.php";


// Verificar que haya productos en el carrito

$carrito = $_SESSION["carrito"] ?? [];

if (empty($carrito)) {

    header("Location: index.php");

    exit;
}


// Calcular total

$total = 0;

$productosCompra = [];


foreach ($carrito as $item) {

    $producto_id = (int) $item["producto_id"];

    $variante_id = (int) $item["variante_id"];

    $cantidad = (int) $item["cantidad"];


    $sql = "SELECT
                p.nombre,
                p.precio,
                v.color,
                v.talla,
                v.imagen
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


    if ($producto) {

        $subtotal =
            $producto["precio"] * $cantidad;

        $total += $subtotal;


        $producto["producto_id"] = $producto_id;

        $producto["variante_id"] = $variante_id;

        $producto["cantidad"] = $cantidad;

        $producto["subtotal"] = $subtotal;


        $productosCompra[] = $producto;
    }
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
        Confirmar compra - Tienda ITT3
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


    <nav class="menu">

        <a href="../index.php">
            Inicio
        </a>

        <a href="index.php">
            Carrito
        </a>

    </nav>


</header>



<main>


    <h2>
        Confirmar compra
    </h2>


    <p>
        Revisa los productos antes de confirmar tu pedido.
    </p>



    <section>


        <?php foreach ($productosCompra as $producto): ?>


            <article>


                <?php if (!empty($producto["imagen"])): ?>

                    <img
                        src="../<?php echo htmlspecialchars($producto["imagen"]); ?>"
                        alt="<?php echo htmlspecialchars($producto["nombre"]); ?>"
                    >

                <?php endif; ?>


                <h3>

                    <?php
                    echo htmlspecialchars(
                        $producto["nombre"]
                    );
                    ?>

                </h3>


                <p>

                    Color:

                    <?php
                    echo htmlspecialchars(
                        $producto["color"]
                    );
                    ?>

                </p>


                <?php if ($producto["talla"] !== "Única"): ?>

                    <p>

                        Talla:

                        <?php
                        echo htmlspecialchars(
                            $producto["talla"]
                        );
                        ?>

                    </p>

                <?php endif; ?>


                <p>

                    Cantidad:

                    <?php
                    echo $producto["cantidad"];
                    ?>

                </p>


                <p>

                    Precio:

                    <strong>

                        $<?php
                        echo number_format(
                            $producto["precio"],
                            2
                        );
                        ?>

                    </strong>

                </p>


                <p>

                    Subtotal:

                    <strong>

                        $<?php
                        echo number_format(
                            $producto["subtotal"],
                            2
                        );
                        ?>

                    </strong>

                </p>


            </article>


        <?php endforeach; ?>


    </section>



    <section class="total-carrito">


        <h2>

            Total de la compra:

            $<?php
            echo number_format(
                $total,
                2
            );
            ?>

        </h2>


        <form
            action="procesar_compra.php"
            method="POST"
        >

            <button
                type="submit"
                class="btn-comprar"
            >
                Confirmar compra
            </button>

        </form>


        <br>


        <a
            href="index.php"
            class="btn-ver-producto"
        >
            Regresar al carrito
        </a>


    </section>


</main>


</body>

</html>