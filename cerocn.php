<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido</title>
    <link rel="stylesheet" href="/styles/cero.css">
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

    //Consultar el Nombre del Nivel 1 (por quien fue referido)
    $records = $conn->prepare('SELECT name FROM usuarios WHERE phone=:numero_n_1');
    $records->bindParam(':numero_n_1', $numero_n_1Cliente);
    $records->execute();
    $results = $records->fetch(PDO::FETCH_ASSOC);
    $nombre_n_1 = $results['name'];
    //Finaliza consultar el Nombre del Nivel 1 

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
                    <a style="font-size: 13px; color: #B06AB3; " href='act_nequi.php?.<?php echo codifica_urlget('previa=cerocn.php&numero=' . $numero) ?>'>Actualizar Nequi</a>
                </div>
            </div>

            <div class="referido">
                <img src="images/informacion.png" class="img-informacion" alt="">
                <label class="referi" for="">Referido por <?php echo $nombre_n_1 ?></label>
            </div>

            <div class="info-msg">
                <img src="images/pago_1.png" class="img-cero" alt="">
                <div>
                    <label class="message-texto" for="">Mañana te indicaremos a quién debes realizar el único pago de $20.000</label><br>
                </div>
            </div>

            <div class="info-msg2">
                <img src="images/referidos.png" class="img-cero" alt="">
                <div>
                    <label class="message-texto" for="">Igualmente, prepara a tus dos referidos <br>para agregarlos en tres días</label><br>
                </div>
            </div>

            <center><a style="font-size: 14px; color: #B06AB3; " href="logout.php">Cerrar Sesión</a>
                <p>&nbsp</p>
            </center>

    </form>
</body>

</html>