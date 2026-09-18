<?php

session_start();

require_once "config/conexion.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $correo = trim($_POST["correo"] ?? "");
    $contraseña = $_POST["contraseña"] ?? "";

    if ($correo === "" || $contraseña === "") {

        $mensaje = "Completa todos los campos.";

    } else {

        $sql = "SELECT id, nombre, correo, contraseña, rol
                FROM usuarios
                WHERE correo = ?";

        $stmt = $conexion->prepare($sql);

        $stmt->bind_param("s", $correo);

        $stmt->execute();

        $resultado = $stmt->get_result();

        $usuario = $resultado->fetch_assoc();


        if ($usuario && password_verify($contraseña, $usuario["contraseña"])) {

            $_SESSION["usuario_id"] = $usuario["id"];
            $_SESSION["usuario_nombre"] = $usuario["nombre"];
            $_SESSION["usuario_correo"] = $usuario["correo"];
            $_SESSION["usuario_rol"] = $usuario["rol"];

            header("Location: index.php");

            exit;

        } else {

            $mensaje = "Correo o contraseña incorrectos.";

        }
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
        Iniciar sesión - Tienda ITT3
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
            src="assets/img/logos/logo-aguila.png"
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
            src="assets/img/logos/logo-itt3.png"
            alt="Instituto Tecnológico de Tláhuac III"
            class="logo-institucional"
        >

    </div>

</header>


<main>

    <section class="formulario-usuario">

        <h2>
            Iniciar sesión
        </h2>


        <?php if ($mensaje !== ""): ?>

            <p class="mensaje-error">
                <?php echo htmlspecialchars($mensaje); ?>
            </p>

        <?php endif; ?>


        <form
            action="login.php"
            method="POST"
        >

            <label for="correo">
                Correo electrónico
            </label>

            <input
                type="email"
                id="correo"
                name="correo"
                required
            >


            <label for="contraseña">
                Contraseña
            </label>

            <input
                type="password"
                id="contraseña"
                name="contraseña"
                required
            >


            <button
                type="submit"
                class="btn-carrito"
            >
                Iniciar sesión
            </button>

        </form>


        <p>

            ¿No tienes una cuenta?

            <a href="registro.php">
                Regístrate
            </a>

        </p>


        <p>

            <a href="index.php">
                Regresar a la tienda
            </a>

        </p>

    </section>

</main>


</body>

</html>