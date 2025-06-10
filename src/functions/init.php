<?php
/*SEGURIDAD*/

if ($_SERVER["HTTP_HOST"] === "enclave.com") {
    define("DIR_SERV", "http://www.enclave.com/ENCLAVE_API");
} else {
    define("DIR_SERV", "http://www.enclave.com/ENCLAVE_API");
}

/**/
define("MINUTOS",15);

function consumir_servicios_REST($url,$metodo,$datos=null)
{
    $llamada=curl_init();
    curl_setopt($llamada,CURLOPT_URL,$url);
    curl_setopt($llamada,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($llamada,CURLOPT_CUSTOMREQUEST,$metodo);
    if(isset($datos))
        curl_setopt($llamada,CURLOPT_POSTFIELDS,http_build_query($datos));
    $respuesta=curl_exec($llamada);
    curl_close($llamada);
    return $respuesta;
}


function consumir_servicios_JWT_REST($url,$metodo,$headers,$datos=null)
{
    $llamada=curl_init();
    curl_setopt($llamada,CURLOPT_URL,$url);
    curl_setopt($llamada,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($llamada,CURLOPT_CUSTOMREQUEST,$metodo);
    curl_setopt($llamada,CURLOPT_HTTPHEADER,$headers);
    if(isset($datos))
        curl_setopt($llamada,CURLOPT_POSTFIELDS,http_build_query($datos));
    $respuesta=curl_exec($llamada);
    curl_close($llamada);
    return $respuesta;
}
function error_page($title, $body)
{
   return '<!DOCTYPE html>
   <html lang="es">
   <head>
       <meta charset="UTF-8">
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <title>'.$title.'</title>
   </head>
   <body>'.$body.'</body>
   </html>';
}

function consumir_servicios_JWT_REST_CON_ARCHIVO($url, $metodo, $headers, $datos, $campo_archivo, $ruta_tmp_archivo, $nombre_original)
{
    $llamada = curl_init();
    curl_setopt($llamada, CURLOPT_URL, $url);
    curl_setopt($llamada, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($llamada, CURLOPT_CUSTOMREQUEST, $metodo);

    $archivo = new CURLFile($ruta_tmp_archivo, mime_content_type($ruta_tmp_archivo), $nombre_original);
    $datos[$campo_archivo] = $archivo;

    $headers[] = "Content-Type: multipart/form-data";
    curl_setopt($llamada, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($llamada, CURLOPT_POSTFIELDS, $datos);

    $respuesta = curl_exec($llamada);
    curl_close($llamada);
    return $respuesta;
}



// Definimos rutas absolutas
define('PATTERNS_PATH', __DIR__ . '/../../patterns/');
define('PAGES_PATH', __DIR__ . '/../../pages/');
/**
 * Incluye el archivo de cabecera (header.php).
 */
function get_header(): void
{
    $file = PATTERNS_PATH . 'header.php';
    if (file_exists($file)) {
        include $file;
    } else {
        echo "<!-- Header no encontrado -->";
    }
}

/**
 * Incluye el archivo de pie de página (footer.php).
 */
function get_footer(): void
{
    $file = PATTERNS_PATH . 'footer.php';
    if (file_exists($file)) {
        include $file;
    } else {
        echo "<!-- Footer no encontrado -->";
    }
}

/**
 * Carga una vista válida desde /pages/, con control de seguridad.
 *
 * @param string $view El nombre de la vista (sin extensión .php)
 */
function load_view(string $view): void
{
    // Limpieza de nombre de vista (evita inyecciones de ruta)
    $view = strtolower(preg_replace('/[^a-z0-9_-]/', '', $view));
    if ($view === 'index') {
        $view = 'frontpage';
    }

    // Lista blanca de vistas permitidas
    $carpeta = 'pages/';
    $archivos = array_diff(scandir($carpeta), ['.', '..']);

    $allowedViews = []; //declaro el arrary vacío

    foreach ($archivos as $archivo) {
        if (is_file($carpeta . $archivo) && pathinfo($archivo, PATHINFO_EXTENSION) === 'php') {
            array_push($allowedViews,pathinfo($archivo, PATHINFO_FILENAME));
        }
    }

    if (in_array($view, $allowedViews)) {
        $file = PAGES_PATH . $view . '.php';
        if (file_exists($file)) {
            include $file;
        } else {
            echo "<!-- Vista '$view' no encontrada -->";
        }
    } else {
        // Cargar vista 404 personalizada si existe
        $fallback = PAGES_PATH . '404.php';
        if (file_exists($fallback)) {
            include $fallback;
        } else {
            echo "<h2>Error 404</h2><p>La vista '$view' no está permitida.</p>";
        }
    }
}
