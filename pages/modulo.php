<?
if (!isset($_GET["id"])) {
    header("Location: control_hogar.php");
    exit;
}

$id_modulo = $_GET["id"];
$headers = [
    "Authorization: Bearer " . $_SESSION["token"]
];

$url = DIR_SERV . "/module/" . $id_modulo;
$respuesta = consumir_servicios_JWT_REST($url, "GET", $headers);
$json_respuesta = json_decode($respuesta, true);
var_dump($json_respuesta);
exit;

// Manejo de errores
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
    $_SESSION["mensaje_seguridad"] = "Sesión expirada o sin permisos.";
    header("Location:index.php");
    exit;
}

$modulo = $json_respuesta["modulo"] ?? null;

if (!$modulo) {
    echo "<p>No se encontró información del módulo con ID $id_modulo</p>";
    exit;
}
?>

<h2>Módulo: <?= htmlspecialchars($modulo["nombre_modulo"] ?? "Desconocido") ?></h2>

<table border="1">
    <tr>
        <?php foreach (array_keys($modulo) as $col): ?>
            <th><?= htmlspecialchars($col) ?></th>
        <?php endforeach; ?>
    </tr>
    <tr>
        <?php foreach ($modulo as $dato): ?>
            <td><?= htmlspecialchars($dato) ?></td>
        <?php endforeach; ?>
    </tr>
</table>

<button onclick="history.back();">Volver</button>
