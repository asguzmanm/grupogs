<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Día 33</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,500,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/styles/veintidos.css">
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

    //Consultar los 8 del Nivel 6 (Quienes pagan)
    $records_3 = $conn->prepare('SELECT DISTINCT nivel_6 FROM usuarios WHERE nivel_9=:numero');
    $records_3->bindParam(':numero', $_SESSION['numero']);
    $records_3->execute();

    //Consultar si tiene personas que le paguen    
    $personas = $conn->prepare('SELECT COUNT(DISTINCT nivel_6) AS cantidad FROM usuarios WHERE nivel_9=:numero');
    $personas->bindParam(':numero', $_SESSION['numero']);
    $personas->execute();
    $resultspersonas = $personas->fetch(PDO::FETCH_ASSOC);
    $numeropersonas = $resultspersonas['cantidad'];
    //Finaliza consultar si tiene personas que le paguen   

    $saldorecibido = $conn->prepare('SELECT sum(valor) AS recibido from pago33 WHERE realizado="Si" AND phone_destino=:destino');
    $saldorecibido->bindParam(':destino', $_SESSION['numero']);
    $saldorecibido->execute();
    $resultado_saldor = $saldorecibido->fetch(PDO::FETCH_ASSOC);
    $saldor = $resultado_saldor['recibido'];

    $saldoconfirmado = $conn->prepare('SELECT sum(valor) AS confirmado from pago33 WHERE realizado="Si" AND confirmado="Si" AND phone_destino=:destino');
    $saldoconfirmado->bindParam(':destino', $_SESSION['numero']);
    $saldoconfirmado->execute();
    $resultado_saldoc = $saldoconfirmado->fetch(PDO::FETCH_ASSOC);
    $saldoc = $resultado_saldoc['confirmado'];

    $records_ref = $conn->prepare('SELECT name FROM usuarios WHERE nivel_1=:numero_n_1');
    $records_ref->bindParam(':numero_n_1', $_SESSION['numero']);
    $records_ref->execute();

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
                    <!-- <a style="font-size: 13px; color: #B06AB3; " href='act_nequi.php?.<?php echo codifica_urlget('previa=uno.php&numero=' . $numero) ?>'>Actualizar Nequi</a> -->
                </div>
            </div>

            <div class="referido">
                <img src="images/informacion.png" class="img-informacion" alt="">
                <label class="referi" for="">Referido por <?php echo $nombre_n_1 ?></label>
            </div>

            <div class="estado-cuenta">

                <img src="images/recibo.png" class="img-consignacion" alt="">
                <div>
                    <label>Recibido</label><br>
                    <label style="font-size: 18px; font-weight: bold;"><?php echo "$" . number_format($saldor, 0) ?></label>
                </div>
                <div>
                    <label>Confirmado</label><br>
                    <label style="font-size: 18px; font-weight: bold;"><?php echo "$" . number_format($saldoc, 0) ?></label>
                </div>


            </div>

            <div class="ganancia">
                <label class="ganancia-texto" for="">Las siguientes personas deben realizarte la devolución del 50%. Por favor, has clic <br> en confirmar cuando lo hayan hecho.</label><br>
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

            //No tiene personas para consignarle
            if ($numeropersonas === '0') { ?>
                <div class="pay-made-no">
                    <img src="images/prohibido.png" class="img-pay-made-time" alt="">
                    <div>
                        <label class="ganancia-texto" for="">Lo sentimos, no tienes personas para consignarte</label>
                    </div>
                </div>

                <?php

                require 'db.php';
                //Estado de cuenta (cuanto recibio en cada día de pago)

                //Día 11
                $saldorecibido11 = $conn->prepare('SELECT sum(valor) AS recibido from pago11 WHERE realizado="Si" AND phone_destino=:destino');
                $saldorecibido11->bindParam(':destino', $_SESSION['numero']);
                $saldorecibido11->execute();
                $resultado_saldor11 = $saldorecibido11->fetch(PDO::FETCH_ASSOC);
                $saldor11 = $resultado_saldor11['recibido'];

                //Día 22
                $saldorecibido22 = $conn->prepare('SELECT sum(valor) AS recibido from pago22 WHERE realizado="Si" AND phone_destino=:destino');
                $saldorecibido22->bindParam(':destino', $_SESSION['numero']);
                $saldorecibido22->execute();
                $resultado_saldor22 = $saldorecibido22->fetch(PDO::FETCH_ASSOC);
                $saldor22 = $resultado_saldor22['recibido'];

                //Día 33
                $saldorecibido33 = $conn->prepare('SELECT sum(valor) AS recibido from pago33 WHERE realizado="Si" AND phone_destino=:destino');
                $saldorecibido33->bindParam(':destino', $_SESSION['numero']);
                $saldorecibido33->execute();
                $resultado_saldor33 = $saldorecibido33->fetch(PDO::FETCH_ASSOC);
                $saldor33 = $resultado_saldor33['recibido'];

                $total_normal = ($saldor11 / 2) + ($saldor22 / 2) + $saldor33;
                $total_cabeza = $saldor11 + $saldor22 + $saldor33;

                ?>

                <center><br><label class="ganancia-texto1" style="font-weight: bold; font-size: 18px;" for="">Estado de cuenta</label></center><br>

                <div class="account-status">
                    <div class="interno">
                        <img class="dia-numero" src="/images/11.png"><br><br><br>
                        <label style="font-size: 10px;" for="">Ganancia</label><br>
                        <label style="font-size: 20px; font-weight: bold;" for=""><?php if ($numero_n_3Cliente === '0') {
                                                                                        echo "$" . number_format($saldor11, 0);
                                                                                    } else {
                                                                                        echo "$" . number_format($saldor11 / 2, 0);
                                                                                    } ?></label>
                    </div>
                    <div class="interno">
                        <img class="dia-numero" src="/images/22.png"><br><br><br>
                        <label style="font-size: 10px;" for="">Ganancia</label><br>
                        <label style="font-size: 20px; font-weight: bold;" for=""><?php if ($numero_n_3Cliente === '0') {
                                                                                        echo "$" . number_format($saldor22, 0);
                                                                                    } else {
                                                                                        echo "$" . number_format($saldor22 / 2, 0);
                                                                                    } ?></label>
                    </div>
                    <div class="interno">
                        <img class="dia-numero" src="/images/33.png"><br><br><br>
                        <label style="font-size: 10px;" for="">Ganancia</label><br>
                        <label style="font-size: 20px; font-weight: bold;" for=""><?php echo "$" . number_format($saldor33, 0) ?></label>
                    </div>
                    <div class="interno">
                        <img class="total" src="/images/total.png"><br><br><br>
                        <label style="font-size: 10px;" for="">Ganancia</label><br>
                        <label style="font-size: 20px; font-weight: bold;" for=""><?php if ($numero_n_3Cliente === '0') {
                                                                                        echo "$" . number_format(($total_cabeza), 0);
                                                                                    } else {
                                                                                        echo "$" . number_format($total_normal, 0);
                                                                                    } ?></label>
                    </div>
                </div>

                <div class="cabecera-info">
                    <label class="ganancia-texto1" for="">Una vez hayas confirmado las consignaciones,<br>realiza el pago del 20% equivalente a:</label><br><br>
                    <label class="devolucion" for=""><?php if ($numero_n_3Cliente === '0') {
                                                            echo "$" . number_format(($total_cabeza * 0.2), 0);
                                                        } else {
                                                            echo "$" . number_format($total_normal * 0.2, 0);
                                                        } ?></label><br>
                    <label style="font-size: 14px" for=""><?php if ($numero_n_3Cliente === '0') {
                                                                                    echo "($" . number_format(($total_cabeza), 0) . "x20%)";
                                                                                } else {
                                                                                    echo "($" . number_format($total_normal, 0) . "x20%)";
                                                                                } ?></label><br><br>                    
                    <label class="ganancia-texto1" for="">al siguiente Nequi:</label>
                </div>

                <div class="grupo">
                    <div class="info-consignacion11">
                        <img src="images/pago_1.png" class="img-consignacion" alt="">
                        <div class="textos">
                            <label class="nombre" for="">Nequi: 3108635298</label><br>
                        </div>
                    </div>
                </div>
                <br>

                <?php

                require 'db.php';

                //Consultar si el pago fue realizado

                $confirmacion = $conn->prepare('SELECT realizado, dateconfirm FROM devadmin WHERE phone=:numero');
                $confirmacion->bindParam(':numero', $_SESSION['numero']);
                $confirmacion->execute();
                $resultado = $confirmacion->fetch(PDO::FETCH_ASSOC);
                $estado = $resultado['realizado'];

                //Consultar si el pago fue confirmado por L9

                $recibido = $conn->prepare('SELECT confirmado, daterecibe FROM devadmin WHERE phone=:numero');
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
                                <label class="ganancia-texto" for="">La consignación pronto sera confirmada <br> por la Administración</label><br>
                            </div>
                        </div>
                    <?php

                    } else { ?>

                        <div class="pay-made">
                            <img src="images/double_check.png" class="img-pay-made-time" alt="">
                            <div>
                                <label class="ganancia-texto" for="">La consignación fue confirmada por la Administración el<br> <?php echo date("d-M-Y g:i a", strtotime(($resultado_recibido['daterecibe']))) ?></label><br>
                            </div>
                        </div>
                    <?php
                    }
                } else { //AQUI LO PEGUE
                    ?>
                    <div style="text-align: center;">
                        <label style="font-size: 22px; font-weight: bold;" for="">! Importante ¡</label>
                    </div>

                    <div class="grupo-adv">
                        <div class="info-consignacion-adv">
                            <img src="images/advertencia.png" class="img-consignacion" alt="">
                            <div class="textos">
                                <label class="nombre-adv" for="">Recuerda que si no realizas el pago del 20% a la Administración tus referidos<br> <?php while ($results_ref = $records_ref->fetch(PDO::FETCH_ASSOC)) {
                                                                                                                                                echo $nombre_ref = $results_ref['name'] . "<br>";
                                                                                                                                            } ?>serán eliminados del sistema y no recibirán <br> el último pago en su día 33</label><br>
                            </div>
                        </div>
                    </div>

                    <div class="subirimagen">
                        <label style="font-size: 14px; font-weight: bold;">Adjuntar Pago</label>
                        <input class="subir" type="file" name="image" required />
                    </div>

                    <button name="boton_confirmar_pago_33" onclick="return confirm('Confirmar consignación');">Confirmar Consignación</button><b>&nbsp</b>

                <?php
                    $admin = 3108635298;
                    if (isset($_REQUEST['boton_confirmar_pago_33'])) {

                        if (isset($_FILES['image']['name'])) {

                            //Reducir imagen y guardar en servidor

                            $img_type = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                            echo $nombre = $_SESSION['numero'] . "_to_" . $admin . "_pago33." . $img_type;

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

                                if ($numero_n_3Cliente === '0') {
                                    $valor = $total_cabeza * 0.2;
                                } else {
                                    $valor = $total_normal * 0.2;
                                }

                                $comodin = date('0000-00-00 00:00:00');
                                $realizado = 'Si';
                                $confirmado = 'No';

                                $sql = $conn->prepare('INSERT INTO devadmin (phone, phone_destino, valor, dateconfirm, realizado, daterecibe, confirmado, evidencia) VALUES (:phone,:numero_nivel_3,:valor,:fecha,:realizado,:comodin,:confirmado,:nombre)');
                                $sql->bindParam(':phone', $phone);
                                $sql->bindParam(':numero_nivel_3', $admin);
                                $sql->bindParam(':valor', $valor);
                                $sql->bindParam(':fecha', $fecha);
                                $sql->bindParam(':realizado', $realizado);
                                $sql->bindParam(':comodin', $comodin);
                                $sql->bindParam(':confirmado', $confirmado);
                                $sql->bindParam(':nombre', $nombre);

                                if ($sql->execute()) {
                                    include 'variables.php';
                                    notificar_devolucion_20("Administración", $nombreCliente, $numeroCliente, number_format($valor, 0), "Pago del 20%",  $token, $url_curl);
                                    echo "<script>alert('Pago Confirmado');window.location= 'treintaytres.php'</script>";
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

                                if ($numero_n_3Cliente === '0') {
                                    $valor = $total_cabeza * 0.2;
                                } else {
                                    $valor = $total_normal * 0.2;
                                }

                                $comodin = date('0000-00-00 00:00:00');
                                $realizado = 'Si';
                                $confirmado = 'No';

                                $sql = $conn->prepare('INSERT INTO devadmin (phone, phone_destino, valor, dateconfirm, realizado, daterecibe, confirmado, evidencia) VALUES (:phone,:numero_nivel_3,:valor,:fecha,:realizado,:comodin,:confirmado,:nombre)');
                                $sql->bindParam(':phone', $phone);
                                $sql->bindParam(':numero_nivel_3', $admin);
                                $sql->bindParam(':valor', $valor);
                                $sql->bindParam(':fecha', $fecha);
                                $sql->bindParam(':realizado', $realizado);
                                $sql->bindParam(':comodin', $comodin);
                                $sql->bindParam(':confirmado', $confirmado);
                                $sql->bindParam(':nombre', $nombre);

                                if ($sql->execute()) {
                                    include 'variables.php';
                                    notificar_devolucion_20("Administración", $nombreCliente, $numeroCliente, number_format($valor, 0), "Pago del 20%",  $token, $url_curl);
                                    echo "<script>alert('Pago Confirmado');window.location= 'treintaytres.php'</script>";
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

            //Si tiene personas para consignarle
            else {
                ?>

                <div class="cabecera">
                    <label>Inversionista</label>
                    <label>Pago<br>hecho</label>
                    <label>Recibo<br>de pago</label>
                    <label>Acción</label>
                </div>

                <div class="grupo">

                    <?php

                    while ($results_3 = $records_3->fetch(PDO::FETCH_ASSOC)) {

                        //Consultar el pago de cada uno de los 8 del Nivel 6 (Quienes pagan)
                        require 'db.php';
                        $confirmacion = $conn->prepare('SELECT realizado, valor FROM pago33 WHERE phone=:phone');
                        $confirmacion->bindParam(':phone', $results_3['nivel_6']);
                        $confirmacion->execute();
                        $resultado_conf = $confirmacion->fetch(PDO::FETCH_ASSOC);
                        $estado_conf = $resultado_conf['realizado'];
                        $estado_valor = $resultado_conf['valor'];

                        //Consultar el nombre de cada uno de los 8 del Nivel 6 (Quienes pagan)
                        $nombre_n3 = $conn->prepare('SELECT name, pin FROM usuarios WHERE phone=:phone');
                        $nombre_n3->bindParam(':phone', $results_3['nivel_6']);
                        $nombre_n3->execute();
                        $resultado_nombre_n3 = $nombre_n3->fetch(PDO::FETCH_ASSOC);

                        if ($estado_conf === 'Si') {
                    ?>
                            <div class="info-consignacion">
                                <label class="hola" for=""><?php echo $resultado_nombre_n3['name'] . "<br>" . "Cel. " . $results_3['nivel_6'] . "<br>"; ?></label>
                                <label class="pago-hecho" for=""><?php echo "$" . number_format($estado_valor, 0) ?></label>
                                <a href='evidencia.php?.<?php echo codifica_urlget('previa=treintaytres.php&basedatos=pago33&numerofrom=' . $results_3['nivel_6']) ?>' target="_blank" onclick="window.open(this.href,this.target,'width=360,height=720,top=200,left=200,toolbar=no,location=no,status=no,menubar=no');return false;">Ver</a>

                                <?php
                                require 'db.php';
                                $confirmacion_recibido = $conn->prepare('SELECT confirmado FROM pago33 WHERE phone=:phone');
                                $confirmacion_recibido->bindParam(':phone', $results_3['nivel_6']);
                                $confirmacion_recibido->execute();
                                $resultado_confirmacion = $confirmacion_recibido->fetch(PDO::FETCH_ASSOC);
                                $estado_confirmacion = $resultado_confirmacion['confirmado'];

                                if ($estado_confirmacion === 'Si') { ?>
                                    <label class="pago-hecho"><?php echo "Confirmado" ?></label>
                                <?php
                                } else {

                                    //Datos para enviar el mensaje de confirmación de pago por Whatsapp
                                    $nombre_orig = $resultado_nombre_n3['name'];
                                    $nombreCliente = $resultsCliente['name'];
                                    $numeroCliente = $resultsCliente['phone'];
                                    $valor = intval($estado_valor);
                                    $motivo = "Devolución del 50%";
                                    $pin_orig = $resultado_nombre_n3['pin'];
                                    include 'variables.php';

                                ?>
                                    <a class="confirmar-pago" href='confirmar_pago.php?.<?php echo codifica_urlget('previa=treintaytres.php&basedatosp=pago33&basedatosr=pago33&numero=' . $results_3['nivel_6'] . '&nombre_orig=' . $nombre_orig . '&nombre_cliente=' . $nombreCliente . '&cel_cliente=' . $numeroCliente . '&valor=' . $valor . '&motivo=' . $motivo . '&pin_orig=' . $pin_orig) ?>'>Confirmar</a>
                                <?php
                                }

                                ?>
                            </div>
                        <?php

                        } else {
                        ?>
                            <div class="info-consignacion">
                                <label class="hola" for=""><?php echo $resultado_nombre_n3['name'] . "<br>" . "Cel. " . $results_3['nivel_6'] . "<br>"; ?></label>
                                <label class="pago-no-hecho"><?php echo "No" ?></label>
                                <a></a>
                            </div>
                    <?php

                        }
                    }

                    //Estadode cuenta (cuanto recibio en cada día de pago)

                    //Día 11
                    $saldorecibido11 = $conn->prepare('SELECT sum(valor) AS recibido from pago11 WHERE realizado="Si" AND phone_destino=:destino');
                    $saldorecibido11->bindParam(':destino', $_SESSION['numero']);
                    $saldorecibido11->execute();
                    $resultado_saldor11 = $saldorecibido11->fetch(PDO::FETCH_ASSOC);
                    $saldor11 = $resultado_saldor11['recibido'];

                    //Día 22
                    $saldorecibido22 = $conn->prepare('SELECT sum(valor) AS recibido from pago22 WHERE realizado="Si" AND phone_destino=:destino');
                    $saldorecibido22->bindParam(':destino', $_SESSION['numero']);
                    $saldorecibido22->execute();
                    $resultado_saldor22 = $saldorecibido22->fetch(PDO::FETCH_ASSOC);
                    $saldor22 = $resultado_saldor22['recibido'];

                    //Día 33
                    $saldorecibido33 = $conn->prepare('SELECT sum(valor) AS recibido from pago33 WHERE realizado="Si" AND phone_destino=:destino');
                    $saldorecibido33->bindParam(':destino', $_SESSION['numero']);
                    $saldorecibido33->execute();
                    $resultado_saldor33 = $saldorecibido33->fetch(PDO::FETCH_ASSOC);
                    $saldor33 = $resultado_saldor33['recibido'];

                    $total_normal = ($saldor11 / 2) + ($saldor22 / 2) + $saldor33;
                    $total_cabeza = $saldor11 + $saldor22 + $saldor33;

                    ?>

                </div>

                <center><br><label class="ganancia-texto1" style="font-weight: bold; font-size: 18px;" for="">Estado de cuenta</label></center><br>

                <div class="account-status">
                    <div class="interno">
                        <img class="dia-numero" src="/images/11.png"><br><br><br>
                        <label style="font-size: 10px;" for="">Ganancia</label><br>
                        <label style="font-size: 20px; font-weight: bold;" for=""><?php if ($numero_n_3Cliente === '0') {
                                                                                        echo "$" . number_format($saldor11, 0);
                                                                                    } else {
                                                                                        echo "$" . number_format($saldor11 / 2, 0);
                                                                                    } ?></label>
                    </div>
                    <div class="interno">
                        <img class="dia-numero" src="/images/22.png"><br><br><br>
                        <label style="font-size: 10px;" for="">Ganancia</label><br>
                        <label style="font-size: 20px; font-weight: bold;" for=""><?php if ($numero_n_3Cliente === '0') {
                                                                                        echo "$" . number_format($saldor22, 0);
                                                                                    } else {
                                                                                        echo "$" . number_format($saldor22 / 2, 0);
                                                                                    } ?></label>
                    </div>
                    <div class="interno">
                        <img class="dia-numero" src="/images/33.png"><br><br><br>
                        <label style="font-size: 10px;" for="">Ganancia</label><br>
                        <label style="font-size: 20px; font-weight: bold;" for=""><?php echo "$" . number_format($saldor33, 0) ?></label>
                    </div>
                    <div class="interno">
                        <img class="total" src="/images/total.png"><br><br><br>
                        <label style="font-size: 10px;" for="">Ganancia</label><br>
                        <label style="font-size: 20px; font-weight: bold;" for=""><?php if ($numero_n_3Cliente === '0') {
                                                                                        echo "$" . number_format(($total_cabeza), 0);
                                                                                    } else {
                                                                                        echo "$" . number_format($total_normal, 0);
                                                                                    } ?></label>
                    </div>
                </div>

                <div class="cabecera-info">
                    <label class="ganancia-texto1" for="">Una vez hayas confirmado las consignaciones,<br>realiza el pago del 20% equivalente a:</label><br><br>
                    <label class="devolucion" for=""><?php if ($numero_n_3Cliente === '0') {
                                                            echo "$" . number_format(($total_cabeza * 0.2), 0);
                                                        } else {
                                                            echo "$" . number_format($total_normal * 0.2, 0);
                                                        } ?></label><br>
                    <label style="font-size: 14px" for=""><?php if ($numero_n_3Cliente === '0') {
                                                                                    echo "($" . number_format(($total_cabeza), 0) . "x20%)";
                                                                                } else {
                                                                                    echo "($" . number_format($total_normal, 0) . "x20%)";
                                                                                } ?></label><br><br>                    
                    <label class="ganancia-texto1" for="">al siguiente Nequi:</label>
                </div>

                <div class="grupo">
                    <div class="info-consignacion11">
                        <img src="images/pago_1.png" class="img-consignacion" alt="">
                        <div class="textos">
                            <label class="nombre" for="">Nequi: 3108635298</label><br>
                        </div>
                    </div>
                </div>
                <br>

                <?php

                require 'db.php';

                //Consultar si el pago fue realizado

                $confirmacion = $conn->prepare('SELECT realizado, dateconfirm FROM devadmin WHERE phone=:numero');
                $confirmacion->bindParam(':numero', $_SESSION['numero']);
                $confirmacion->execute();
                $resultado = $confirmacion->fetch(PDO::FETCH_ASSOC);
                $estado = $resultado['realizado'];

                //Consultar si el pago fue confirmado por L9

                $recibido = $conn->prepare('SELECT confirmado, daterecibe FROM devadmin WHERE phone=:numero');
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
                                <label class="ganancia-texto" for="">La consignación pronto sera confirmada <br> por la Administración</label><br>
                            </div>
                        </div>
                    <?php

                    } else { ?>

                        <div class="pay-made">
                            <img src="images/double_check.png" class="img-pay-made-time" alt="">
                            <div>
                                <label class="ganancia-texto" for="">La consignación fue confirmada por la Administración el<br> <?php echo date("d-M-Y g:i a", strtotime(($resultado_recibido['daterecibe']))) ?></label><br>
                            </div>
                        </div>
                    <?php
                    }
                } else { //AQUI LO PEGUE
                    ?>
                    <div style="text-align: center;">
                        <label style="font-size: 22px; font-weight: bold;" for="">! Importante ¡</label>
                    </div>

                    <div class="grupo-adv">
                        <div class="info-consignacion-adv">
                            <img src="images/advertencia.png" class="img-consignacion" alt="">
                            <div class="textos">
                                <label class="nombre-adv" for="">Recuerda que si no realizas el pago del 20% a la Administración tus referidos<br> <?php while ($results_ref = $records_ref->fetch(PDO::FETCH_ASSOC)) {
                                                                                                                                                echo $nombre_ref = $results_ref['name'] . "<br>";
                                                                                                                                            } ?>serán eliminados del sistema y no recibirán <br> el último pago en su día 33</label><br>
                            </div>
                        </div>
                    </div>
                    <div class="subirimagen">
                        <label style="font-size: 14px; font-weight: bold;">Adjuntar Pago</label>
                        <input class="subir" type="file" name="image" required />
                    </div>

                    <button name="boton_confirmar_pago_33" onclick="return confirm('Confirmar consignación');">Confirmar Consignación</button><b>&nbsp</b>

            <?php
                    $admin = 3108635298;
                    if (isset($_REQUEST['boton_confirmar_pago_33'])) {

                        if (isset($_FILES['image']['name'])) {

                            //Reducir imagen y guardar en servidor

                            $img_type = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                            echo $nombre = $_SESSION['numero'] . "_to_" . $admin . "_pago33." . $img_type;

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

                                if ($numero_n_3Cliente === '0') {
                                    $valor = $total_cabeza * 0.2;
                                } else {
                                    $valor = $total_normal * 0.2;
                                }

                                $comodin = date('0000-00-00 00:00:00');
                                $realizado = 'Si';
                                $confirmado = 'No';

                                $sql = $conn->prepare('INSERT INTO devadmin (phone, phone_destino, valor, dateconfirm, realizado, daterecibe, confirmado, evidencia) VALUES (:phone,:numero_nivel_3,:valor,:fecha,:realizado,:comodin,:confirmado,:nombre)');
                                $sql->bindParam(':phone', $phone);
                                $sql->bindParam(':numero_nivel_3', $admin);
                                $sql->bindParam(':valor', $valor);
                                $sql->bindParam(':fecha', $fecha);
                                $sql->bindParam(':realizado', $realizado);
                                $sql->bindParam(':comodin', $comodin);
                                $sql->bindParam(':confirmado', $confirmado);
                                $sql->bindParam(':nombre', $nombre);

                                if ($sql->execute()) {
                                    include 'variables.php';
                                    notificar_devolucion_20("Administración", $nombreCliente, $numeroCliente, number_format($valor, 0), "Pago del 20%",  $token, $url_curl);
                                    echo "<script>alert('Pago Confirmado');window.location= 'treintaytres.php'</script>";
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

                                if ($numero_n_3Cliente === '0') {
                                    $valor = $total_cabeza * 0.2;
                                } else {
                                    $valor = $total_normal * 0.2;
                                }

                                $comodin = date('0000-00-00 00:00:00');
                                $realizado = 'Si';
                                $confirmado = 'No';

                                $sql = $conn->prepare('INSERT INTO devadmin (phone, phone_destino, valor, dateconfirm, realizado, daterecibe, confirmado, evidencia) VALUES (:phone,:numero_nivel_3,:valor,:fecha,:realizado,:comodin,:confirmado,:nombre)');
                                $sql->bindParam(':phone', $phone);
                                $sql->bindParam(':numero_nivel_3', $admin);
                                $sql->bindParam(':valor', $valor);
                                $sql->bindParam(':fecha', $fecha);
                                $sql->bindParam(':realizado', $realizado);
                                $sql->bindParam(':comodin', $comodin);
                                $sql->bindParam(':confirmado', $confirmado);
                                $sql->bindParam(':nombre', $nombre);

                                if ($sql->execute()) {
                                    include 'variables.php';
                                    notificar_devolucion_20("Administración", $nombreCliente, $numeroCliente, number_format($valor, 0), "Pago del 20%",  $token, $url_curl);
                                    echo "<script>alert('Pago Confirmado');window.location= 'treintaytres.php'</script>";
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

            function notificar_devolucion_20($nombre_dest, $nombre, $cel, $valor, $motivo, $token, $url_curl)
            {
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => '' . $url_curl . '',
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
                                    "name": "d33_pago_realizado_admin",
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

            <p class="warnings" align="justify"><?php print $message ?></p><b></b>

            <center><a style="font-size: 14px; color: #B06AB3; " href="logout.php">Cerrar Sesión</a>
                <p>&nbsp</p>
            </center>

        </div>
    </form>
</body>

</html>