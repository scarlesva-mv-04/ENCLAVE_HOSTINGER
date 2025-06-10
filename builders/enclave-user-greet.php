<?php
$headers[] = "Authorization: Bearer " . $_SESSION["token"];
$url = DIR_SERV . "/cliente/" . $_SESSION["user_id"];

$respuesta = consumir_servicios_JWT_REST($url, "GET", $headers);
$json_respuesta = json_decode($respuesta, true);

if (!$json_respuesta) {
    session_destroy();
    die(error_page("Gestión Libros", "<h1>ENCLAVE</h1><p>Error consumiendo el servicio REST: <strong>$url</strong></p>"));
}

if (isset($json_respuesta["error"])) {
    session_destroy();
    die(error_page("Gestión Libros", "<h1>ENCLAVE</h1><p>" . $json_respuesta["error"] . "</p>"));
}

if (isset($json_respuesta["no_auth"])) {
    session_unset();
    $_SESSION["mensaje_seguridad"] = "El tiempo de sesión de la API ha expirado o no tiene permisos";
    header("Location:index.php");
    exit;
}

if (isset($json_respuesta["mensaje_baneo"])) {
    session_unset();
    $_SESSION["mensaje_seguridad"] = "Usted ya no se encuentra registrado en la BD";
    header("Location:index.php");
    exit;
}
?>

<?php
$cliente = $json_respuesta["cliente"];
$_SESSION["is_logged_in"]=true;
?>

<div class="user-info">
    <div class="rounded-img">
        <picture>
            <img src="images/photos/<?php echo htmlspecialchars($cliente['profile-pic']); ?>" alt="profile-pic">
        </picture>
    </div>
    <p class="subtitles">
        <?php
        echo (
            $cliente["genero"] === "masculino" ? "Bienvenido" : ($cliente["genero"] === "femenino" ? "Bienvenida" : "Hola de nuevo")
        ) . " " . htmlspecialchars(ucfirst($cliente["nombre"])) . ".";
        ?>
    </p>
    <p class="text resaltar">Es un placer.</p>
    <script>
        redirigirAutomaticamente("/properties", 1500)
    </script>
</div>