<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Referidos</title>
    <link rel="stylesheet" href="/styles/referidos.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,500,700&display=swap" rel="stylesheet">
</head>

<body>
    <?php
    date_default_timezone_set("America/Bogota");
    require 'db.php';
    session_start();

    //Colsultar la información del cliente
    $cliente = $conn->prepare('SELECT name, phone, nequi, nivel_1 FROM usuarios WHERE phone=:numero');
    $cliente->bindParam(':numero', $_SESSION['numero']);
    $cliente->execute();
    $resultsCliente = $cliente->fetch(PDO::FETCH_ASSOC);

    $nombreCliente = $resultsCliente['name'];
    $numeroCliente = $resultsCliente['phone'];
    $nequiCliente = $resultsCliente['nequi'];
    $numero_n_1Cliente = $resultsCliente['nivel_1'];

    //Finaliza la consulta de la información del cliente

    //Consultar el Nombre del Nivel 1
    $records = $conn->prepare('SELECT name FROM usuarios WHERE phone=:numero_n_1');
    $records->bindParam(':numero_n_1', $numero_n_1Cliente);
    $records->execute();
    $results = $records->fetch(PDO::FETCH_ASSOC);
    $nombre_n_1 = $results['name'];
    //Finaliza consultar el Nombre del Nivel 1 

    //Consultar cantidad de referidos ingresados
    $referidos = $conn->prepare('SELECT COUNT(*) AS cantidad FROM usuarios WHERE nivel_1=:numero');
    $referidos->bindParam(':numero', $_SESSION['numero']);
    $referidos->execute();
    $resultsreferidos = $referidos->fetch(PDO::FETCH_ASSOC);
    $numeroreferidos = $resultsreferidos['cantidad'];
    //Finaliza consultar cantidad de referidos ingresados

    $numero = $_SESSION['numero'];

    function codifica_urlget(string $cadena): string
    {
        $cadena = mb_convert_encoding($cadena, 'UTF-8', mb_detect_encoding($cadena)); // Codifico a UTF-8
        $control = "Esteman106Control";
        $cadena = $control . $cadena . $control;  // Agrego al inicio y al final la palabra de control
        $cadena = base64_encode($cadena);  // Codifico en Base64 
        return $cadena;
    }

    ?>
    <form action="" method="POST" id="form">

        <!-- Mostrar la información del usuario -->
        <div class="contenedor">

            <div class="header">
                <img src="images/avatar.png" class="avatar" alt="avatar">
                <div class="textos">
                    <label class="hola" for="">Hola</label><br>
                    <label class="nombre" for=""><?php echo $nombreCliente ?></label><br>
                    <label class="numero" for="">Cel. <?php echo $numeroCliente ?></label>
                </div>
            </div>

            <div class="informacion">
                <div class="dias">
                    <label class="tudia" for="">Es tu día</label><br>
                    <label class="dias-numero" for=""><?php echo $_SESSION['dias'] ?></label><br>
                </div>
                <div class="nequi">
                    <label class="nequi-titulo" for="">Nequi</label>
                    <p>&nbsp</p>
                    <label class="nequi-numero" for=""><?php echo $nequiCliente ?></label><br>
                    <a style="font-size: 13px; color: #B06AB3; " href='act_nequi.php?.<?php echo codifica_urlget('previa=referidos.php&numero=' . $numero) ?>'>Actualizar Nequi</a>
                </div>
            </div>

            <div class="referido">
                <img src="images/informacion.png" class="img-informacion" alt="">
                <label class="referi" for="">Referido por <?php echo $nombre_n_1 ?></label>
            </div>

            <div class="info-nequi">
                <img src="images/referidos.png" class="img-nequi" alt="">
                <div class="texto-message">
                    <?php

                    switch ($numeroreferidos) {
                        case '0':
                    ?>
                            <label class="message-texto" for="">Registra tus dos referidos</label>
                        <?php
                            break;
                        case '1':
                        ?>
                            <label class="message-texto" for="">Registra tus dos referidos</label>
                        <?php
                            break;
                        case '2':
                        ?>
                            <label class="message-texto" for="">Gracias por registrar tus <br>dos referidos</label>
                    <?php
                    }
                    ?>
                </div>

            </div>

            <?php

            switch ($numeroreferidos) {

                    //Si el cliente no tiene referidos
                case '0': ?>

                    <!-- Campos para el Referido 1 -->

                    <div class="grupo_r1">
                        <img src="images/1.png" class="img-1" alt="">
                        <div>
                            <input type="text" name="nombre_referido_1" placeholder="Nombre" required><br>
                            <input type="tel" name="celular_referido_1" placeholder="Celular" required><br>
                            <input type="tel" name="celular_referido_1_conf" placeholder="Confirmar Celular" required><br>
                            <button name="registrar_referido_1" onclick="return confirm('Confirmas registro de referido');">Registrar</button>
                        </div>
                    </div>

                    <?php

                    //Clic en registrar Referido 1
                    if (isset($_POST['registrar_referido_1'])) {

                        if (empty($_POST['nombre_referido_1']) || empty($_POST['celular_referido_1']) || empty($_POST['celular_referido_1_conf'])) {
                            echo '<script language="javascript">alert("Un campo se encuentra vacio");</script>';
                        } else {

                            $celular_referido_1_1 = $_POST['celular_referido_1'];
                            $celular_referido_1_2 = $_POST['celular_referido_1_conf'];

                            if ($celular_referido_1_1 === $celular_referido_1_2) {

                                require  'db.php';

                                //Colsultar si el # de celular ya existe
                                $validacion = $conn->prepare('SELECT COUNT(*) AS cantidad FROM usuarios WHERE phone=:numero_r1');
                                $validacion->bindParam(':numero_r1', $_POST['celular_referido_1']);
                                $validacion->execute();
                                $resultsvalidacion = $validacion->fetch(PDO::FETCH_ASSOC);
                                $vecesnumero = $resultsvalidacion['cantidad'];

                                //Si existe
                                if ($vecesnumero === '1') {
                                    echo '<script language="javascript">alert("El número ingresado ya se encuentra registrado en nuestro sistema, intenta con un número diferente");</script>';
                                }

                                //Si no existe
                                else {
                                    //Obtengo los niveles del usuario
                                    require  'db.php';
                                    $niveles = $conn->prepare('SELECT nivel_1, nivel_2, nivel_3, nivel_4, nivel_5, nivel_6, nivel_7, nivel_8, nivel_9 FROM usuarios WHERE phone=:numero');
                                    $niveles->bindParam(':numero', $_SESSION['numero']);
                                    $niveles->execute();
                                    $resultsniveles = $niveles->fetch(PDO::FETCH_ASSOC);

                                    $nivel_1 = $resultsniveles['nivel_1'];
                                    $nivel_2 = $resultsniveles['nivel_2'];
                                    $nivel_3 = $resultsniveles['nivel_3'];
                                    $nivel_4 = $resultsniveles['nivel_4'];
                                    $nivel_5 = $resultsniveles['nivel_5'];
                                    $nivel_6 = $resultsniveles['nivel_6'];
                                    $nivel_7 = $resultsniveles['nivel_7'];
                                    $nivel_8 = $resultsniveles['nivel_8'];
                                    $nivel_9 = $resultsniveles['nivel_9'];

                                    //Generación del PIN

                                    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
                                    $pin = substr(str_shuffle($permitted_chars), 0, 4);
                                    $nequi = intval('0');

                                    //Ingresar el referido
                                    $ingreso_1 = $conn->prepare('INSERT INTO usuarios (phone, name, pin, ingreso, nequi, nivel_1, nivel_2, nivel_3, nivel_4, nivel_5, nivel_6, nivel_7, nivel_8, nivel_9) VALUES (:phone,:name,:pin,:ingreso,:nequi,:nivel_1,:nivel_2,:nivel_3,:nivel_4,:nivel_5,:nivel_6,:nivel_7,:nivel_8,:nivel_9)');
                                    $ingreso_1->bindParam(':phone', $_POST['celular_referido_1']);
                                    $ingreso_1->bindParam(':name', ucwords($_POST['nombre_referido_1']));
                                    $ingreso_1->bindParam(':pin', $pin);
                                    $ingreso_1->bindParam(':ingreso', date('Y-m-d'));
                                    $ingreso_1->bindParam(':nequi', $nequi);
                                    $ingreso_1->bindParam(':nivel_1', $_SESSION['numero']);
                                    $ingreso_1->bindParam(':nivel_2', $nivel_1);
                                    $ingreso_1->bindParam(':nivel_3', $nivel_2);
                                    $ingreso_1->bindParam(':nivel_4', $nivel_3);
                                    $ingreso_1->bindParam(':nivel_5', $nivel_4);
                                    $ingreso_1->bindParam(':nivel_6', $nivel_5);
                                    $ingreso_1->bindParam(':nivel_7', $nivel_6);
                                    $ingreso_1->bindParam(':nivel_8', $nivel_7);
                                    $ingreso_1->bindParam(':nivel_9', $nivel_8);

                                    if ($ingreso_1->execute()) {
                                        include 'variables.php';
                                        msj_bienvenida(ucwords($_POST['nombre_referido_1']), $nombreCliente, $pagina, $_POST['celular_referido_1'], $pin, $youtube_canal, $token, $url_curl);
                                        echo "<script>alert('Referido ingresado correctamente');window.location= 'referidos.php'</script>";
                                    } else {
                                        $message = 'Ocurrio un error, intenta más tarde';
                                    }
                                }
                            } else {
                                echo '<script language="javascript">alert("Los números son diferentes, intenta nuevamente");</script>';
                            }
                        }
                    }
                    ?>

                    <!-- Campos para el Referido 2 -->
                    <div class="grupo_r2">
                        <img src="images/2.png" class="img-1" alt="">
                        <div>
                            <input type="text" name="nombre_referido_2" placeholder="Nombre" disabled><br>
                            <input type="tel" name="celular_referido_2" placeholder="Celular" disabled><br>
                            <input type="tel" name="celular_referido_2_conf" placeholder="Confirmar Celular" disabled><br>
                            <!-- <button name="registrar_referido_2">Registrar</button> -->
                        </div>
                    </div>

                    <?php
                    //Clic en registrar Referido 2
                    if (isset($_POST['registrar_referido_2'])) {
                        echo '<script language="javascript">alert("Debe ingresar el Referido No. 1");</script>';
                    }
                    break;

                    //Si el cliente tiene 1 solo referido
                case '1':

                    //Consultar 1er Referido
                    $refe_1 = $conn->prepare('SELECT name, phone FROM usuarios WHERE nivel_1=:numero');
                    $refe_1->bindParam(':numero', $_SESSION['numero']);
                    $refe_1->execute();
                    $resultado = $refe_1->fetch(PDO::FETCH_ASSOC);
                    $nombre_refe_1 = $resultado['name'];
                    $numero_refe_1 = $resultado['phone'];
                    ?>

                    <!-- Campos del Referido 1 -->
                    <div class="grupo_r1">
                        <img src="images/1.png" class="img-1" alt="">
                        <div>
                            <label for=""><?php echo $nombre_refe_1 ?></label><br>
                            <label for=""><?php echo "Cel. " . $numero_refe_1 ?></label>
                        </div>
                    </div>

                    <!-- Campos para el Referido 2 -->
                    <div class="grupo_r2">
                        <img src="images/2.png" class="img-1" alt="">
                        <div>
                            <input type="text" name="nombre_referido_2" placeholder="Nombre" required><br>
                            <input type="tel" name="celular_referido_2" placeholder="Celular" required><br>
                            <input type="tel" name="celular_referido_2_conf" placeholder="Confirmar Celular" required><br>
                            <button name="registrar_referido_2" onclick="return confirm('Confirmas registro de referido');">Registrar</button>
                        </div>
                    </div>

                    <?php
                    //Clic en registrar Referido 2
                    if (isset($_POST['registrar_referido_2'])) {

                        if (empty($_POST['nombre_referido_2']) || empty($_POST['celular_referido_2']) || empty($_POST['celular_referido_2_conf'])) {
                            echo '<script language="javascript">alert("Un campo se encuentra vacio");</script>';
                        } else {

                            $celular_referido_2_1 = $_POST['celular_referido_2'];
                            $celular_referido_2_2 = $_POST['celular_referido_2_conf'];

                            if ($celular_referido_2_1 === $celular_referido_2_2) {

                                require  'db.php';

                                //Colsultar si el # de celular ya existe
                                $validacion = $conn->prepare('SELECT COUNT(*) AS cantidad FROM usuarios WHERE phone=:numero_r2');
                                $validacion->bindParam(':numero_r2', $_POST['celular_referido_2']);
                                $validacion->execute();
                                $resultsvalidacion = $validacion->fetch(PDO::FETCH_ASSOC);
                                $vecesnumero = $resultsvalidacion['cantidad'];

                                //Si existe
                                if ($vecesnumero === '1') {
                                    echo '<script language="javascript">alert("El número ingresado ya se encuentra registrado en nuestro sistema, intenta con un número diferente");</script>';
                                }

                                //Si no existe
                                else {
                                    //Obtengo los niveles del usuario
                                    require  'db.php';
                                    $niveles = $conn->prepare('SELECT nivel_1, nivel_2, nivel_3, nivel_4, nivel_5, nivel_6, nivel_7, nivel_8, nivel_9 FROM usuarios WHERE phone=:numero');
                                    $niveles->bindParam(':numero', $_SESSION['numero']);
                                    $niveles->execute();
                                    $resultsniveles = $niveles->fetch(PDO::FETCH_ASSOC);

                                    $nivel_1 = $resultsniveles['nivel_1'];
                                    $nivel_2 = $resultsniveles['nivel_2'];
                                    $nivel_3 = $resultsniveles['nivel_3'];
                                    $nivel_4 = $resultsniveles['nivel_4'];
                                    $nivel_5 = $resultsniveles['nivel_5'];
                                    $nivel_6 = $resultsniveles['nivel_6'];
                                    $nivel_7 = $resultsniveles['nivel_7'];
                                    $nivel_8 = $resultsniveles['nivel_8'];
                                    $nivel_9 = $resultsniveles['nivel_9'];

                                    //Generación del PIN

                                    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
                                    $pin = substr(str_shuffle($permitted_chars), 0, 4);
                                    $nequi = intval('0');

                                    //Ingresar el referido
                                    $ingreso_1 = $conn->prepare('INSERT INTO usuarios (phone, name, pin, ingreso, nequi, nivel_1, nivel_2, nivel_3, nivel_4, nivel_5, nivel_6, nivel_7, nivel_8, nivel_9) VALUES (:phone,:name,:pin,:ingreso,:nequi,:nivel_1,:nivel_2,:nivel_3,:nivel_4,:nivel_5,:nivel_6,:nivel_7,:nivel_8,:nivel_9)');
                                    $ingreso_1->bindParam(':phone', $_POST['celular_referido_2']);
                                    $ingreso_1->bindParam(':name', ucwords($_POST['nombre_referido_2']));
                                    $ingreso_1->bindParam(':pin', $pin);
                                    $ingreso_1->bindParam(':ingreso', date('Y-m-d'));
                                    $ingreso_1->bindParam(':nequi', $nequi);
                                    $ingreso_1->bindParam(':nivel_1', $_SESSION['numero']);
                                    $ingreso_1->bindParam(':nivel_2', $nivel_1);
                                    $ingreso_1->bindParam(':nivel_3', $nivel_2);
                                    $ingreso_1->bindParam(':nivel_4', $nivel_3);
                                    $ingreso_1->bindParam(':nivel_5', $nivel_4);
                                    $ingreso_1->bindParam(':nivel_6', $nivel_5);
                                    $ingreso_1->bindParam(':nivel_7', $nivel_6);
                                    $ingreso_1->bindParam(':nivel_8', $nivel_7);
                                    $ingreso_1->bindParam(':nivel_9', $nivel_8);

                                    if ($ingreso_1->execute()) {
                                        include 'variables.php';
                                        msj_bienvenida(ucwords($_POST['nombre_referido_2']), $nombreCliente, $pagina, $_POST['celular_referido_2'], $pin, $youtube_canal, $token, $url_curl);
                                        echo "<script>alert('Referido ingresado correctamente');window.location= 'referidos.php'</script>";
                                    } else {
                                        $message = 'Ocurrio un error, intenta más tarde';
                                    }
                                }
                            } else {
                                echo '<script language="javascript">alert("Los números son diferentes, intenta nuevamente");</script>';
                            }
                        }
                    }

                    break;

                    //Si el cliente tiene 2 referidos
                case '2':

                    //Consultar 1er Referido
                    $refe_1 = $conn->prepare('SELECT name, phone FROM usuarios WHERE nivel_1=:numero ORDER BY phone ASC');
                    $refe_1->bindParam(':numero', $_SESSION['numero']);
                    $refe_1->execute();
                    $referido_1 = $refe_1->fetch(PDO::FETCH_ASSOC);
                    $nombre_refer_1 = $referido_1['name'];
                    $numero_refer_1 = $referido_1['phone'];

                    //Consultar 2o Referido
                    $refe_2 = $conn->prepare('SELECT name, phone FROM usuarios WHERE nivel_1=:numero ORDER BY phone DESC');
                    $refe_2->bindParam(':numero', $_SESSION['numero']);
                    $refe_2->execute();
                    $referido_2 = $refe_2->fetch(PDO::FETCH_ASSOC);
                    $nombre_refer_2 = $referido_2['name'];
                    $numero_refer_2 = $referido_2['phone'];

                    ?>

                    <!-- Campos del Referido 1 -->
                    <div class="grupo_r1">
                        <img src="images/1.png" class="img-1" alt="">
                        <div>
                            <div>
                                <label for=""><?php echo $nombre_refer_1 ?></label><br>
                                <label for=""><?php echo "Cel. " . $numero_refer_1 ?></label>
                            </div>
                        </div>
                    </div>

                    <!-- Campos del Referido 2 -->
                    <div class="grupo_r2">
                        <img src="images/2.png" class="img-1" alt="">
                        <div>
                            <div>
                                <label for=""><?php echo $nombre_refer_2 ?></label><br>
                                <label for=""><?php echo "Cel. " . $numero_refer_2 ?></label>
                            </div>
                        </div>
                    </div>
            <?php
            }

            ?>

            <!-- Envio de Whatsapp de Bienvenida -->

            <?php

            function msj_bienvenida($nombre, $referidopor, $pagina, $celular, $PIN, $video, $token, $url_curl)
            {
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => ''.$url_curl.'',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    //"to": "57' . $celular . '",
                    CURLOPT_POSTFIELDS => '{
                                            "messaging_product": "whatsapp",
                                            "to": "573108635298",
                                            "type": "template",
                                            "template": {
                                                "name": "d3_bienvenida",
                                                "language": {
                                                    "code": "es",
                                                    "policy": "deterministic"
                                                },
                                                "components": [
                                                    {
                                                        "type": "body",
                                                        "parameters": [
                                                            {
                                                                "type": "text",
                                                                "text": "*' . $nombre . '*"
                                                            },
                                                            {
                                                                "type": "text",
                                                                "text": "' . $referidopor . '"
                                                            },
                                                            {
                                                                "type": "text",
                                                                "text": "' . $pagina . '"
                                                            },
                                                            {
                                                                "type": "text",
                                                                "text": "' . $celular . '"
                                                            },
                                                            {
                                                                "type": "text",
                                                                "text": "' . $PIN . '"
                                                            },
                                                            {
                                                                "type": "text",
                                                                "text": "' . $video . '"
                                                            }
                                                        ]
                                                    } 
                                                ]
                                            }
                                            }',
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: Bearer ' . $token . '',
                        'Content-Type: application/json'
                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);
                //echo $response;
            }

            ?>

            <p class="warnings" align="justify"><?php print $message ?></p>

            <center><a style="font-size: 14px; color: #B06AB3; " href="logout.php">Cerrar Sesión</a>
                <p>&nbsp</p>
            </center>
    </form>
</body>

</html>