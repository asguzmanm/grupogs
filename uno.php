<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Día 1</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,500,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/styles/uno.css">
</head>

<body>
    <?php
    date_default_timezone_set("America/Bogota");
    require 'db.php';
    session_start();

    //Colsultar la información del cliente
    $cliente = $conn->prepare('SELECT name, phone, nequi, nivel_1, nivel_3 FROM usuarios WHERE phone=:numero');
    $cliente->bindParam(':numero', $_SESSION['numero']);
    $cliente->execute();
    $resultsCliente = $cliente->fetch(PDO::FETCH_ASSOC);

    $nombreCliente = $resultsCliente['name'];
    $numeroCliente = $resultsCliente['phone'];
    $nequiCliente = $resultsCliente['nequi'];
    $numero_n_1Cliente = $resultsCliente['nivel_1'];
    $numero_n_3Cliente = $resultsCliente['nivel_3']; //Para saber si debe o no realizar devolución

    //Finaliza la consulta de la información del cliente

    //Consultar el Nombre del Nivel 1
    $records = $conn->prepare('SELECT name FROM usuarios WHERE phone=:numero_n_1');
    $records->bindParam(':numero_n_1', $numero_n_1Cliente);
    $records->execute();
    $results = $records->fetch(PDO::FETCH_ASSOC);
    $nombre_n_1 = $results['name'];
    //Finaliza consultar el Nombre del Nivel 1  

    //Consultar el numero de Nivel 3 (a quien debe pagarle)
    $records_3 = $conn->prepare('SELECT nivel_3 FROM usuarios WHERE phone=:numero');
    $records_3->bindParam(':numero', $_SESSION['numero']);
    $records_3->execute();
    $results_3 = $records_3->fetch(PDO::FETCH_ASSOC);
    $numero_nivel_3 = $results_3['nivel_3'];
    //Finaliza Consultar el numero de Nivel 3       

    //Consultar el Nombre, Nequi y PIN de Nivel 3
    $records_31 = $conn->prepare('SELECT name, nequi, pin FROM usuarios WHERE phone=:numero_3');
    $records_31->bindParam(':numero_3', $numero_nivel_3);
    $records_31->execute();
    $results_31 = $records_31->fetch(PDO::FETCH_ASSOC);
    $nombre_nivel_3 = $results_31['name'];
    $nequi_nivel_3 = $results_31['nequi'];
    $pin_nivel_3 = $results_31['pin'];
    //Finaliza Consultar el Nombre y Nequi de Nivel 3
    ?>
    
    <form action="" method="POST" id="form" enctype="multipart/form-data">

        <!-- Mostrar la información del usuario -->

        <div class="contenedor">

            <?php
            $numero = $_SESSION['numero'];
            ?>

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
                    <a style="font-size: 13px; color: #B06AB3; " href='act_nequi.php?.<?php echo codifica_urlget('previa=uno.php&numero=' . $numero) ?>'>Actualizar Nequi</a>
                </div>
            </div>

            <div class="referido">
                <img src="images/informacion.png" class="img-informacion" alt="">
                <label class="referi" for="">Referido por <?php echo $nombre_n_1 ?></label>
            </div>

            <div class="ganancia">
                <label class="ganancia-texto" for="">Realiza la única inversión de $20.000 al <br>número Nequi de la siguiente persona</label><br>
            </div>

            <?php
            function codifica_urlget(string $cadena): string
            {
                $cadena = mb_convert_encoding($cadena, 'UTF-8', mb_detect_encoding($cadena)); // Codifico a UTF-8
                $control = "Esteman106Control";
                $cadena = $control . $cadena . $control;  // Agrego al inicio y al final la palabra de control
                $cadena = base64_encode($cadena);  // Codifico en Base64 
                return $cadena;
            }
            ?>

            <!-- Aqui se indica si debe o no hacer devolución debido a que es cabeza de cadena -->

            <?php
            //No debe realizar devolución de dinero
            if ($numero_n_3Cliente === '0') { ?>
                <div class="pay-made-no">
                    <img src="images/double_check.png" class="img-pay-made-time" alt="">
                    <div>
                        <label class="ganancia-texto" for="">Por ser cabeza de equipo, no debes realizar 1er pago</label>
                    </div>
                </div>
            <?php
            }
            //Debe relizar devolución
            else {
            ?>

                <div class="grupo">
                    <div class="info-consignacion">
                        <img src="images/pago_1.png" class="img-consignacion" alt="">
                        <div class="textos">
                            <!-- <label style="font-weight: bold;">Realizar consignación a</label><br><br> -->
                            <!-- <p>&nbsp</p> -->
                            <label class="hola" for=""><?php echo $nombre_nivel_3 ?></label><br>
                            <label class="numero" for="">Cel. <?php echo $numero_nivel_3 ?></label><br>
                            <label class="nombre" for="">Nequi: <?php echo $nequi_nivel_3 ?></label><br>
                        </div>
                    </div>
                </div>

                <?php

                require 'db.php';

                //Consultar si el pago fue realizado

                $confirmacion = $conn->prepare('SELECT realizado, dateconfirm FROM pago11 WHERE phone=:numero');
                $confirmacion->bindParam(':numero', $_SESSION['numero']);
                $confirmacion->execute();
                $resultado = $confirmacion->fetch(PDO::FETCH_ASSOC);
                $estado = $resultado['realizado'];

                //Consultar si el pago fue confirmado por L3

                $recibido = $conn->prepare('SELECT confirmado, daterecibe FROM pago11 WHERE phone=:numero');
                $recibido->bindParam(':numero', $_SESSION['numero']);
                $recibido->execute();
                $resultado_recibido = $recibido->fetch(PDO::FETCH_ASSOC);
                $estado_recibido = $resultado_recibido['confirmado'];


                if ($estado === 'Si') {
                ?>
                    <div class="pay-made">
                        <img src="images/paymade.png" class="img-pay-made" alt="">
                        <div>
                            <label class="ganancia-texto" for="">Usted confirmó la consignación el <br> <?php echo date("d-M-Y g:i a", strtotime(($resultado['dateconfirm']))) ?></label><br>
                        </div>
                    </div>
                    <?php

                    if ($estado_recibido === 'No') { ?>
                        <div class="pay-made-no">
                            <img src="images/time.gif" class="img-pay-made-time" alt="">
                            <div>
                                <label class="ganancia-texto" for="">La consignación pronto será confirmada <br> por <?php echo $nombre_nivel_3 ?></label><br>
                            </div>
                        </div>
                    <?php

                    } else { ?>

                        <div class="pay-made">
                            <img src="images/double_check.png" class="img-pay-made-time" alt="">
                            <div>
                                <label class="ganancia-texto" for="">La consignación fue confirmada por <br> <?php echo $nombre_nivel_3 . " el<br>" . date("d-M-Y g:i a", strtotime(($resultado_recibido['daterecibe']))) ?></label><br>
                            </div>
                        </div>
                    <?php
                    }
                } else {
                    ?>

                    <div class="subirimagen">
                        <label style="font-size: 14px; font-weight: bold;">Adjuntar Pago</label>
                        <input class="subir" type="file" name="image" required />
                    </div>

                    <button name="boton_confirmar_pago_1" onclick="return confirm('¿Confirmar la consignación?');">Confirmar Consignación</button><b>&nbsp</b>

            <?php

                    if (isset($_REQUEST['boton_confirmar_pago_1'])) {
                        if (isset($_FILES['image']['name'])) {

                            //Reducir imagen y guardar en servidor

                            $img_type = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                            $nombre = $_SESSION['numero'] . "_to_" . $numero_nivel_3 . "_pago1." . $img_type;

                            if ($img_type === 'PNG' || $img_type === 'png') {

                                $img_origen = imagecreatefrompng($_FILES['image']['tmp_name']);
                                $ancho_origen = imagesx($img_origen);
                                $alto_origen = imagesy($img_origen);

                                $ancho_limite = 1560;

                                if ($ancho_origen > $alto_origen) {
                                    $ancho_origen = $ancho_limite;
                                    $alto_origen = $ancho_limite * imagesy($img_origen) / imagesx($img_origen);
                                } else {
                                    $alto_origen = $ancho_limite;
                                    $ancho_origen = $ancho_limite * imagesx($img_origen) / imagesy($img_origen);
                                }

                                $img_destino = imagecreatetruecolor($ancho_origen, $alto_origen);
                                imagecopyresized($img_destino, $img_origen, 0, 0, 0, 0, $ancho_origen, $alto_origen, imagesx($img_origen), imagesy($img_origen));
                                imagepng($img_destino, 'images/evidencias/' . $nombre);

                                require  'db.php';
                                $phone = $_SESSION['numero'];
                                $fecha = date('Y-m-d H:i:s');
                                $valor = intval('20000');
                                $comodin = date('0000-00-00 00:00:00');
                                $realizado = 'Si';
                                $confirmado = 'No';

                                $sql = $conn->prepare('INSERT INTO pago11 (phone, phone_destino, valor, dateconfirm, realizado, daterecibe, confirmado, evidencia) VALUES (:phone,:numero_nivel_3,:valor,:fecha,:realizado,:comodin,:confirmado,:nombre)');
                                $sql->bindParam(':phone', $phone);
                                $sql->bindParam(':numero_nivel_3', $numero_nivel_3);
                                $sql->bindParam(':valor', $valor);
                                $sql->bindParam(':fecha', $fecha);
                                $sql->bindParam(':realizado', $realizado);
                                $sql->bindParam(':comodin', $comodin);
                                $sql->bindParam(':confirmado', $confirmado);
                                $sql->bindParam(':nombre', $nombre);

                                if ($sql->execute()) {
                                    include 'variables.php';
                                    notificar_primer_pago(ucwords($nombre_nivel_3), "11 (Día de 1er Pago)", $nombreCliente, $numeroCliente, number_format($valor, 0), "1er Pago", $pagina, $numero_nivel_3, $pin_nivel_3, $youtube_confirmar_pago, $token, $url_curl);
                                    echo '<script language="javascript">alert("Pago confirmado correctamente");</script>';
                                    header('Refresh: 1; url=uno.php');
                                } else {
                                    $message = 'Ocurrio un error, intenta más tarde';
                                }
                            } elseif ($img_type === 'JPG' || $img_type === 'JPEG' || $img_type === 'jpeg' || $img_type === 'jpg') {

                                $img_origen = imagecreatefromjpeg($_FILES['image']['tmp_name']);
                                $ancho_origen = imagesx($img_origen);
                                $alto_origen = imagesy($img_origen);

                                $ancho_limite = 1560;

                                if ($ancho_origen > $alto_origen) {
                                    $ancho_origen = $ancho_limite;
                                    $alto_origen = $ancho_limite * imagesy($img_origen) / imagesx($img_origen);
                                } else {
                                    $alto_origen = $ancho_limite;
                                    $ancho_origen = $ancho_limite * imagesx($img_origen) / imagesy($img_origen);
                                }

                                $img_destino = imagecreatetruecolor($ancho_origen, $alto_origen);
                                imagecopyresized($img_destino, $img_origen, 0, 0, 0, 0, $ancho_origen, $alto_origen, imagesx($img_origen), imagesy($img_origen));
                                imagejpeg($img_destino, 'images/evidencias/' . $nombre);

                                require  'db.php';
                                $phone = $_SESSION['numero'];
                                $fecha = date('Y-m-d H:i:s');
                                $valor = intval('20000');
                                $comodin = date('0000-00-00 00:00:00');
                                $realizado = 'Si';
                                $confirmado = 'No';

                                $sql = $conn->prepare('INSERT INTO pago11 (phone, phone_destino, valor, dateconfirm, realizado, daterecibe, confirmado, evidencia) VALUES (:phone,:numero_nivel_3,:valor,:fecha,:realizado,:comodin,:confirmado,:nombre)');
                                $sql->bindParam(':phone', $phone);
                                $sql->bindParam(':numero_nivel_3', $numero_nivel_3);
                                $sql->bindParam(':valor', $valor);
                                $sql->bindParam(':fecha', $fecha);
                                $sql->bindParam(':realizado', $realizado);
                                $sql->bindParam(':comodin', $comodin);
                                $sql->bindParam(':confirmado', $confirmado);
                                $sql->bindParam(':nombre', $nombre);

                                if ($sql->execute()) {
                                    include 'variables.php';
                                    notificar_primer_pago(ucwords($nombre_nivel_3), "11 (Día de 1er Pago)", $nombreCliente, $numeroCliente, number_format($valor, 0), "1er Pago", $pagina, $numero_nivel_3, $pin_nivel_3, $youtube_confirmar_pago, $token, $url_curl);
                                    echo '<script language="javascript">alert("Pago confirmado correctamente");</script>';
                                    header('Refresh: 1; url=uno.php');
                                } else {
                                    $message = 'Ocurrio un error, intenta más tarde';
                                }
                            } else {
                                echo '<script language="javascript">alert("Formato no valido");</script>';
                            }

                            //Fin de reducir imagen y guardar en servidor
                        }
                    }
                }
            }
            ?>

            <!-- Envio de Whatsapp informando que se realizo el primer pago -->

            <?php

            function notificar_primer_pago($nombre_dest, $dia, $nombre, $cel, $valor, $motivo, $pagina, $celular, $PIN, $video, $token, $url_curl)
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
                                                "name": "dm_pago_realizado",
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
                                                                "text": "' . $nombre_dest . '"
                                                            },
                                                            {
                                                                "type": "text",
                                                                "text": "' . $dia . '"
                                                            },
                                                            {
                                                                "type": "text",
                                                                "text": "' . $nombre . '"
                                                            },
                                                            {
                                                                "type": "text",
                                                                "text": "' . $cel . '"
                                                            },
                                                            {
                                                                "type": "text",
                                                                "text": "' . $valor . '"
                                                            },
                                                            {
                                                                "type": "text",
                                                                "text": "' . $motivo . '"
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

            <p class="warnings" align="justify"><?php print $message ?></p><b>&nbsp</b>

            <center><a style="font-size: 14px; color: #B06AB3; " href="logout.php">Cerrar Sesión</a>
                <p>&nbsp</p>
                <p>&nbsp</p>
            </center>

        </div>
    </form>
</body>

</html>