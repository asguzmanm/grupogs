<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguimiento</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,500,700&display=swap" rel="stylesheet">


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-treeview/1.2.0/bootstrap-treeview.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-treeview/1.2.0/bootstrap-treeview.min.css" />
    <link rel="stylesheet" href="/styles/seguimiento.css">
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

    //Consultar ganancias

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

    //Mostrar Tabla numero referidos

    $records = $conn->prepare('SELECT phone, name FROM usuarios WHERE phone=:numero');
    $records->bindParam(':numero', $number);
    $records->execute();
    $results = $records->fetch(PDO::FETCH_ASSOC);

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
                    <label style="color:white; margin-bottom: 0px; font-weight: 400; font-size: 16px" for="">Hola</label><br>
                    <label style="color:white; margin-bottom: 0px; font-size: 20px" for=""><?php echo $nombreCliente ?></label><br>
                    <label style="color:white; margin-bottom: 0px; font-weight: 400; font-size: 14px" for="">Cel. <?php echo $numeroCliente ?></label>
                </div>
            </div>

            <div class="informacion">
                <div class="dias">
                    <label style="color:black; margin-bottom: 0px; font-weight: bold; font-size: 16px" for="">Es tu día</label><br>
                    <label style="color:black; margin-bottom: 0px; font-weight: bold; font-size: 48px" for=""><?php echo $_SESSION['dias'] ?></label><br>
                </div>
                <div class="nequi">
                    <label style="color:black; margin-bottom: 0px; font-weight: bold; font-size: 16px" for="">Nequi</label>
                    <p>&nbsp</p>
                    <label style="color:black; margin-bottom: 0px; font-weight: bold; font-size: 18px" for=""><?php echo $nequiCliente ?></label><br>
                    <a style="font-size: 13px; color: #B06AB3; text-decoration:underline" href='act_nequi.php?.<?php echo codifica_urlget('previa=seguimiento.php&numero=' . $numero) ?>'>Actualizar Nequi</a>
                </div>
            </div>

            <div class="referido">
                <img src="images/informacion.png" class="img-informacion" alt="">
                <label style="color:black; margin-bottom: 0px; font-weight: 400; font-size: 14px" for="">Referido por <?php echo $nombre_n_1 ?></label>
            </div>

            <div class="estado-cuenta">

                <img src="images/recibo.png" class="img-consignacion" alt="">
                <div>
                    <label style="font-weight: bold; font-size: 18px;">Estado de cuenta</label><br>                    
                </div>

            </div>

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
                <br>


            <div class="pay-made-no">
                <img src="images/equipo.png" class="img-pay-made-time" alt="">
                <div>
                    <label class="ganancia-texto" style="font-weight: bold; font-size: 18px;" for="">Tu equipo</label>
                </div>
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

            //Cantidad de referidos
            require 'db.php';

            $cant_referidos = $conn->prepare('SELECT COUNT(*) AS cantidad FROM usuarios WHERE nivel_1=:numero');
            $cant_referidos->bindParam(':numero', $_SESSION['numero']);
            $cant_referidos->execute();
            $resultsreferidos = $cant_referidos->fetch(PDO::FETCH_ASSOC);
            $numeroreferidos = $resultsreferidos['cantidad'];

            //Consultar 1er Referido
            $refe_1 = $conn->prepare('SELECT name, phone FROM usuarios WHERE nivel_1=:numero ORDER BY phone ASC');
            $refe_1->bindParam(':numero', $_SESSION['numero']);
            $refe_1->execute();
            $referido_1 = $refe_1->fetch(PDO::FETCH_ASSOC);
            $nombre_refer_1 = $referido_1['name'];
            $numero_refer_1 = $referido_1['phone'];


            //Consultar 2do Referido
            $refe_2 = $conn->prepare('SELECT name, phone FROM usuarios WHERE nivel_1=:numero ORDER BY phone DESC');
            $refe_2->bindParam(':numero', $_SESSION['numero']);
            $refe_2->execute();
            $referido_2 = $refe_2->fetch(PDO::FETCH_ASSOC);
            $nombre_refer_2 = $referido_2['name'];
            $numero_refer_2 = $referido_2['phone'];

            if ($numeroreferidos === '1') { ?>

                <center><label style="text-align: center; font-size:16px" for="">Referidos ingresados</label></center>

                <div class="table-referidos">
                    <table class="table table-striped" style="text-align:center; width: 315px; align-items:center; margin-top: 5px; padding:0px;">
                        <thead style="background: linear-gradient(to right, #34396e, #1f5c9c); color: white;">
                            <th style="text-align:center; vertical-align:middle; align-items:center; padding:5px; border-start-start-radius:5px;">Referidos</th>
                            <th style="text-align:center; vertical-align:middle; align-items:center; padding:5px;"><?php echo $nombre_refer_1 ?></th>
                        </thead>
                        <tbody>

                            <?php

                            $i = 1;
                            while ($i <= 8) {
                                require 'db.php';

                                $jornada = $i . "a Jornada";
                                $nivel = "Nivel_" . $i;
                                $referidos_1 = $conn->prepare("SELECT COUNT(*) AS cantidad FROM usuarios WHERE " . $nivel . "=:numero");
                                $referidos_1->bindParam(':numero', $numero_refer_1);
                                $referidos_1->execute();
                                $resultsreferidos_1 = $referidos_1->fetch(PDO::FETCH_ASSOC);

                                $referidos_2 = $conn->prepare("SELECT COUNT(*) AS cantidad FROM usuarios WHERE " . $nivel . "=:numero");
                                $referidos_2->bindParam(':numero', $numero_refer_2);
                                $referidos_2->execute();
                                $resultsreferidos_2 = $referidos_2->fetch(PDO::FETCH_ASSOC);
                            ?>
                                <tr>
                                    <td style="text-align:center; width: 80px; padding:1px;"><?php echo $jornada ?></td>
                                    <td style="color:black; margin: 0px; padding:1px;"><?php echo $resultsreferidos_1['cantidad'] ?></td>
                                </tr>
                            <?php $i++;
                            }

                            ?>
                        </tbody>
                    </table>
                </div><br>

            <?php } elseif ($numeroreferidos === '2') {

            ?>
                <center><label style="text-align: center; font-size:16px" for="">Referidos ingresados</label></center>

                <div class="table-referidos">
                    <table class="table table-striped" style="text-align:center; width: 315px; align-items:center; margin-top: 5px; padding:0px;">
                        <thead style="background: linear-gradient(to right, #34396e, #1f5c9c); color: white;">
                            <th style="text-align:center; vertical-align:middle; align-items:center; padding:5px; border-start-start-radius:5px;">Referidos</th>
                            <th style="text-align:center; vertical-align:middle; align-items:center; padding:5px;"><?php echo $nombre_refer_1 ?></th>
                            <th style="text-align:center; vertical-align:middle; align-items:center; padding:5px; border-start-end-radius:5px;"><?php echo $nombre_refer_2 ?></th>
                        </thead>
                        <tbody>

                            <?php

                            $i = 1;
                            while ($i <= 8) {
                                require 'db.php';

                                $jornada = $i . "a Jornada";
                                $nivel = "Nivel_" . $i;
                                $referidos_1 = $conn->prepare("SELECT COUNT(*) AS cantidad FROM usuarios WHERE " . $nivel . "=:numero");
                                $referidos_1->bindParam(':numero', $numero_refer_1);
                                $referidos_1->execute();
                                $resultsreferidos_1 = $referidos_1->fetch(PDO::FETCH_ASSOC);

                                $referidos_2 = $conn->prepare("SELECT COUNT(*) AS cantidad FROM usuarios WHERE " . $nivel . "=:numero");
                                $referidos_2->bindParam(':numero', $numero_refer_2);
                                $referidos_2->execute();
                                $resultsreferidos_2 = $referidos_2->fetch(PDO::FETCH_ASSOC);
                            ?>
                                <tr>
                                    <td style="text-align:center; width: 80px; padding:1px;"><?php echo $jornada ?></td>
                                    <td style="color:black; margin: 0px; padding:1px;"><?php echo $resultsreferidos_1['cantidad'] ?></td>
                                    <td style="color:black; margin: 0px; padding:1px;"><?php echo $resultsreferidos_2['cantidad'] ?></td>
                                </tr>
                            <?php $i++;
                            }

                            ?>
                        </tbody>
                    </table>
                </div><br>

            <?php } ?>

            <center><label style="text-align: center; font-size:16px" for="">Diagrama de referidos</label></center>

            <?php

            //Mostrar Arbol de Referidos

            echo "<script>

                    $(document).ready(function() {
                        
                        var valor = '" . $numero . "';

                        $.ajax({
                            url: 'fetch.php',
                            method: 'POST',
                            data:{'numero':valor},
                            dataType: 'json',
                            success: function(data) {
                                $('#treeview').treeview({
                                    data: data                                    
                                });
                            }
                        });
                    });
                </script>"

            ?>

            <div class="tree">
                <div id="treeview"></div>
            </div>

            <center><a style="font-size: 14px; color: #B06AB3; " href="logout.php">Cerrar Sesión</a>
                <p>&nbsp</p>
            </center>

        </div>
    </form>
</body>

</html>