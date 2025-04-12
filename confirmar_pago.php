<?php
date_default_timezone_set("America/Bogota");
$url = $_SERVER["REQUEST_URI"];
decodifica_urlget($url);
$numero = $_GET['numero'];
$pagina_previa = $_GET['previa'];
$basep = $_GET['basedatosp'];
$baser = $_GET['basedatosr'];

//Campos para Whastapp
$nombre_orig = $_GET['nombre_orig'];
$nombre = $_GET['nombre_cliente'];
$cel = $_GET['cel_cliente'];
$valor = number_format($_GET['valor'], 0);
$motivo = $_GET['motivo'];
$pin_orig = $_GET['pin_orig'];

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

$fecha = date('Y-m-d H:i:s');
$sql = "update " . $basep . " set confirmado='Si', daterecibe='" . $fecha . "' where phone=" . $numero;
$stmt = $conn->prepare($sql);

if ($stmt->execute()) {
    echo "<script>alert('Pago Confirmado');window.location= '$pagina_previa'</script>";
    include 'variables.php';
    confirmar_pago($nombre_orig, $nombre, $cel, $valor, $motivo, $pagina, $numero, $pin_orig, $youtube_pago_confirmado, $token, $url_curl);
} else {
    $message = 'Ocurrio un error, intenta más tarde';
}

function confirmar_pago($nombre_orig, $nombre, $cel, $valor, $motivo, $pagina, $numero, $pin_orig, $video, $token, $url_curl)
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
                                "template": {
                                    "name": "dm_pago_confirmado",
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
                                                    "text": "' . $nombre_orig . '"
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
                                                    "text": "' . $numero . '"
                                                },
                                                {
                                                    "type": "text",
                                                    "text": "' . $pin_orig . '"
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
