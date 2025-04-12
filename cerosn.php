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
    setlocale(LC_TIME, 'es_ES');
    require 'db.php';
    session_start();

    //Colsultar la información del cliente
    $cliente = $conn->prepare('SELECT name, phone, nequi, ingreso, nivel_1 FROM usuarios WHERE phone=:numero');
    $cliente->bindParam(':numero', $_SESSION['numero']);
    $cliente->execute();
    $resultsCliente = $cliente->fetch(PDO::FETCH_ASSOC);

    $nombreCliente = $resultsCliente['name'];
    $numeroCliente = $resultsCliente['phone'];
    $nequiCliente = $resultsCliente['nequi'];
    //$ingresoCliente = $resultsCliente['ingreso'];
    $numero_n_1Cliente = $resultsCliente['nivel_1'];   

    //Finaliza la consulta de la información del cliente

    //Consultar el Nombre del Nivel 1 (Quien lo refirió)
    $records = $conn->prepare('SELECT name FROM usuarios WHERE phone=:numero_n_1');
    $records->bindParam(':numero_n_1', $numero_n_1Cliente);
    $records->execute();
    $results = $records->fetch(PDO::FETCH_ASSOC);
    $nombre_n_1 = $results['name'];
    //Finaliza consultar el Nombre del Nivel 1 (Quien lo refirió)

    $numero = $_SESSION['numero'];   

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
                    <label class="nequi-numero" for="">Sin Nequi</label><br>
                </div>
            </div>

            <div class="referido">
                <img src="images/informacion.png" class="img-informacion" alt="">
                <label class="referi" for="">Referido por <?php echo $nombre_n_1 ?></label>
            </div>

            <div class="info-nequi">
                <img src="images/senal.png" class="img-nequi" alt="">
                <div style="text-align:center;" class="texto-message">
                    <label class="message-texto" for="">Registra tu número Nequi, este será el número al cual se consignarán los pagos</label>
                </div>

            </div>

            <div class="grupo">
                <label class="ingrese" for="">Ingrese su número Nequi</label><br>
                <input type="tel" name="nequi" required><br>
                <label class="ingrese" for="">Confirme su número Nequi</label><br>
                <input type="tel" name="nequi_conf" required>
            </div>

            <button name="registrar_nequi" onclick="return confirm('Clic en Aceptar para confirmar el registro de número Nequi');">Registrar Nequi</button>

            <?php


            if (isset($_POST['registrar_nequi'])) {

                if (empty($_POST['nequi']) || empty($_POST['nequi_conf'])) {
                    echo '<script language="javascript">alert("Número Nequi vacio");</script>';
                } else {

                    $nequi1 = $_POST['nequi'];
                    $nequi2 = $_POST['nequi_conf'];

                    if ($nequi1 === $nequi2) {                        

                        require  'db.php';
                        $sql = "UPDATE usuarios SET nequi=:nequi WHERE phone =:numero";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':nequi', $nequi1);
                        $stmt->bindParam(':numero', $_SESSION['numero']);

                        if ($stmt->execute()) {      
                            include 'variables.php';                            
                            confirmar_registro_nequi($nombreCliente, $numeroCliente, $nequi1, $token, $url_curl);                     
                            echo "<script>alert('!Número Nequi registrado correctamente¡');window.location= 'cerocn.php'</script>";                            
                        } else {
                            $message = 'Ocurrio un error, intenta más tarde';
                        }
                    } 
                    else{
                        echo '<script language="javascript">alert("Números de Nequi diferentes, ingrésalos nuevamente");</script>';
                    }
                }
            }

            function confirmar_registro_nequi($nombre_cliente, $numero, $nequi_cliente, $token, $url_curl)
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
                    //"to": "57' . $numero . '",
                    CURLOPT_POSTFIELDS => '{
                                                "messaging_product": "whatsapp",
                                                "to": "573108635298",
                                                "type": "template",
                                                "template": 
                                                {
                                                    "name": "d0_confirmacion_registro_nequi",
                                                    "language": 
                                                    {
                                                        "code": "es_ES",
                                                        "policy": "deterministic"
                                                    },
                                                    "components": 
                                                    [
                                                        {
                                                            "type": "body",
                                                            "parameters": 
                                                            [
                                                                {
                                                                    "type": "text",
                                                                    "text": "' . $nombre_cliente . '"
                                                                },
                                                                {
                                                                    "type": "text",
                                                                    "text": "' . $nequi_cliente . '"
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