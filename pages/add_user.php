<?php
if (!isset($_SESSION["token"])) {
    header("Location:/");
}

var_dump($_SESSION);

if (!isset($_SESSION["cliente"]) || $_SESSION["cliente"]["tipo"] !== "administrador") {
    die("<p>No tienes permiso.</p>");
}
$id_propiedad = $_SESSION["propiedad_id"] ?? null;
if (!$id_propiedad) {
    die("<p>ID de propiedad faltante.</p>");
}

$id_propiedad = $_POST["id_propiedad"];
$datos = [
    "nombre"    => $_POST["nombre"],
    "apellidos" => $_POST["apellidos"],
    "dni"       => $_POST["dni"],
    "genero"    => $_POST["genero"],
    "usuario"   => $_POST["usuario"],
    "clave"     => $_POST["clave"],
    "tipo"      => $_POST["tipo"]
];

$headers[] = "Content-Type: application/json";
$headers[] = "Authorization: Bearer ".$_SESSION["token"];
$url = DIR_SERV."/property/".$id_propiedad."/user";

$respuesta = consumir_servicios_JWT_REST($url, "POST", $headers, json_encode($datos));
$json = json_decode($respuesta, true);

if (!$json) {
    session_destroy();
    die(error_page("ENCLAVE", "<h1>ENCLAVE</h1><p>Error consumiendo el servicio: <strong>$url</strong></p>"));
}

if (isset($json["error"])) {
    die("<p>".$json["error"]."</p>");
}

if (isset($json["no_auth"])) {
    session_unset();
    $_SESSION["mensaje_seguridad"] = $json["no_auth"];
    header("Location:index.php");
    exit;
}

if (isset($json["success"])) {
    echo "<p>".$json["message"]."</p>";
}


?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Agregar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-4">
    <div class="container">
        <h1>Agregar usuario a propiedad #<?= htmlspecialchars($id_propiedad) ?></h1>
        <form method="post" action="agregar_usuario.php">
            <input type="hidden" name="id_propiedad" value="<?= htmlspecialchars($id_propiedad) ?>">
            <div class="mb-3">
                <label>Nombre</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Apellidos</label>
                <input type="text" name="apellidos" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>DNI</label>
                <input type="text" name="dni" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Género</label>
                <select name="genero" class="form-select" required>
                    <option value="masculino">Masculino</option>
                    <option value="femenino">Femenino</option>
                    <option value="neutro">Neutro</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Usuario</label>
                <input type="text" name="usuario" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Clave</label>
                <input type="password" name="clave" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Tipo</label>
                <select name="tipo" class="form-select" required>
                    <option value="autorizado">Autorizado</option>
                    <option value="temporal">Temporal</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Agregar</button>
        </form>
    </div>
</body>

</html>