<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require 'Firebase/autoload.php';

define("PASSWORD_API", "Una_clave_para_usar_para_encriptar");
define("MINUTOS_API", 60);
define("SERVIDOR_BD", "localhost");
define("USUARIO_BD", "jose");
define("CLAVE_BD", "josefa");
define("NOMBRE_BD", "enclave");


function validateToken()
{

    $headers = apache_request_headers();
    if (!isset($headers["Authorization"]))
        return false; //Sin autorizacion
    else {
        $authorization = $headers["Authorization"];
        $authorizationArray = explode(" ", $authorization);
        $token = $authorizationArray[1];
        try {
            $info = JWT::decode($token, new Key(PASSWORD_API, 'HS256'));
        } catch (\Throwable $th) {
            return false; //Expirado
        }

        try {
            $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        } catch (PDOException $e) {

            $respuesta["error"] = "Imposible conectar:" . $e->getMessage();
            return $respuesta;
        }

        try {
            $consulta = "select * from clientes where id=?";
            $sentencia = $conexion->prepare($consulta);
            $sentencia->execute([$info->data]);
        } catch (PDOException $e) {
            $respuesta["error"] = "Imposible realizar la consulta:" . $e->getMessage();
            $sentencia = null;
            $conexion = null;
            return $respuesta;
        }
        if ($sentencia->rowCount() > 0) {
            $respuesta["cliente"] = $sentencia->fetch(PDO::FETCH_ASSOC);

            $payload['exp'] = time() + (MINUTOS_API * 60);
            $payload['data'] = $respuesta["cliente"]["id"];
            $jwt = JWT::encode($payload, PASSWORD_API, 'HS256');
            $respuesta["token"] = $jwt;
        } else
            $respuesta["mensaje_baneo"] = "El usuario no se encuentra registrado en la BD";

        $sentencia = null;
        $conexion = null;
        return $respuesta;
    }
}

function isAuthorized($requestedId, $tokenData)
{
    return isset($tokenData["cliente"]) && $tokenData["cliente"]["id"] == $requestedId;
}

function propiedadPerteneceACliente($id_propiedad, $id_cliente)
{
    try {
        $conexion = new PDO(
            "mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD,
            USUARIO_BD,
            CLAVE_BD,
            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'")
        );
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $consulta = "SELECT COUNT(*) FROM propiedad_cliente WHERE id_propiedad = ? AND id_cliente = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$id_propiedad, $id_cliente]);

        return $sentencia->fetchColumn() > 0;
    } catch (PDOException $e) {
        return false;
    }
}



function login($datos_login)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No he podido conectarse a la base de batos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "select * from clientes where usuario=? and clave=?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute($datos_login);
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion = null;
        $respuesta["error"] = "No he podido realizarse la consulta: " . $e->getMessage();
        return $respuesta;
    }

    if ($sentencia->rowCount() > 0) {
        $respuesta["cliente"] = $sentencia->fetch(PDO::FETCH_ASSOC);
        $payload['exp'] = time() + (MINUTOS_API * 60);
        $payload['data'] = $respuesta["cliente"]["id"];
        $jwt = JWT::encode($payload, PASSWORD_API, 'HS256');
        $respuesta["token"] = $jwt;
    } else
        $respuesta["mensaje"] = "El usuario no se encuentra en la BD";

    $sentencia = null;
    $conexion = null;
    return $respuesta;
}




/*obtener cliente*/

function obtener_cliente_por_id($id)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No he podido conectarme a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "SELECT * FROM clientes WHERE id = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$id]);
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion = null;
        $respuesta["error"] = "No he podido realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    if ($sentencia->rowCount() > 0) {
        $respuesta["cliente"] = $sentencia->fetch(PDO::FETCH_ASSOC);
    } else {
        $respuesta["mensaje"] = "No se encontró ningún cliente con ese ID";
    }

    $sentencia = null;
    $conexion = null;
    return $respuesta;
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

/*obtener propiedades de un cliente*/

function obtener_propiedades_por_cliente($id_cliente)
{
    try {
        $conexion = new PDO(
            "mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD,
            USUARIO_BD,
            CLAVE_BD,
            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'")
        );
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        return ["error" => "No he podido conectarme a la base de datos: " . $e->getMessage()];
    }

    try {
        $consulta = "SELECT 
    p.*,
    a.area_name,
    se.estado AS enclave_estado,
    se.version_sistema AS enclave_version,
    se.fecha_instalacion AS enclave_fecha_instalacion,
    se.fecha_ultima_revision AS enclave_fecha_ultima_revision,
    sh.estado AS horus_estado,
    sh.version_sistema AS horus_version,
    sh.fecha_instalacion AS horus_fecha_instalacion,
    sh.fecha_ultima_revision AS horus_fecha_ultima_revision
FROM propiedades p
JOIN propiedad_cliente pc ON p.id = pc.id_propiedad
JOIN areas a ON p.area_id = a.area_id
LEFT JOIN sistemas_enclave se ON se.id_propiedad = p.id
LEFT JOIN sistemas_horus sh ON sh.id_propiedad = p.id
WHERE pc.id_cliente = ?
";

        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$id_cliente]);

        $resultados = $sentencia->fetchAll(PDO::FETCH_ASSOC);

        if (count($resultados) > 0) {
            return ["propiedades" => $resultados];
        } else {
            return ["mensaje" => "El cliente no tiene propiedades asociadas"];
        }
    } catch (PDOException $e) {
        return ["error" => "No he podido realizar la consulta: " . $e->getMessage()];
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

/*obtener propiedades de un cliente*/

function obtener_propiedad_por_id($id_propiedad)
{
    try {
        $conexion = new PDO(
            "mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD,
            USUARIO_BD,
            CLAVE_BD,
            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'")
        );
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        return ["error" => "No se pudo conectar a la base de datos: " . $e->getMessage()];
    }

    try {
        $consulta = "SELECT 
            p.id AS propiedad_id,
            p.nombre_propiedad,
            p.area_id,
            p.type,
            p.longitud,
            p.latitud,
            p.img_portada,
            a.area_name,
            se.estado AS enclave_estado,
            se.version_sistema AS enclave_version,
            se.fecha_instalacion AS enclave_fecha_instalacion,
            se.fecha_ultima_revision AS enclave_fecha_ultima_revision,
            sh.estado AS horus_estado,
            sh.version_sistema AS horus_version,
            sh.fecha_instalacion AS horus_fecha_instalacion,
            sh.fecha_ultima_revision AS horus_fecha_ultima_revision
        FROM propiedades p
        JOIN areas a ON p.area_id = a.area_id
        LEFT JOIN sistemas_enclave se ON se.id_propiedad = p.id
        LEFT JOIN sistemas_horus sh ON sh.id_propiedad = p.id
        WHERE p.id = ? ";

        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$id_propiedad]);

        $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            return ["propiedad" => $resultado];
        } else {
            return ["mensaje" => "No se encontró la propiedad indicada"];
        }
    } catch (PDOException $e) {
        return ["error" => "Error en la consulta: " . $e->getMessage()];
    }
}


/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

/*test calendario*/

function crear_cita($datos)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"
        ]);
    } catch (PDOException $e) {
        return ["error" => "No se pudo conectar a la base de datos: " . $e->getMessage()];
    }

    // Comprobar disponibilidad de la fecha y hora
    try {
        $consulta = "SELECT COUNT(*) FROM appointments WHERE appointment_date = ? AND appointment_time = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$datos[5], $datos[6]]);
        if ($sentencia->fetchColumn() > 0) {
            return ["error" => "Ya existe una cita para esa fecha y hora"];
        }
    } catch (PDOException $e) {
        return ["error" => "Error al comprobar la disponibilidad: " . $e->getMessage()];
    }

    // Insertar cita
    try {
        $consulta = "INSERT INTO appointments (name, surname, dni, email, phone, appointment_date, appointment_time)
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute($datos);
        return ["success" => "Cita registrada correctamente"];
    } catch (PDOException $e) {
        return ["error" => "Error al registrar la cita: " . $e->getMessage()];
    }
}


/* CALENDARIO DIAS DISPONIBLES */

function fechas_disponibles($fecha){

if (!$fecha) {
    echo json_encode([]);
    exit;
}

try {
    $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"
    ]);
} catch (PDOException $e) {
    echo json_encode([]);
    exit;
}

$todas = ["10:00", "11:00", "12:00", "13:00", "14:00", "15:00"];

$consulta = "SELECT TIME_FORMAT(appointment_time, '%H:%i') 
             FROM appointments 
             WHERE appointment_date = ?";

$sentencia = $conexion->prepare($consulta);
$sentencia->execute([$fecha]);
$ocupadas = $sentencia->fetchAll(PDO::FETCH_COLUMN);

$disponibles = array_values(array_diff($todas, $ocupadas));
return $disponibles;

}


function fechas_ocupadas() {
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"
        ]);
    } catch (PDOException $e) {
        return [];
    }

    $consulta = "SELECT appointment_date
                 FROM appointments
                 GROUP BY appointment_date
                 HAVING COUNT(*) >= 6";
    $sentencia = $conexion->prepare($consulta);
    $sentencia->execute();

    return $sentencia->fetchAll(PDO::FETCH_COLUMN);
}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

function obtener_modulos_confort_por_propiedad($id_propiedad)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        return ["error" => "No se pudo conectar a la base de datos: " . $e->getMessage()];
    }

    try {
        $consulta = "SELECT 
                        m.id AS id_modulo,
                        m.nombre_modulo,
                        cm.nombre_categoria
                    FROM modulos m
                    JOIN categoria_modulos cm ON cm.id = m.id_categoria
                    JOIN sistemas_enclave se ON se.id = m.id_sist_enclave
                    WHERE m.tipo = 'confort' AND se.id_propiedad = ?
                    ORDER BY cm.nombre_categoria, m.nombre_modulo";

        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$id_propiedad]);
        $datos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

        $modulos = ["confort" => []];
        foreach ($datos as $mod) {
            $cat = $mod["nombre_categoria"];
            if (!isset($modulos["confort"][$cat])) {
                $modulos["confort"][$cat] = [];
            }
            $modulos["confort"][$cat][] = $mod;
        }

        return $modulos;
    } catch (PDOException $e) {
        return ["error" => "Error en la consulta: " . $e->getMessage()];
    }
}

function obtener_modulos_seguridad_por_propiedad($id_propiedad)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        return ["error" => "No se pudo conectar a la base de datos: " . $e->getMessage()];
    }

    try {
        $consulta = "SELECT 
                        m.id AS id_modulo,
                        m.nombre_modulo,
                        cm.nombre_categoria
                    FROM modulos m
                    JOIN categoria_modulos cm ON cm.id = m.id_categoria
                    JOIN sistemas_enclave se ON se.id = m.id_sist_enclave
                    WHERE m.tipo = 'seguridad' AND se.id_propiedad = ?
                    ORDER BY cm.nombre_categoria, m.nombre_modulo";

        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$id_propiedad]);
        $datos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

        $modulos = ["seguridad" => []];
        foreach ($datos as $mod) {
            $cat = $mod["nombre_categoria"];
            if (!isset($modulos["seguridad"][$cat])) {
                $modulos["seguridad"][$cat] = [];
            }
            $modulos["seguridad"][$cat][] = $mod;
        }

        return $modulos;
    } catch (PDOException $e) {
        return ["error" => "Error en la consulta: " . $e->getMessage()];
    }
}

function agregar_usuario_propiedad($id_propiedad, $data)
{
    try {
        $conexion = new PDO(
            "mysql:host=".SERVIDOR_BD.";dbname=".NOMBRE_BD,
            USUARIO_BD,
            CLAVE_BD,
            [PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES 'utf8'"]
        );
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        return ["error"=>"No se pudo conectar: ".$e->getMessage()];
    }

    try {
        $conexion->beginTransaction();

        $hash = password_hash($data["clave"], PASSWORD_BCRYPT);

        $sql = "INSERT INTO clientes
            (nombre,apellidos,dni,genero,usuario,clave,`tipo`)
            VALUES (?,?,?,?,?,?,?)";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            $data["nombre"],
            $data["apellidos"],
            $data["dni"],
            $data["genero"],
            $data["usuario"],
            $hash,
            $data["tipo"]
        ]);
        $id_nuevo = $conexion->lastInsertId();

        $sql2 = "INSERT INTO propiedad_cliente (id_cliente,id_propiedad) VALUES (?,?)";
        $stmt2 = $conexion->prepare($sql2);
        $stmt2->execute([$id_nuevo, $id_propiedad]);

        $conexion->commit();

        return ["success"=>true,"message"=>"Usuario agregado","id_usuario"=>(int)$id_nuevo];
    } catch (PDOException $e) {
        $conexion->rollBack();
        return ["error"=>"Error en inserción: ".$e->getMessage()];
    }
}
