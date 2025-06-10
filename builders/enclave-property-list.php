<?php

if (!isset($_SESSION["token"])) {
    header("Location:/");
    exit;
}

$headers[] = "Authorization: Bearer " . $_SESSION["token"];
$url = DIR_SERV . "/properties/" . $_SESSION["user_id"];

$respuesta = consumir_servicios_JWT_REST($url, "GET", $headers);
$json_respuesta = json_decode($respuesta, true);

if (!is_array($json_respuesta)) {
    session_destroy();
    header("Location:/");
    exit;
}

if (isset($json_respuesta["error"])) {
    session_destroy();
    header("Location:/");
    exit;
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

if (!isset($json_respuesta["propiedades"]) || count($json_respuesta["propiedades"]) <= 0) {
    session_destroy();
    header("Location:/");
    exit;
}
?>

<?php
foreach ($json_respuesta["propiedades"] as $propiedad) {
    $nombre = htmlspecialchars($propiedad["nombre_propiedad"]);
    $zona = htmlspecialchars($propiedad["area_name"]);
    $img_portada = htmlspecialchars($propiedad["img_portada"]);
    $horus_estado = htmlspecialchars($propiedad["horus_estado"]);
    $enclave_estado = htmlspecialchars($propiedad["enclave_estado"]);
    $horus_icono = "../images/estados/{$horus_estado}.svg";
    $enclave_icono = "../images/estados/{$enclave_estado}.svg";
?>
    <div class="tarjeta-propiedad" onclick="window.location.href = '/property?id=<?= $propiedad["id"] ?>'">
        <div class="imagen-property lazy-bg" data-bg="../images/photos/<?= $img_portada ?>">
            <div>
                <div class="iconos">
                    <span class="icono horus">
                        <img src="<?= $horus_icono ?>" alt="Estado Horus: <?= $horus_estado ?>" width="20" height="16" />
                    </span>
                    <span class="icono enclave">
                        <img src="<?= $enclave_icono ?>" alt="Estado Enclave: <?= $enclave_estado ?>" width="22" height="22" />
                    </span>
                </div>
                <div class="info-propiedad">
                    <p class="encabezado resaltar"><?= $nombre ?> - <?= $zona ?></p>
                </div>
            </div>
        </div>
    </div>
<?php
}
?>
