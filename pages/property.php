<?php

if (!isset($_SESSION["token"])) {
    header("Location:/");
}


require_once(__DIR__ . "/../src/functions/property_helpers.php");


if (!isset($_GET["id"])) {
    die(error_page("ENCLAVE", "<h1>ENCLAVE</h1><p>No se especificó ninguna propiedad.</p>"));
}

$id_propiedad = $_GET["id"];
$headers[] = "Authorization: Bearer " . $_SESSION["token"];
$url = DIR_SERV . "/property/" . $id_propiedad;

$respuesta = consumir_servicios_JWT_REST($url, "GET", $headers);
$json_respuesta = json_decode($respuesta, true);

if (!$json_respuesta) {
    session_destroy();
    die(error_page("ENCLAVE", "<h1>ENCLAVE</h1><p>Error consumiendo el servicio REST: <strong>$url</strong></p>"));
}

if (isset($json_respuesta["error"])) {
    session_destroy();
    die(error_page("ENCLAVE", "<h1>ENCLAVE</h1><p>" . $json_respuesta["error"] . "</p>"));
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

$propiedad = $json_respuesta["propiedad"];
$_SESSION["propiedad_id"] = $propiedad["id"];

$estado_horus = $propiedad["horus_estado"];
$estado_enclave = $propiedad["enclave_estado"];
?>
<main class="property">
    <div class="cover-property">
        <div class="property-image">
            <img src="images/properties/<?= $propiedad["img_portada"] ?>" alt="<?= $propiedad["img_portada"] ?>">
        </div>
        <div class="content">
            <p class="subtitles">
                <?= htmlspecialchars($propiedad["nombre_propiedad"]) ?> - <?= htmlspecialchars($propiedad["area_name"]) ?>
            </p>
        </div>
    </div>

    <section id="status">
        <h2 class="subtitles">Estado</h2>
        <div>
            <div class="home-status">
                <div>
                    <span></span>
                    <span class="txt-botones">
                        <?= estado_horus_label($estado_horus) ?>
                    </span>
                </div>
                <span class="text">
                    Última revisión: <?= htmlspecialchars($propiedad["horus_fecha_ultima_revision"]) ?>
                </span>
            </div>

            <div class="camera-status">
                <div>
                    <span></span>
                    <span class="txt-botones">
                        <?= estado_enclave_label($estado_enclave) ?>
                    </span>
                </div>
                <span class="text">
                    Última revisión: <?= htmlspecialchars($propiedad["enclave_fecha_ultima_revision"]) ?>
                </span>
            </div>
        </div>
    </section>

<!-- ******************* CONTROL DEL HOGAR ******************* -->

<?php
function icono_segun_categoria($cat) {
    $mapa = [
        'iluminacion_rgb' => 'bulb',
        'climatizacion' => 'fan',
        'temperatura_agua' => 'thermometer'
    ];
    return $mapa[$cat] ?? 'default';
}

/*Carga Modulos confort*/
$headers[] = "Authorization: Bearer " . $_SESSION["token"];
$url = DIR_SERV . "/property/" . $id_propiedad . "/modules/confort";

$respuesta = consumir_servicios_JWT_REST($url, "GET", $headers);
$json_respuesta = json_decode($respuesta, true);

if (!$json_respuesta) {
    session_destroy();
    die(error_page("ENCLAVE", "<h1>ENCLAVE</h1><p>Error consumiendo el servicio REST.</p>"));
}

if (isset($json_respuesta["error"])) {
    session_destroy();
    die(error_page("ENCLAVE", "<h1>ENCLAVE</h1><p>" . $json_respuesta["error"] . "</p>"));
}

if (isset($json_respuesta["no_auth"])) {
    session_unset();
    $_SESSION["mensaje_seguridad"] = "El token ha expirado o no tienes permisos.";
    header("Location:index.php");
    exit;
}
$modulos_confort = $json_respuesta;

/*Carga modulos seguridad*/

$headers[] = "Authorization: Bearer " . $_SESSION["token"];
$url = DIR_SERV . "/property/" . $id_propiedad . "/modules/seguridad";

$respuesta = consumir_servicios_JWT_REST($url, "GET", $headers);
$json_respuesta = json_decode($respuesta, true);

if (!$json_respuesta) {
    session_destroy();
    die(error_page("ENCLAVE", "<h1>ENCLAVE</h1><p>Error consumiendo el servicio REST.</p>"));
}

if (isset($json_respuesta["error"])) {
    session_destroy();
    die(error_page("ENCLAVE", "<h1>ENCLAVE</h1><p>" . $json_respuesta["error"] . "</p>"));
}

if (isset($json_respuesta["no_auth"])) {
    session_unset();
    $_SESSION["mensaje_seguridad"] = "El token ha expirado o no tienes permisos.";
    header("Location:index.php");
    exit;
}
$modulos_seguridad = $json_respuesta;

?>
<section class="control-del-hogar">
<h2>Control del Hogar</h2>
<?php if (!empty($modulos_confort["confort"])): ?>
<div class="confort-modules">
    <h3 class="txt-botones resaltar">Confort</h3>

    <?php foreach ($modulos_confort["confort"] as $subcategoria => $lista_modulos): ?>
        <div class="module-category">
            <h4 class="text resaltar"><?= ucfirst(str_replace('_', ' ', $subcategoria)) ?></h4>
            <div class="modules-wrapper">
                <?php foreach ($lista_modulos as $mod): ?>
                    <div class="module">
                        <div class="module-icon">
                            <img src="images/icons/<?= icono_segun_categoria($subcategoria) ?>.svg" alt="<?= htmlspecialchars($mod["nombre_modulo"]) ?>">
                        </div>
                        <div class="module-text">
                            <p class="subtitles"><?= htmlspecialchars($mod["nombre_modulo"]) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (!empty($modulos_seguridad["seguridad"])): ?>
<div class="security-modules">
    <h3 class="txt-botones resaltar">Seguridad</h3>
    <?php foreach ($modulos_seguridad["seguridad"] as $subcategoria => $lista_modulos): ?>
        <div class="module-category">
            <h4 class="text resaltar"><?= ucfirst(str_replace('_', ' ', $subcategoria)) ?></h4>
            <div class="modules-wrapper">
                <?php foreach ($lista_modulos as $mod): ?>
                    <div class="module">
                        <div class="module-icon">
                            <img src="images/icons/<?= icono_segun_categoria($subcategoria) ?>.svg" alt="<?= htmlspecialchars($mod["nombre_modulo"]) ?>">
                        </div>
                        <div class="module-text">
                            <p class="subtitles"><?= htmlspecialchars($mod["nombre_modulo"]) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
</section>

<!-- ******************* FUNCIONES USUARIO ADMINISTRADOR ******************* -->

<section class="admin-functions">
    <h2>Configuración adicional del hogar</h2>

    <div class="aditional-option-wrapper" onclick="window.location.href = '/add_user'">
        <div class="option-icon">
            <img src="../images/icons/house_add.svg" alt="Añadir usuario">
        </div>
        <div class="option-text">
            <p class="txt-botones resaltar">Añadir usuario al hogar</p>
        </div>
    </div>
</section>

</main>