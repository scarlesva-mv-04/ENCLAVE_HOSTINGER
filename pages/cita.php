<?php
$errores = [];
$error_cita_duplicada = false;

// Función para validar DNI o NIE español
function validar_documento_identidad($valor)
{
    $valor = strtoupper(trim($valor));
    $letras = 'TRWAGMYFPDXBNJZSQVHLCKE';

    // Validar DNI (8 números + letra)
    if (preg_match('/^\d{8}[A-Z]$/', $valor)) {
        $numero = substr($valor, 0, 8);
        $letra = substr($valor, 8, 1);
        return $letras[$numero % 23] === $letra;
    }

    // Validar NIE (X/Y/Z + 7 números + letra)
    if (preg_match('/^[XYZ]\d{7}[A-Z]$/', $valor)) {
        $prefijo = substr($valor, 0, 1);
        $numero = substr($valor, 1, 7);
        $letra = substr($valor, 8, 1);
        $prefijo_num = ['X' => '0', 'Y' => '1', 'Z' => '2'][$prefijo];
        $num_total = $prefijo_num . $numero;
        return $letras[$num_total % 23] === $letra;
    }

    return false;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["name"] ?? "");
    $apellidos = trim($_POST["surname"] ?? "");
    $dni = strtoupper(trim($_POST["dni"] ?? ""));
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $fecha = trim($_POST["appointment_date"] ?? "");
    $hora = trim($_POST["appointment_time"] ?? "");

    // Validaciones
    if ($nombre === "" || !preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/', $nombre)) {
        $errores["name"] = "Nombre inválido";
    }

    if ($apellidos === "" || !preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/', $apellidos)) {
        $errores["surname"] = "Apellidos inválidos";
    }

    if ($dni === "" || !validar_documento_identidad($dni)) {
        $errores["dni"] = "DNI o NIE inválido";
    }

    if (!preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $email)) {
        $errores["email"] = "Email inválido";
    }

    if (!preg_match('/^\d{9}$/', $phone)) {
        $errores["phone"] = "Teléfono inválido";
    }

    if ($fecha === "") {
        $errores["appointment_date"] = "Seleccione una fecha";
    }

    if ($hora === "") {
        $errores["appointment_time"] = "Seleccione una hora";
    }

    if (empty($errores)) {
        $url = DIR_SERV . "/citas";
        $datos_env = [
            "name" => $nombre,
            "surname" => $apellidos,
            "dni" => $dni,
            "email" => $email,
            "phone" => $phone,
            "appointment_date" => $fecha,
            "appointment_time" => $hora
        ];

        $respuesta = consumir_servicios_REST($url, "POST", $datos_env);
        $json_respuesta = json_decode($respuesta, true);

        if (!$json_respuesta) {
            session_destroy();
            die(error_page("Citas", "<h1>ENCLAVE</h1><p>Error consumiendo el servicio REST: <strong>$url</strong></p>"));
        }

        if (isset($json_respuesta["error"])) {
            if (
                str_contains($json_respuesta["error"], "Duplicate entry") &&
                str_contains($json_respuesta["error"], "unique_dni_per_day")
            ) {
                $error_cita_duplicada = true;
            } else {
                die(error_page("Citas", "<h1>ENCLAVE</h1><p>{$json_respuesta["error"]}</p>"));
            }
        }

        if (isset($json_respuesta["success"])) {
            header("Location:/cita_successful");
            exit;
        }
    }
}
?>


<main class="cita">
    <div class="cover cover-cita">
        <div class="cover-content">
            <h1 class="subtitles">Agencia una cita y prepárate para <span class="resaltar">una nueva forma de vivir</span></h1>
            <p class="text">Explorar la vida en la Costa del Sol es un sueño. En Enclave, lo hacemos aún más excepcional, integrando sistemas domóticos de lujo que redefinen el confort, la seguridad y la elegancia de tu hogar. Nuestro equipo está aquí para acompañarte en cada paso, ofreciéndote asesoramiento experto y soluciones a medida para transformar tu propiedad en una experiencia de vida inteligente.</p>
            <p class="text"><strong>Completa el formulario y déjanos mostrarte cómo es vivir en otro nivel.</strong></p>
        </div>
    </div>

    <section id="cita">
        <h2 class="subtitles cita-h2-title">Introduce tus datos</h2>
        <form id="cita" method="post">
            <div class="cita-name-surname">
                <div class="cita-input">
                    <span class="txt-input">Nombre</span>
                    <input class="txt-input <?= isset($errores['name']) ? 'error' : '' ?>" type="text" name="name"
                        placeholder="<?= isset($errores['name']) ? $errores['name'] : 'Introduzca su nombre' ?>"
                        data-placeholder="Introduzca su nombre"
                        value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                </div>
                <div class="cita-input">
                    <span class="txt-input">Apellidos</span>
                    <input class="txt-input <?= isset($errores['surname']) ? 'error' : '' ?>" type="text" name="surname"
                        placeholder="<?= isset($errores['surname']) ? $errores['surname'] : 'Introduzca sus apellidos' ?>"
                        data-placeholder="Introduzca sus apellidos"
                        value="<?= htmlspecialchars($_POST['surname'] ?? '') ?>">
                </div>
            </div>

            <div class="cita-dni">
                <span class="txt-input">DNI/NIE</span>
                <input class="txt-input <?= isset($errores['dni']) ? 'error' : '' ?>" type="text" name="dni"
                    placeholder="<?= isset($errores['dni']) ? $errores['dni'] : 'Introduzca su DNI/NIE' ?>"
                    data-placeholder="Introduzca su DNI/NIE"
                    value="<?= htmlspecialchars($_POST['dni'] ?? '') ?>">
            </div>

            <div class="cita-email">
                <span class="txt-input">Email</span>
                <input class="txt-input <?= isset($errores['email']) ? 'error' : '' ?>" type="text" name="email"
                    placeholder="<?= isset($errores['email']) ? $errores['email'] : 'Email' ?>"
                    data-placeholder="Email"
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <div class="cita-telefono">
                <span class="txt-input">Teléfono</span>
                <input class="txt-input <?= isset($errores['phone']) ? 'error' : '' ?>" type="text" name="phone"
                    placeholder="<?= isset($errores['phone']) ? $errores['phone'] : 'Teléfono' ?>"
                    data-placeholder="Teléfono"
                    value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
            </div>

            <div class="cita-fecha">
                <div class="cita-input-fecha">
                    <h3 class="resaltar txt-botones">Elija una fecha para su cita</h3>
                    <p class="input-txt">
                        <input class="txt-input <?= isset($errores['appointment_date']) ? 'error' : '' ?>"
                            id="datepicker" name="appointment_date"
                            placeholder="<?= isset($errores['appointment_date']) ? $errores['appointment_date'] : 'Selecciona una fecha' ?>"
                            data-placeholder="Selecciona una fecha"
                            value="<?= htmlspecialchars($_POST['appointment_date'] ?? '') ?>">
                    </p>
                </div>

                <div class="cita-input-horas">
                    <h3 class="resaltar txt-botones">Elija una hora</h3>
                    <div class="input-txt" id="hourpicker-container">
                        <input type="hidden" name="appointment_time" id="appointment_time"
                            class="<?= isset($errores['appointment_time']) ? 'error' : '' ?>"
                            value="<?= htmlspecialchars($_POST['appointment_time'] ?? '') ?>">
                        <div id="hours_avaliable" class="hours-avaliable"></div>
                    </div>
                </div>
            </div>

            <?php if ($error_cita_duplicada): ?>
                <p class="error-msg">Ya existe una cita con este DNI para esa fecha.</p>
            <?php endif; ?>

            <div>
                <button class="normal txt-botones" type="submit">Agendar cita</button>
            </div>
        </form>



    </section>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const dateInput = document.querySelector("#datepicker");
            const hours_avaliable = document.getElementById("hours_avaliable");
            const hiddenHourInput = document.getElementById("appointment_time");
            const hourPickerContainer = document.querySelector(".cita-input-horas");
            console.log(hourPickerContainer);

            hourPickerContainer.classList.remove("avaliable"); // Oculta inicialmente

            fetch(`${API_BASE}/fechas_ocupadas`)
                .then(response => response.json())
                .then(disabledDatesFromAPI => {
                    flatpickr(dateInput, {
                        dateFormat: "Y-m-d",
                        minDate: "today",
                        disable: [
                            ...disabledDatesFromAPI,
                            function(date) {
                                return date.getDay() === 0 || date.getDay() === 6;
                            }
                        ],
                        locale: {
                            firstDayOfWeek: 1
                        },
                        onChange: function(selectedDates, dateStr) {
                            if (!dateStr) return;

                            hourPickerContainer.classList.remove("avaliable");
                            hiddenHourInput.value = ""; // Limpia valor previo
                            hours_avaliable.innerHTML = ""; // Limpia horas anteriores

                            fetch(`${API_BASE}/fechas_disponibles/${dateStr}`)
                                .then(response => response.json())
                                .then(data => {
                                    if (data.length === 0) {
                                        hours_avaliable.innerHTML = "<p class='txt-input'>Sin horas disponibles</p>";
                                    } else {
                                        data.forEach(hora => {
                                            const div = document.createElement("div");
                                            div.textContent = hora;
                                            div.classList.add("hour");
                                            div.addEventListener("click", function() {
                                                hours_avaliable.querySelectorAll("div").forEach(d => d.classList.remove("selected"));
                                                div.classList.add("selected");
                                                hiddenHourInput.value = hora;
                                            });
                                            hours_avaliable.appendChild(div);
                                        });
                                    }

                                    hourPickerContainer.classList.add("avaliable");
                                })
                                .catch(err => {
                                    console.error("Error cargando horas:", err);
                                });
                        }
                    });
                })
                .catch(err => {
                    console.error("Error cargando días ocupados:", err);
                });
        });
    </script>
</main>

<!-- Funciona regulero, hay que testear y arreglar-->