<?php
date_default_timezone_set("America/Bogota");
$url = $_SERVER["REQUEST_URI"];
decodifica_urlget($url);
$numero = $_GET['numerofrom'];
$pagina_previa = $_GET['previa'];
$base = $_GET['basedatos'];

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

require  'db.php';
$request = "select dateconfirm, evidencia from " . $base . " where phone=$numero";
$select_image = $conn->prepare($request);
$select_image->execute();
$resultado_img = $select_image->fetch(PDO::FETCH_ASSOC);
$dateconfirm = $resultado_img['dateconfirm'];
$image = $resultado_img['evidencia'];

$ruta="images/evidencias/".$image;

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Pago</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,500,700&display=swap" rel="stylesheet">

</head>

<body>
    <center><a style="font-size: 14px; color: #B06AB3; font-family:'Nunito'; font-weight: bold;" href="" onClick="window.close();">Cerrar esta ventana</a></center><br>

    <div>
        <img width="340" src=<?php echo $ruta?> alt="Evidencia">
        
    </div>
</body>

</html>