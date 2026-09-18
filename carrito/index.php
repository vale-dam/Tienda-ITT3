<?php

session_start();

require_once "../config/conexion.php";

$carrito = $_SESSION["carrito"] ?? [];

$total = 0;

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Carrito - Tienda ITT3</title>

    <link rel="stylesheet" href="../assets/css/estilos.css">

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

            <h1>Tienda ITT3</h1>

            <p>Todos somos águilas reales</p>

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

        <a href="../index.php">
            Productos
        </a>

        <?php if (isset($_SESSION["usuario_id"])): ?>

            <a href="../logout.php">
                Cerrar sesión
            </a>

        <?php else: ?>

            <a href="../login.php">
                Iniciar sesión
            </a>

        <?php endif; ?>

    </nav>

</header>


<main class="carrito-principal">

    <h2>Mi carrito</h2>


    <?php if (empty($carrito)): ?>

        <div class="carrito-vacio">

            <h3>Tu carrito está vacío</h3>

            <p>
                Agrega algunos productos para continuar con tu compra.
            </p>

            <a
                href="../index.php"
                class="btn-seguir-comprando"
            >
                Ver productos
            </a>

        </div>


    <?php else: ?>


        <div class="lista-carrito">


            <?php foreach ($carrito as $item): ?>


                <?php

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

                $stmt->close();

                ?>


                <?php if ($producto): ?>


                    <?php

                    $subtotal =
                        (float) $producto["precio"] * $cantidad;

                    $total += $subtotal;

                    ?>


                    <article class="producto-carrito">


                        <!-- IMAGEN -->

                        <div class="imagen-carrito">

                            <?php if (!empty($producto["imagen"])): ?>

                                <img
                                    src="../<?php echo htmlspecialchars($producto["imagen"]); ?>"
                                    alt="<?php echo htmlspecialchars($producto["nombre"]); ?>"
                                >

                            <?php endif; ?>

                        </div>


                        <!-- INFORMACIÓN -->

                        <div class="informacion-carrito">

                            <h3>

                                <?php

                                echo htmlspecialchars(
                                    $producto["nombre"]
                                );

                                ?>

                            </h3>


                            <p>

                                <strong>Color:</strong>

                                <?php

                                echo htmlspecialchars(
                                    $producto["color"]
                                );

                                ?>

                            </p>


                            <?php if ($producto["talla"] !== "Única"): ?>

                                <p>

                                    <strong>Talla:</strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $producto["talla"]
                                    );

                                    ?>

                                </p>

                            <?php endif; ?>


                            <p class="precio-carrito">

                                <span>Precio:</span>

                                <strong>

                                    $<?php

                                    echo number_format(
                                        $producto["precio"],
                                        2
                                    );

                                    ?>

                                </strong>

                            </p>


                            <p class="subtotal-carrito">

                                <span>Subtotal:</span>

                                <strong>

                                    $<?php

                                    echo number_format(
                                        $subtotal,
                                        2
                                    );

                                    ?>

                                </strong>

                            </p>


                            <!-- CANTIDAD -->

                            <div class="cantidad-carrito">

                                <span class="texto-cantidad">
                                    Cantidad:
                                </span>


                                <form
                                    action="actualizar.php"
                                    method="POST"
                                >

                                    <input
                                        type="hidden"
                                        name="producto_id"
                                        value="<?php echo $producto_id; ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="variante_id"
                                        value="<?php echo $variante_id; ?>"
                                    >


                                    <button
                                        type="submit"
                                        name="cantidad"
                                        value="<?php echo max(1, $cantidad - 1); ?>"
                                        aria-label="Disminuir cantidad"
                                    >
                                        −
                                    </button>


                                    <strong class="numero-cantidad">
                                        <?php echo $cantidad; ?>
                                    </strong>


                                    <button
                                        type="submit"
                                        name="cantidad"
                                        value="<?php echo $cantidad + 1; ?>"
                                        aria-label="Aumentar cantidad"
                                    >
                                        +
                                    </button>

                                </form>

                            </div>


                            <!-- ELIMINAR -->

                            <form
                                action="eliminar.php"
                                method="POST"
                                class="form-eliminar"
                            >

                                <input
                                    type="hidden"
                                    name="producto_id"
                                    value="<?php echo $producto_id; ?>"
                                >

                                <input
                                    type="hidden"
                                    name="variante_id"
                                    value="<?php echo $variante_id; ?>"
                                >


                                <button type="submit">
                                    Eliminar
                                </button>

                            </form>

                        </div>

                    </article>


                <?php endif; ?>


            <?php endforeach; ?>


        </div>


        <!-- RESUMEN -->

        <section class="resumen-carrito">

            <div class="total-resumen">

                <span>
                    Total de la compra
                </span>

                <strong>

                    $<?php

                    echo number_format(
                        $total,
                        2
                    );

                    ?>

                </strong>

            </div>


            <div class="acciones-carrito">

                <a
                    href="../index.php"
                    class="btn-seguir-comprando"
                >
                    Seguir comprando
                </a>


                <a
                    href="comprar.php"
                    class="btn-comprar"
                >
                    Comprar
                </a>

            </div>

        </section>


    <?php endif; ?>


</main>

</body>

</html>