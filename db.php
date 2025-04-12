<?php
date_default_timezone_set("America/Bogota");

$host = "sql312.infinityfree.com";  // El nombre correcto del host de la base de datos
$user = "if0_38722473";            // Tu usuario de base de datos
$password = "NqqYSthpmpryPLx";     // Tu contraseña de base de datos
$db = "if0_38722473_dbgrupogs";    // El nombre exacto de tu base de datos

try{
    $conn = new PDO("mysql:host=$host;dbname=$db;",$user,$password);
}catch(PDOException $e){
    die("Fallo la conexión: ".$e->getMessage());
}
?>
