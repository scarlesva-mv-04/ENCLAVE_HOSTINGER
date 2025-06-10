<?php 

function estado_horus_label($estado)
{
    $mensajes = [
        "operativo" => "Las cámaras están activadas.",
        "deshabilitado" => "Las cámaras están desactivadas.",
        "crítico" => "Sistema en estado crítico. Se requiere intervención inmediata.",
        "mantenimiento" => "El sistema Horus está en mantenimiento.",
        "alimentado_externamente" => "Sistema Horus funcionando con alimentación externa.",
        "persona_no_identificada_detectada" => "Persona no identificada detectada en las cámaras.",
        "intruso_detectado" => "Intruso detectado en la propiedad.",
        "robo_en_curso" => "¡Alerta! Posible robo en curso.",
        "entrada_comprometida" => "La entrada principal ha sido comprometida.",
        "modulo_roto" => "Un módulo del sistema Horus está roto.",
        "modulo_defectuoso" => "Módulo defectuoso detectado en Horus.",
    ];

    return $mensajes[$estado] ?? "Estado desconocido.";
}

function estado_enclave_label($estado)
{
    $mensajes = [
        "operativo" => "Su hogar se encuentra protegido.",
        "deshabilitado" => "Sistema de protección desactivado.",
        "crítico" => "Enclave en estado crítico. Se requiere intervención inmediata.",
        "mantenimiento" => "Sistema Enclave en mantenimiento.",
        "alimentado_externamente" => "Sistema Enclave funcionando con alimentación externa.",
        "incencio_detectado" => "¡Alerta de incendio detectada!",
        "falla_electrica_detectada" => "Falla eléctrica detectada.",
        "inundacion_detectada" => "¡Riesgo de inundación detectado!",
        "seismo_detectado" => "Movimiento sísmico detectado.",
        "modulo_roto" => "Un módulo del sistema Enclave está roto.",
        "modulo_defectuoso" => "Módulo defectuoso detectado en Enclave.",
    ];

    return $mensajes[$estado] ?? "Estado desconocido.";
}



?>