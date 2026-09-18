<?php

session_start();

require_once "../config/conexion.php";


/* =========================================
   VERIFICAR SESIÓN
   ========================================= */

if (!isset($_SESSION["usuario_id"])) {

    header("Location: ../login.php");

    exit;
}


$usuario_id = (int) $_SESSION["usuario_id"];


/* =========================================
   OBTENER PEDIDOS DEL USUARIO
   ========================================= */

$sql = "SELECT
            id,
            fecha,
            total,
            estado
        FROM ventas
        WHERE usuario_id = ?
        ORDER BY fecha DESC";


$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "i",
    $usuario_id
);

$stmt->execute();

$resultado = $stmt->get_result();

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
        Mis pedidos - Tienda ITT3
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/estilos.css"
    >

</head>


<body>


<!-- =========================================
     ENCABEZADO
     ========================================= -->

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

        <a href="../index.php">
            Productos
        </a>

        <a href="index.php">
            📦 Mis pedidos
        </a>

        <a href="../carrito/index.php">
            🛒 Carrito
        </a>

        <a href="../logout.php">
            Cerrar sesión
        </a>

    </nav>

</header>


<!-- =========================================
     CONTENIDO
     ========================================= -->

<main class="pedidos-principal">


    <div class="encabezado-pedidos">

        <h2>
            Mis pedidos
        </h2>

        <p>
            Consulta el historial de tus compras realizadas en Tienda ITT3.
        </p>

    </div>


    <?php if ($resultado->num_rows === 0): ?>


        <!-- SIN PEDIDOS -->

        <div class="sin-pedidos">

            <h3>
                Aún no tienes pedidos
            </h3>

            <p>
                Cuando realices una compra, aparecerá aquí.
            </p>

            <a href="../index.php">
                Ver productos
            </a>

        </div>


    <?php else: ?>


        <!-- LISTA DE PEDIDOS -->

        <div class="lista-pedidos">


            <?php while ($pedido = $resultado->fetch_assoc()): ?>


                <article class="pedido">


                    <!-- INFORMACIÓN DEL PEDIDO -->

                    <div class="pedido-encabezado">

                        <div>

                            <strong>
                                Pedido #<?php echo $pedido["id"]; ?>
                            </strong>

                            <p>

                                Fecha:
                                <?php
                                echo date(
                                    "d/m/Y H:i",
                                    strtotime($pedido["fecha"])
                                );
                                ?>

                            </p>

                        </div>


                        <span class="estado-pedido">

                            <?php
                            echo htmlspecialchars(
                                $pedido["estado"]
                            );
                            ?>

                        </span>

                    </div>


                    <!-- PRODUCTOS DEL PEDIDO -->

                    <div class="productos-pedido">

                        <h3>
                            Productos
                        </h3>


                        <?php

                        $venta_id = (int) $pedido["id"];


                        $sqlDetalle = "SELECT

                                            p.nombre,

                                            dv.cantidad,

                                            dv.precio_unitario,

                                            dv.subtotal,

                                            v.color,

                                            v.talla,

                                            v.imagen

                                        FROM detalle_ventas dv

                                        INNER JOIN productos p
                                            ON p.id = dv.producto_id

                                        LEFT JOIN variantes v
                                            ON v.id = dv.variante_id

                                        WHERE dv.venta_id = ?

                                        ORDER BY dv.id ASC";


                        $stmtDetalle =
                            $conexion->prepare(
                                $sqlDetalle
                            );


                        $stmtDetalle->bind_param(
                            "i",
                            $venta_id
                        );


                        $stmtDetalle->execute();


                        $resultadoDetalle =
                            $stmtDetalle->get_result();

                        ?>


                        <?php if ($resultadoDetalle->num_rows > 0): ?>


                            <div class="lista-productos-pedido">


                                <?php while (
                                    $producto = $resultadoDetalle->fetch_assoc()
                                ): ?>


                                    <div class="producto-pedido">


                                        <!-- IMAGEN -->

                                        <div class="imagen-producto-pedido">

                                            <?php if (
                                                !empty($producto["imagen"])
                                            ): ?>

                                                <img
                                                    src="../<?php
                                                        echo htmlspecialchars(
                                                            $producto["imagen"]
                                                        );
                                                    ?>"
                                                    alt="<?php
                                                        echo htmlspecialchars(
                                                            $producto["nombre"]
                                                        );
                                                    ?>"
                                                >

                                            <?php endif; ?>

                                        </div>


                                        <!-- INFORMACIÓN -->

                                        <div class="informacion-producto-pedido">

                                            <h4>

                                                <?php
                                                echo htmlspecialchars(
                                                    $producto["nombre"]
                                                );
                                                ?>

                                            </h4>


                                            <?php if (
                                                !empty($producto["color"])
                                            ): ?>

                                                <p>

                                                    <strong>
                                                        Color:
                                                    </strong>

                                                    <?php
                                                    echo htmlspecialchars(
                                                        $producto["color"]
                                                    );
                                                    ?>

                                                </p>

                                            <?php endif; ?>


                                            <?php if (
                                                !empty($producto["talla"]) &&
                                                $producto["talla"] !== "Única"
                                            ): ?>

                                                <p>

                                                    <strong>
                                                        Talla:
                                                    </strong>

                                                    <?php
                                                    echo htmlspecialchars(
                                                        $producto["talla"]
                                                    );
                                                    ?>

                                                </p>

                                            <?php endif; ?>


                                            <p>

                                                <strong>
                                                    Cantidad:
                                                </strong>

                                                <?php
                                                echo (int)
                                                    $producto["cantidad"];
                                                ?>

                                            </p>

                                        </div>


                                        <!-- PRECIO -->

                                        <div class="precio-producto-pedido">

                                            <span>
                                                Precio
                                            </span>

                                            <strong>

                                                $<?php
                                                echo number_format(
                                                    $producto["precio_unitario"],
                                                    2
                                                );
                                                ?>

                                            </strong>


                                            <small>

                                                Subtotal:
                                                $<?php
                                                echo number_format(
                                                    $producto["subtotal"],
                                                    2
                                                );
                                                ?>

                                            </small>

                                        </div>


                                    </div>


                                <?php endwhile; ?>


                            </div>


                        <?php else: ?>


                            <p>
                                No se encontraron productos para este pedido.
                            </p>


                        <?php endif; ?>


                        <?php

                        $stmtDetalle->close();

                        ?>

                    </div>


                    <!-- TOTAL -->

                    <div class="pedido-informacion">

                        <div>

                            <span>
                                Total del pedido
                            </span>

                            <strong>

                                $<?php
                                echo number_format(
                                    $pedido["total"],
                                    2
                                );
                                ?>

                            </strong>

                        </div>

                    </div>


                </article>


            <?php endwhile; ?>


        </div>


    <?php endif; ?>


</main>


</body>

</html>


<?php

$stmt->close();

$conexion->close();

?>