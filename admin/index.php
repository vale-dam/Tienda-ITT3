<?php
session_start();

require_once "../config/conexion.php";

/* Verificar que haya sesión */
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../login.php");
    exit;
}

/* Verificar que sea administrador */
if (!isset($_SESSION["usuario_rol"]) || $_SESSION["usuario_rol"] !== "administrador") {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Panel de administración - Tienda ITT3</title>

    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>

<body>

<header class="encabezado">

    <div class="logo-principal">
        <img src="../assets/img/logos/logo-aguila.png" alt="Águila Real">
    </div>

    <div class="titulo-tienda">
        <h1>TIENDA ITT3</h1>
        <p>Panel de administración</p>
    </div>

    <div class="logo-instituto">
        <img src="../assets/img/logos/logo-itt3.png" alt="ITT3">
    </div>

</header>

<nav class="menu">

    <a href="../index.php">Inicio</a>

    <a href="../index.php">Productos</a>

    <a href="../pedidos/index.php">📦 Mis pedidos</a>

    <a href="../carrito/index.php">🛒 Carrito</a>

    <a href="../logout.php">Cerrar sesión</a>

</nav>


<main class="admin-principal">

    <div class="encabezado-admin">

        <h2>Panel de administración</h2>

        <p>
            Bienvenido,
            <?php echo htmlspecialchars($_SESSION["usuario_nombre"]); ?>.
        </p>

    </div>


    <section class="admin-opciones">

        <article class="admin-card">
            <h3>👥 Usuarios</h3>
            <p>Consultar los usuarios registrados en la tienda.</p>
        </article>

        <article class="admin-card">
            <h3>📦 Productos</h3>
            <p>Consultar los productos disponibles.</p>
        </article>

        <article class="admin-card">
            <h3>🎓 Carreras</h3>
            <p>Consultar las carreras de la institución.</p>
        </article>

        <article class="admin-card">
            <h3>🏷️ Categorías</h3>
            <p>Consultar las categorías de productos.</p>
        </article>

        <article class="admin-card">
            <h3>📊 Inventario</h3>
            <p>Consultar existencias y variantes.</p>
        </article>

        <article class="admin-card">
            <h3>🛒 Pedidos</h3>
            <p>Consultar las ventas realizadas.</p>
        </article>

    </section>

</main>


<footer>

    <p>
        © <?php echo date("Y"); ?> Tienda ITT3 -
        Todos somos águilas reales
    </p>

</footer>

</body>
</html>