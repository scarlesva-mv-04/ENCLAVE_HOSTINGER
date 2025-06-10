<?php
ob_start();
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => false, // true si usas HTTPS
    'httponly' => true,
    'samesite' => 'Lax' // o 'None' si usas HTTPS y quieres compartir cookies cross-site
]);
session_name("Enclave");
session_start();
require_once __DIR__ . '/src/functions/init.php';

if(isset($_POST["btnSalir"]))
{
    session_destroy();
    header("Location:index.php");
    exit;
}

if(isset($_SESSION["token"]))
{
    require __DIR__ . "/src/functions/security.php";
}


// Extraer ruta limpia (para URLs amigables)
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$route = trim(str_replace($basePath, '', $requestUri), '/');
$route = preg_replace('/\.php$/', '', $route);

// Vista por defecto
$vista = $route ?: 'frontpage';

// Plantillas y vista
get_header();
load_view($vista);
get_footer();
ob_end_flush();
?>
