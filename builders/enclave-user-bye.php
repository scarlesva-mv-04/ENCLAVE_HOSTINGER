<?php

if(!isset($_SESSION["token"])){
    header("Location:/");
}

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
        echo ("Hasta luego ".$cliente["nombre"].".");
        ?>
    </p>
    <p class="text resaltar">
        <?php
        if($cliente["genero"]==="neutro"){
            echo "Volverá a la página de inicio.";
        }else{
        echo (
            $cliente["genero"] === "masculino" ? "Será redirigido" : "Será redirigida"
        ) . " a la página de inicio.";
        }
        ?>
    </p>
    <script>
        redirigirAutomaticamente("/", 1500)
    </script>
    <?php session_destroy(); ?>
</div>