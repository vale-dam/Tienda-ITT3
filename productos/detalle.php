<?php

require_once "../config/conexion.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Producto no válido.");
}

$producto_id = (int) $_GET["id"];


/* OBTENER PRODUCTO */

$sql = "SELECT 
            p.id,
            p.nombre,
            p.descripcion,
            p.precio,
            p.stock,
            c.nombre AS categoria,
            ca.nombre AS carrera
        FROM productos p
        INNER JOIN categorias c
            ON c.id = p.categoria_id
        LEFT JOIN carreras ca
            ON ca.id = p.carrera_id
        WHERE p.id = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $producto_id);
$stmt->execute();

$resultado = $stmt->get_result();
$producto = $resultado->fetch_assoc();

if (!$producto) {
    die("Producto no encontrado.");
}


/* OBTENER VARIANTES */

$sqlVariantes = "SELECT
                    id,
                    color,
                    talla,
                    stock,
                    imagen
                 FROM variantes
                 WHERE producto_id = ?
                 ORDER BY id ASC";

$stmtVariantes = $conexion->prepare($sqlVariantes);
$stmtVariantes->bind_param("i", $producto_id);
$stmtVariantes->execute();

$variantes = $stmtVariantes->get_result();


/* GUARDAR VARIANTES EN UN ARREGLO */

$listaVariantes = [];

while ($variante = $variantes->fetch_assoc()) {
    $listaVariantes[] = $variante;
}


/* OBTENER PRIMERA IMAGEN */

$primeraVariante = null;

if (!empty($listaVariantes)) {
    $primeraVariante = $listaVariantes[0];
}


/* OBTENER COLORES */

$colores = [];

foreach ($listaVariantes as $variante) {

    if (!in_array($variante["color"], $colores)) {
        $colores[] = $variante["color"];
    }

}


/* OBTENER TALLAS */

$tallas = [];

foreach ($listaVariantes as $variante) {

    if (
        $variante["talla"] !== "Única" &&
        !in_array($variante["talla"], $tallas)
    ) {
        $tallas[] = $variante["talla"];
    }

}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo htmlspecialchars($producto["nombre"]); ?> - Tienda ITT3
    </title>

    <link rel="stylesheet" href="/assets/css/estilos.css">

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
            Carreras
        </a>

        <a href="../index.php">
            Productos
        </a>

    </nav>


    <div class="acciones">

        <button type="button">
            🔍
        </button>

        <button type="button">
            🛒
        </button>

    </div>

</header>



<main class="detalle-producto">


    <a href="../index.php" class="volver">
        ← Volver a productos
    </a>



    <div class="producto-detalle">


        <!-- IMAGEN DEL PRODUCTO -->

        <div class="galeria-producto">

            <div class="imagen-principal">

                <?php if ($primeraVariante && !empty($primeraVariante["imagen"])): ?>

                    <img
                        id="imagen-producto"
                        src="../<?php echo htmlspecialchars($primeraVariante["imagen"]); ?>"
                        alt="<?php echo htmlspecialchars($producto["nombre"]); ?>"
                    >

                <?php endif; ?>

            </div>

        </div>



        <!-- INFORMACIÓN DEL PRODUCTO -->

        <div class="informacion-producto">


            <p class="categoria-producto">

                <?php echo htmlspecialchars($producto["categoria"]); ?>

            </p>



            <h2>

                <?php echo htmlspecialchars($producto["nombre"]); ?>

            </h2>



            <?php if (!empty($producto["carrera"])): ?>

                <p class="carrera-producto">

                    <?php echo htmlspecialchars($producto["carrera"]); ?>

                </p>

            <?php endif; ?>



            <p class="precio-producto">

                $<?php echo number_format($producto["precio"], 2); ?>

            </p>



            <p class="descripcion-producto">

                <?php echo htmlspecialchars($producto["descripcion"]); ?>

            </p>



            <!-- COLOR -->

            <h3>
                Color
            </h3>


            <div class="opciones-color">

                <?php foreach ($colores as $color): ?>

                    <button
                        type="button"
                        class="opcion-color"
                        data-color="<?php echo htmlspecialchars($color); ?>"
                    >

                        <?php echo htmlspecialchars($color); ?>

                    </button>

                <?php endforeach; ?>

            </div>



            <!-- TALLA -->

            <?php if (!empty($tallas)): ?>

                <h3>
                    Talla
                </h3>


                <div class="opciones-talla">

                    <?php foreach ($tallas as $talla): ?>

                        <button
                            type="button"
                            class="opcion-talla"
                            data-talla="<?php echo htmlspecialchars($talla); ?>"
                        >

                            <?php echo htmlspecialchars($talla); ?>

                        </button>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>



            <!-- FORMULARIO DEL CARRITO -->

            <form
                action="../carrito/agregar.php"
                method="POST"
            >


                <!-- ID DEL PRODUCTO -->

                <input
                    type="hidden"
                    name="producto_id"
                    value="<?php echo $producto["id"]; ?>"
                >


                <!-- ID DE LA VARIANTE -->

                <input
                    type="hidden"
                    name="variante_id"
                    id="variante-form"
                    value=""
                >


                <!-- CANTIDAD -->

                <div class="cantidad-producto">

                    <label for="cantidad">
                        Cantidad:
                    </label>


                    <input
                        type="number"
                        id="cantidad"
                        value="1"
                        min="1"
                    >


                    <input
                        type="hidden"
                        name="cantidad"
                        id="cantidad-form"
                        value="1"
                    >

                </div>



                <!-- BOTÓN -->

                <button
                    type="submit"
                    class="btn-carrito"
                >

                    Agregar al carrito

                </button>


            </form>


        </div>

    </div>

</main>



<script>


/* VARIANTES */

const variantes =
    <?php echo json_encode($listaVariantes); ?>;


/* BOTONES */

const botonesColor =
    document.querySelectorAll(".opcion-color");

const botonesTalla =
    document.querySelectorAll(".opcion-talla");


/* ELEMENTOS */

const imagenProducto =
    document.getElementById("imagen-producto");

const varianteSeleccionada =
    document.getElementById("variante-form");

const cantidad =
    document.getElementById("cantidad");

const cantidadForm =
    document.getElementById("cantidad-form");


/* VARIABLES */

let colorSeleccionado = "";

let tallaSeleccionada = "";



/* CAMBIAR COLOR */

botonesColor.forEach(function(boton) {

    boton.addEventListener("click", function() {


        colorSeleccionado =
            boton.getAttribute("data-color");


        botonesColor.forEach(function(item) {

            item.classList.remove("seleccionada");

        });


        boton.classList.add("seleccionada");


        actualizarProducto();

    });

});



/* CAMBIAR TALLA */

botonesTalla.forEach(function(boton) {

    boton.addEventListener("click", function() {


        tallaSeleccionada =
            boton.getAttribute("data-talla");


        botonesTalla.forEach(function(item) {

            item.classList.remove("seleccionada");

        });


        boton.classList.add("seleccionada");


        actualizarProducto();

    });

});



/* CAMBIAR CANTIDAD */

cantidad.addEventListener("input", function() {

    let valor = parseInt(cantidad.value);


    if (isNaN(valor) || valor < 1) {

        valor = 1;

        cantidad.value = 1;

    }


    cantidadForm.value = valor;

});



/* BUSCAR VARIANTE */

function actualizarProducto() {


    let varianteEncontrada = null;



    /* PRODUCTOS CON TALLA */

    if (botonesTalla.length > 0) {


        if (
            colorSeleccionado === "" ||
            tallaSeleccionada === ""
        ) {

            varianteSeleccionada.value = "";

            return;

        }


        varianteEncontrada =
            variantes.find(function(variante) {

                return (
                    variante.color === colorSeleccionado &&
                    variante.talla === tallaSeleccionada
                );

            });

    }



    /* PRODUCTOS SIN TALLA */

    else {


        if (colorSeleccionado === "") {

            varianteSeleccionada.value = "";

            return;

        }


        varianteEncontrada =
            variantes.find(function(variante) {

                return (
                    variante.color === colorSeleccionado
                );

            });

    }



    /* VARIANTE ENCONTRADA */

    if (varianteEncontrada) {


        /* CAMBIAR IMAGEN */

        if (
            imagenProducto &&
            varianteEncontrada.imagen
        ) {

            imagenProducto.src =
                "../" + varianteEncontrada.imagen;

        }


        /* GUARDAR ID DE VARIANTE */

        varianteSeleccionada.value =
            varianteEncontrada.id;

    }

}


</script>


</body>

</html>