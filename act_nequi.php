<?php
date_default_timezone_set("America/Bogota");
$url = $_SERVER["REQUEST_URI"];
decodifica_urlget($url);
$numero = $_GET['numero'];
$pagina_previa = $_GET['previa'];

function decodifica_urlget(string $cadena): string
{
    $arraycad = explode("?", $cadena); // Creo array a partir de la URL, separándolo por "?"
    
    if (isset($arraycad[1])) {
        $cadena = $arraycad[1]; // Utilizo el segundo elemento del array (si existe)
        $cadena = base64_decode($cadena); // Decodifico

        $control = "Esteman106Control";
        $cadena = str_replace($control, "", $cadena); // Remuevo la frase de control

        $cadena_get = explode("&", $cadena);
        foreach ($cadena_get as $value) {
            $val_gets = explode("=", $value);
            $_GET[$val_gets[0]] = $val_gets[1];
        }

        return $cadena;
    } else {
        // Manejar el caso en el que no hay parámetros en la URL
        return '';
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Nequi</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,500,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/index.css">
</head>

<body>
    <form action="" method="POST" id="form">

        <div class="contenedor">
            <div class="header">
                <img src="images/logo_blanco.png" class="logo-index" alt="logo">
            </div>
            <h1>Actualizar Nequi</h1>
            <div class="grupo">
                <input type="tel" name="nuevo_nequi"><span class="barra" required></span>
                <label for="">Ingresar nuevo Nequi</label>
            </div>

            <div class="grupo">
                <input type="tel" name="conf_nuevo_nequi"><span class="barra" required></span>
                <label for="">Confirmar nuevo Nequi</label>
            </div>

            <button name="boton_nuevo_nequi" onclick="return confirm('Clic en Aceptar para confirmar la actualización de Nequi');">Actualizar</button>
            <button name="boton_cancelar">Regresar</button>

            <?php

            if (isset($_POST['boton_nuevo_nequi'])) {

                if (empty($_POST['nuevo_nequi']) || empty($_POST['conf_nuevo_nequi'])) {
                    echo '<script language="javascript">alert("Campo Nequi vacio");</script>';
                } else {

                    if ($_POST['nuevo_nequi'] === $_POST['conf_nuevo_nequi']) {

                        require  'db.php';
                        $sql = "UPDATE usuarios SET nequi=:nuevo_nequi WHERE phone =:numero";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':nuevo_nequi', $_POST['nuevo_nequi']);
                        $stmt->bindParam(':numero', $numero);

                        if ($stmt->execute()) {
                            echo "<script>alert('Número de Nequi actualizado correctamente');window.location= '$pagina_previa'</script>"; 
                            $message = 'Número de Nequi actualizado correctamente';
                            //header("location: " . $pagina_previa);
                        } else {
                            $message = 'Ocurrio un error, intenta más tarde';
                        }
                    } else {
                        echo '<script language="javascript">alert("Números de Nequi diferentes, ingrésalos nuevamente");</script>';
                    }
                }
            }

            if (isset($_POST['boton_cancelar'])) {
                header("location: " . $pagina_previa);
            }
            ?>
            <p class="warnings" align="justify"><?php print $message ?></p><b>&nbsp</b>
        </div>

    </form>
</body>

</html>