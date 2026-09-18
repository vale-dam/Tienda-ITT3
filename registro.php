<?php

session_start();

require_once "config/conexion.php";

$mensaje = "";
$tipo_mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["nombre"] ?? "");
    $correo = trim($_POST["correo"] ?? "");
    $contraseña = $_POST["contraseña"] ?? "";
    $confirmar = $_POST["confirmar"] ?? "";

    if (
        $nombre === "" ||
        $correo === "" ||
        $contraseña === "" ||
        $confirmar === ""
    ) {

        $mensaje = "Completa todos los campos.";
        $tipo_mensaje = "error";

    } elseif ($contraseña !== $confirmar) {

        $mensaje = "Las contraseñas no coinciden.";
        $tipo_mensaje = "error";

    } elseif (strlen($contraseña) < 6) {

        $mensaje = "La contraseña debe tener al menos 6 caracteres.";
        $tipo_mensaje = "error";

    } else {

        // Comprobar si el correo ya existe

        $sql = "SELECT id
                FROM usuarios
                WHERE correo = ?";

        $stmt = $conexion->prepare($sql);

        $stmt->bind_param("s", $correo);

        $stmt->execute();

        $resultado = $stmt->get_result();


        if ($resultado->num_rows > 0) {

            $mensaje = "Este correo ya está registrado.";
            $tipo_mensaje = "error";

        } else {

            // Encriptar contraseña

            $contraseña_segura = password_hash(
                $contraseña,
                PASSWORD_DEFAULT
            );


            // Registrar usuario

            $sql = "INSERT INTO usuarios
                    (nombre, correo, contraseña, rol)
                    VALUES (?, ?, ?, 'cliente')";

            $stmt = $conexion->prepare($sql);

            $stmt->bind_param(
                "sss",
                $nombre,
                $correo,
                $contraseña_segura
            );


            if ($stmt->execute()) {

                $mensaje = "Cuenta creada correctamente. Ya puedes iniciar sesión.";
                $tipo_mensaje = "exito";

            } else {

                $mensaje = "No se pudo crear la cuenta.";
                $tipo_mensaje = "error";

            }
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
        Crear cuenta - Tienda ITT3
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
            Crear cuenta
        </h2>


        <?php if ($mensaje !== ""): ?>

            <p class="mensaje-<?php echo $tipo_mensaje; ?>">

                <?php
                echo htmlspecialchars($mensaje);
                ?>

            </p>

        <?php endif; ?>



        <form
            action="registro.php"
            method="POST"
        >


            <label for="nombre">
                Nombre
            </label>

            <input
                type="text"
                id="nombre"
                name="nombre"
                required
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
                minlength="6"
                required
            >



            <label for="confirmar">
                Confirmar contraseña
            </label>

            <input
                type="password"
                id="confirmar"
                name="confirmar"
                minlength="6"
                required
            >



            <button
                type="submit"
                class="btn-carrito"
            >
                Crear cuenta
            </button>


        </form>



        <p>

            ¿Ya tienes una cuenta?

            <a href="login.php">
                Iniciar sesión
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