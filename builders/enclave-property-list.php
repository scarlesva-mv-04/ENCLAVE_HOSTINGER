<?php

if(!isset($_SESSION["token"])){
    header("Location:/");
}

$headers[] = "Authorization: Bearer " . $_SESSION["token"];
$url = DIR_SERV . "/properties/" . $_SESSION["user_id"];

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
foreach ($json_respuesta["propiedades"] as $propiedad) {
    $nombre = htmlspecialchars($propiedad["nombre_propiedad"]);
    $zona = htmlspecialchars($propiedad["area_name"]);
    $img_portada = htmlspecialchars($propiedad["img_portada"]);

    // Estados dinámicos
    $horus_estado = htmlspecialchars($propiedad["horus_estado"]);
    $enclave_estado = htmlspecialchars($propiedad["enclave_estado"]);

    // Rutas de iconos dinámicas
    $horus_icono = "../images/icons/horus-{$horus_estado}.svg";
    $enclave_icono = "../images/icons/enclave-{$enclave_estado}.svg";
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
