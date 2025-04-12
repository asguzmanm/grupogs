<?php
date_default_timezone_set("America/Bogota");

$host = "sql312.infinityfree.com";  // El nombre correcto del host de la base de datos
$user = "if0_38722473";            // Tu usuario de base de datos
$password = "NqqYSthpmpryPLx";     // Tu contraseña de base de datos
$db = "if0_38722473_dbgrupogs";    // El nombre exacto de tu base de datos

try{
    // Intentamos la conexión con el puerto explícitamente
    $conn = new PDO("mysql:host=$host;port=3306;dbname=$db", $user, $password);
    // Establecer el modo de error de PDO a excepción
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexión exitosa";
}catch(PDOException $e){
    die("Fallo la conexión: ".$e->getMessage());
}
?>
