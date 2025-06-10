<?php

require __DIR__ . '/Slim/autoload.php';

require "src/funciones_CTES.php";

$app = new \Slim\App;

$app->get('/logueado', function () {

    $test = validateToken();
    if (is_array($test)) {
        echo json_encode($test);
    } else
        echo json_encode(array("no_auth" => "No tienes permiso para usar el servicio"));
});


$app->post('/login', function ($request) {

    $datos_login[] = $request->getParam("user");
    $datos_login[] = $request->getParam("clave");


    echo json_encode(login($datos_login));
});

/*OBTENCIÓN DATOS CLIENTE ESPECIFICO*/

$app->get('/cliente/{id}', function ($request, $response, $args) {
    $test = validateToken();

    if (!is_array($test)) {
        echo json_encode(["no_auth" => "No tienes permiso para usar el servicio"]);
        return;
    }

    if (!isset($test["cliente"])) {
        echo json_encode($test); // puede ser un error o mensaje_baneo
        return;
    }

    $id_cliente = $args['id'];

    if (!isAuthorized($id_cliente, $test)) {
        echo json_encode(["no_auth" => "No tienes permiso para acceder a este recurso"]);
        return;
    }

    echo json_encode(obtener_cliente_por_id($id_cliente));
});


$app->put('/cliente/{id}/{tupla}/{valor}', function ($request, $response, $args) {
    $test = validateToken();

    if (!is_array($test)) {
        echo json_encode(["no_auth" => "No tienes permiso para usar el servicio"]);
        return;
    }

    if (!isset($test["cliente"])) {
        echo json_encode($test); // puede ser un error o mensaje_baneo
        return;
    }

    $id_cliente = $args['id'];

    if (!isAuthorized($id_cliente, $test)) {
        echo json_encode(["no_auth" => "No tienes permiso para acceder a este recurso"]);
        return;
    }

    echo json_encode( actualizar_dato_cliente($id_cliente, $args['tupla'], $args['valor']));
});

/*------------------------------------------------------------------------------------------------------------------------------------------------------*/


/*OBTENCIÓN DATOS propiedades cliente ESPECIFICO*/

$app->get('/properties/{id}', function ($request, $response, $args) {
    $test = validateToken();

    if (!is_array($test)) {
        echo json_encode(["no_auth" => "No tienes permiso para usar el servicio"]);
        return;
    }

    if (!isset($test["cliente"])) {
        echo json_encode($test); // puede ser un error o mensaje_baneo
        return;
    }

    $id_cliente = $args['id'];

    if (!isAuthorized($id_cliente, $test)) {
        echo json_encode(["no_auth" => "No tienes permiso para acceder a este recurso"]);
        return;
    }


    $resultado = obtener_propiedades_por_cliente($id_cliente);
    echo json_encode($resultado);
});


/*------------------------------------------------------------------------------------------------------------------------------------------------------*/

/*OBTENCIÓN DATOS propiedad cliente ESPECIFICO*/

$app->get('/property/{id}', function ($request, $response, $args) {
    $test = validateToken();

    if (!is_array($test)) {
        echo json_encode(["no_auth" => "No tienes permiso para usar el servicio"]);
        return;
    }

    if (!isset($test["cliente"])) {
        echo json_encode($test);
        return;
    }

    $id_propiedad = $args['id'];
    $id_cliente = $test["cliente"]["id"];

    if (!propiedadPerteneceACliente($id_propiedad, $id_cliente)) {
        echo json_encode(["no_auth" => "No tienes permiso para acceder a esta propiedad"]);
        return;
    }

    $resultado = obtener_propiedad_por_id($id_propiedad);
    echo json_encode($resultado);
});

/*------------------------------------------------------------------------------------------------------------------------------------------------------*/


/* CALENDARIO TEST */
$app->post('/citas', function ($request) {
    $datos_cita[] = $request->getParam("name");
    $datos_cita[] = $request->getParam("surname");
    $datos_cita[] = $request->getParam("dni");
    $datos_cita[] = $request->getParam("email");  // antes: contact
    $datos_cita[] = $request->getParam("phone");  // nuevo campo
    $datos_cita[] = $request->getParam("appointment_date");
    $datos_cita[] = $request->getParam("appointment_time");

    echo json_encode(crear_cita($datos_cita));
});


/* CALENDARIO fechas disponibles */

$app->get('/fechas_disponibles/{fecha}', function ($request, $response, $args) {
    echo json_encode(fechas_disponibles($args['fecha']));
});

/* CALENDARIO fechas ocupadas */

$app->get('/fechas_ocupadas', function ($request, $response) {
    echo json_encode(fechas_ocupadas());
});


/* ------------------------------------------------------------------------------------------------------------------------------------------------------- */

// endpoint: /property/{id}/modules/confort
$app->get('/property/{id}/modules/confort', function ($request, $response, $args) {
    $test = validateToken();

    if (!is_array($test)) {
        echo json_encode(["no_auth" => "No tienes permiso para usar el servicio"]);
        return;
    }

    if (!isset($test["cliente"])) {
        echo json_encode($test);
        return;
    }

    $id_propiedad = $args['id'];
    $id_cliente = $test["cliente"]["id"];

    if (!propiedadPerteneceACliente($id_propiedad, $id_cliente)) {
        echo json_encode(["no_auth" => "No tienes permiso para acceder a esta propiedad"]);
        return;
    }

    $resultado = obtener_modulos_confort_por_propiedad($id_propiedad);
    echo json_encode($resultado);
});

$app->get('/property/{id}/modules/seguridad', function ($request, $response, $args) {
    $test = validateToken();

    if (!is_array($test)) {
        echo json_encode(["no_auth" => "No tienes permiso para usar el servicio"]);
        return;
    }

    if (!isset($test["cliente"])) {
        echo json_encode($test);
        return;
    }

    $id_propiedad = $args['id'];
    $id_cliente = $test["cliente"]["id"];

    if (!propiedadPerteneceACliente($id_propiedad, $id_cliente)) {
        echo json_encode(["no_auth" => "No tienes permiso para acceder a esta propiedad"]);
        return;
    }

    $resultado = obtener_modulos_seguridad_por_propiedad($id_propiedad);
    echo json_encode($resultado);
});



/*MODULOS DINAMICOS*/

$app->get('/module/{id}', function ($request, $response, $args) {
    $test = validateToken();

    if (!is_array($test)) {
        echo json_encode(["no_auth" => "No tienes permiso para usar el servicio"]);
        return;
    }

    if (!isset($test["cliente"])) {
        echo json_encode($test);
        return;
    }

    $id_cliente = $test["cliente"]["id"];
    $id_modulo = $args['id'];

    if (!isAuthorizedModulo($id_modulo, $id_cliente)) {
        echo json_encode(["no_auth" => "No tienes permiso para acceder a este recurso"]);
        return;
    }

    $resultado = obtener_modulo_por_id($id_modulo);
    echo json_encode($resultado);
});




$app->run();
