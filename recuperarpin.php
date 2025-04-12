<?php
date_default_timezone_set("America/Bogota");
//$token = "EAAJYv68NTfoBABcQQfP1Pk64MpRkqZApDEovQV3yvKY66tZALJTjAIBo7xapTXfZBHO3BHnZBN220DUtcYH3637NWOTZBawRESp3WrFY4ulZAshXFdjkOOGTC6WVsTdy84ZBhGZARrXWHvX6wUFyVXqqij7jr9awOFJZB3RPG5u48ZCoWRpDjChWCNTZCyac03aXdleMOLZAs9BAL52vTXlvnehx";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar PIN</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,500,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/index.css">
</head>

<body>
    <form action="" method="POST" id="form">

        <div class="contenedor">
            <div class="header">
                <img src="images/logo_blanco.png" class="logo-index" alt="logo">
            </div>
            <h1>Recuperar PIN</h1>
            <div class="grupo">
                <input type="tel" name="celular"><span class="barra" required></span>
                <label for="">Ingresa tu Celular</label>
            </div>

            <div class="grupo">
                <input type="tel" name="conf_celular"><span class="barra" required></span>
                <label for="">Confirmar tu Celular</label>
            </div>

            <button name="boton_recuperar" onclick="return confirm('Clic en Aceptar para confirmar la recuperación del PIN');">Recuperar PIN</button>
            <button name="boton_cancelar">Regresar</button>

            <?php

            if (isset($_POST['boton_recuperar'])) {

                if (empty($_POST['celular']) || empty($_POST['conf_celular'])) {
                    echo '<script language="javascript">alert("Campo Celular vacio");</script>';
                } else {

                    if ($_POST['celular'] === $_POST['conf_celular']) {

                        require  'db.php';

                        $personas = $conn->prepare('SELECT COUNT(*) AS cantidad FROM usuarios WHERE phone=:numero');
                        $personas->bindParam(':numero', $_POST['celular']);
                        $personas->execute();
                        $resultspersonas = $personas->fetch(PDO::FETCH_ASSOC);
                        $numeropersonas = $resultspersonas['cantidad'];

                        if ($numeropersonas === '0') {
                            echo "<script>alert('Usuario no encontrado, intenta nuevamente');</script>";
                        } else {
                            $cliente = $conn->prepare('SELECT name, phone, pin FROM usuarios WHERE phone=:numero');
                            $cliente->bindParam(':numero', $_POST['celular']);
                            $cliente->execute();
                            $resultsCliente = $cliente->fetch(PDO::FETCH_ASSOC);
                            $nombreCliente = $resultsCliente['name'];
                            $numeroCliente = $resultsCliente['phone'];
                            $pinCliente = $resultsCliente['pin'];

                            if ($cliente->execute()) {
                                include 'variables.php';
                                recuperar_pin($nombreCliente, $numeroCliente, $pinCliente, $token, $url_curl);
                                echo "<script>alert('Se ha enviado un mensaje a Whatsapp con tu PIN');window.location= 'index.php'</script>";
                            } else {
                                $message = 'Ocurrio un error, intenta más tarde';
                            }
                        }
                    } else {
                        echo '<script language="javascript">alert("Números de celular diferentes, intenta nuevamente");</script>';
                    }
                }
            }

            if (isset($_POST['boton_cancelar'])) {
                header("location: index.php");
            }
            ?>

            <?php

            function recuperar_pin($nombre, $celular, $PIN, $token, $url_curl)
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
                                                "name": "olvido",
                                                "language": {
                                                    "code": "es_MX",
                                                    "policy": "deterministic"
                                                },
                                                "components": [
                                                    {
                                                        "type": "body",
                                                        "parameters": [
                                                            {
                                                                "type": "text",
                                                                "text": "' . $nombre . '"
                                                            },                                                            
                                                            {
                                                                "type": "text",
                                                                "text": "' . $celular . '"
                                                            },
                                                            {
                                                                "type": "text",
                                                                "text": "' . $PIN . '"
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
        </div>

    </form>
</body>

</html>