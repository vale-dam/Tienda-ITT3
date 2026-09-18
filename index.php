<?php
session_start();

$carrito = $_SESSION["carrito"] ?? [];

$totalCarrito = 0;

foreach ($carrito as $item) {
    $totalCarrito += (int) $item["cantidad"];
}

require_once "config/conexion.php";

$sql = "SELECT 
            p.id,
            p.nombre,
            p.descripcion,
            p.precio,
            p.stock,
            c.nombre AS categoria,
            ca.nombre AS carrera,
            (
                SELECT v.imagen
                FROM variantes v
                WHERE v.producto_id = p.id
                ORDER BY v.id ASC
                LIMIT 1
            ) AS imagen
        FROM productos p
        INNER JOIN categorias c 
            ON c.id = p.categoria_id
        LEFT JOIN carreras ca 
            ON ca.id = p.carrera_id
        ORDER BY p.id DESC";

$resultado = $conexion->query($sql);
$consultaCarreras = $conexion->query(
    "SELECT id, nombre FROM carreras ORDER BY nombre"
);

$consultaCategorias = $conexion->query(
    "SELECT id, nombre FROM categorias ORDER BY nombre"
);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tienda ITT3</title>

    <link rel="stylesheet" href="/assets/css/estilos.css">

</head>

<body>

<header class="encabezado">

    <div class="logo-tienda">

        <img 
            src="assets/img/logos/logo-aguila.png"
            alt="Águila ITT3"
            class="logo-aguila"
        >

        <div class="nombre-tienda">

            <h1>Tienda ITT3</h1>

            <p>Todos somos águilas reales</p>

        </div>

        <img 
            src="assets/img/logos/logo-itt3.png"
            alt="Instituto Tecnológico de Tláhuac III"
            class="logo-institucional"
        >

    </div>

  <nav class="menu">

    <a href="index.php">
        Inicio
    </a>

    <a href="index.php">
        Productos
        <a href="pedidos/index.php">📦 Mis pedidos</a>
    </a>

    <?php if (isset($_SESSION["usuario_id"])): ?>

        <a href="#">
            👤 <?php echo htmlspecialchars($_SESSION["usuario_nombre"]); ?>
        </a>

        <a href="logout.php">
            Cerrar sesión
        </a>

    <?php else: ?>

        <a href="login.php">
            Iniciar sesión
        </a>

    <?php endif; ?>

    <a href="carrito/index.php">
        🛒 Carrito
        (<?php echo $totalCarrito; ?>)
    </a>

</nav>

    <div class="acciones">

        <button type="button">🔍</button>

        <button type="button">🛒</button>

    </div>

</header>


<main>

<div class="barra-herramientas">

    <div class="buscador">

        <input 
            type="text"
            id="busqueda"
            placeholder="Buscar productos..."
        >

        <button type="button">🔍</button>

    </div>


    <select id="filtro-carrera">

        <option value="">Todas las carreras</option>

        <?php while ($carrera = $consultaCarreras->fetch_assoc()): ?>

            <option value="<?php echo htmlspecialchars($carrera["nombre"]); ?>">
                <?php echo htmlspecialchars($carrera["nombre"]); ?>
            </option>

        <?php endwhile; ?>

    </select>


    <select id="filtro-categoria">

        <option value="">Todas las categorías</option>

        <?php while ($categoria = $consultaCategorias->fetch_assoc()): ?>

            <option value="<?php echo htmlspecialchars($categoria["nombre"]); ?>">
                <?php echo htmlspecialchars($categoria["nombre"]); ?>
            </option>

        <?php endwhile; ?>

    </select>

</div>

    </div>


    <h2>Productos</h2>


    <section>

        <?php if ($resultado && $resultado->num_rows > 0): ?>

            <?php while ($producto = $resultado->fetch_assoc()): ?>

                <article>

                    <?php if (!empty($producto["imagen"])): ?>

                        <img 
                            src="<?php echo htmlspecialchars($producto["imagen"]); ?>"
                            alt="<?php echo htmlspecialchars($producto["nombre"]); ?>"
                        >

                    <?php endif; ?>


                    <h3>

                        <?php echo htmlspecialchars($producto["nombre"]); ?>

                    </h3>


                    <p>

                        Categoría:
                        <?php echo htmlspecialchars($producto["categoria"]); ?>

                    </p>


                    <?php if (!empty($producto["carrera"])): ?>

                        <p>

                            Carrera:
                            <?php echo htmlspecialchars($producto["carrera"]); ?>

                        </p>

                    <?php endif; ?>


                    <p>

                        <?php echo htmlspecialchars($producto["descripcion"]); ?>

                    </p>


                    <strong>

                        $<?php echo number_format($producto["precio"], 2); ?>
<a 
    href="productos/detalle.php?id=<?php echo $producto['id']; ?>"
    class="btn-ver-producto"
>
    Ver producto
</a>
                    </strong>

                </article>

            <?php endwhile; ?>


        <?php else: ?>

            <p>No hay productos disponibles.</p>

        <?php endif; ?>

    </section>

</main>
<script src="assets/js/tienda.js"></script>

</body>

</html>