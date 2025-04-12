<?php

date_default_timezone_set("America/Bogota");

require 'db.php';

if (!empty($_POST['numero']) && !empty($_POST['pin'])) {
    $records = $conn->prepare('SELECT phone, name, pin, nequi, nivel_1, ingreso FROM usuarios WHERE phone=:numero');
    $records->bindParam(':numero', $_POST['numero']);
    $records->execute();
    $results = $records->fetch(PDO::FETCH_ASSOC);
    $message = '';

    if (count($results) > 0 && $_POST['pin'] === $results['pin']) {
        session_start();
        $_SESSION['numero'] = $results['phone']; //Enviar Celular        

        $ingreso = date("Y-m-d", strtotime($results['ingreso']));
        
        include "variables.php";
        $hoy = $hoy_v; //Fecha actual

        $actual =  date("Y-m-d", strtotime($hoy));
        $days = (abs(strtotime($ingreso) - strtotime($actual))) / 86400; //Dias

        $_SESSION['dias'] = $days; //Enviar el # de días

        //Prueba Ajuste Días

        $dia_ingreso = date("D", strtotime($results['ingreso']));
        $dia_hoy = date("D", strtotime($hoy));

        switch ($dia_ingreso) {

            case 'Thu':

                //Dia Cero (agregar Nequi)
                if ($days === 0) {

                    $sin_nequi = intval('0');

                    if ($results['nequi'] != $sin_nequi) {
                        header("location: cerocn.php");
                    } else {
                        header("location: cerosn.php");
                    }
                }

                //Dia 1 (Realizar pago) y día 2
                elseif ($days === 1 || $days === 2) {
                    header("location: uno.php");
                }

                //Día 3 (Ingreso referidos)
                elseif ($days === 3 && $dia_hoy === 'Sun') {
                    header("location: uno.php");
                } elseif ($days === 4 && $dia_hoy === 'Mon') {
                    header("location: referidos.php");
                }

                //Día 11 (1er pago y devolución del 50%)
                elseif ($days === 11 && $dia_hoy === 'Mon') {
                    header("location: seguimiento.php"); //Pendiente que mostrar;
                } elseif ($days === 12 && $dia_hoy === 'Tue') {
                    header("location: once.php");
                } elseif ($days === 13) {
                    header("location: once.php");
                }

                //Día 22 (2o pago y devolución del 50%)
                elseif ($days === 22 || $days === 23) {
                    header("location: veintidos.php");
                }

                //Día 33 (3r pago y pago del 20% Admin)
                elseif ($days === 32 && $dia_hoy === 'Mon') {
                    header("location: seguimiento.php"); //Pendiente que mostrar; header("location: treintaytres.php");
                } elseif ($days === 33 || $days === 34 || $days === 35 || $days === 36) {
                    header("location: treintaytres.php");
                } else {
                    header("location: seguimiento.php");
                }

                break;

            case 'Mon':

                //Dia Cero (agregar Nequi)
                if ($days === 0) {

                    $sin_nequi = intval('0');

                    if ($results['nequi'] != $sin_nequi) {
                        header("location: cerocn.php");
                    } else {
                        header("location: cerosn.php");
                    }
                }

                //Dia 1 (Realizar pago), se incluye 2 pero si no ha pagado está eliminado de la BD por lo que no
                //tendra habilitada la opción de pagar
                elseif ($days === 1 || $days === 2) {
                    header("location: uno.php");
                }

                //Día 3 (Ingreso referidos)
                elseif ($days === 3) {
                    header("location: referidos.php");
                }

                //Día 11 (1er pago y devolución del 50%)
                elseif ($days === 11 || $days === 12) {
                    header("location: once.php");
                }

                //Día 22 (2o pago y devolución del 50%)
                elseif ($days === 22 || $days === 23) {
                    header("location: veintidos.php");
                }

                //Día 33 (3r pago y pago del 20% Admin)
                elseif ($days === 32 && $dia_hoy === 'Fri') {
                    header("location: treintaytres.php");
                } elseif ($days === 33 || $days === 34 || $days === 35 || $days === 36)  {
                    header("location: treintaytres.php");
                } else {
                    header("location: seguimiento.php");
                }
                break;
        }
    } else {
        $message = 'Celular o PIN incorrecto';
    }
} else {
    //echo '<script language="javascript">alert("Campo vacio");</script>';
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grupo GS SAT</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,500,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/styles/index.css">
</head>

<body>
    <form action="index.php" method="POST" id="form">

        <div class="contenedor">
            <div class="header">
                <img src="images/logo_blanco.png" class="logo-index" alt="logo">
            </div>
            <h1>Ingreso</h1>
            <div class="grupo">
                <input type="tel" name="numero" required><span class="barra"></span>
                <label for="">Celular</label>
            </div>

            <div class="grupo">
                <input type="password" name="pin" required><span class="barra"></span>
                <label for="">PIN</label>
            </div>

            <button type="submit">Ingresar</button>
            <p>&nbsp</p>

            <center><a style="font-size: 14px; color: #B06AB3; " href="recuperarpin.php">¿Olvide mi PIN?</a>
                <p>&nbsp</p>
            </center>

            <center><a style="font-size: 14px; color: #B06AB3; " href="politica.php">Política de Privacidad</a>
                <p>&nbsp</p>
            </center>            

            <p class="warnings" align="justify"><?php print $message ?></p>&nbsp</p>
        </div>

    </form>
</body>

</html>