<?php
date_default_timezone_set("America/Bogota");
//$server="localhost";
//$user="root";
//$pass="";
//$db="inversionesg";

$server="sql312.infinityfree.com";
$user="if0_38722473";
$pass="NqqYSthpmpryPLx";
$db="if0_38722473_dbgrupogs";

try{
    $conn = new PDO("mysql:host=$server;dbname=$db;",$user,$pass);
}catch(PDOException $e){
    die("Fallo la conexión: ".$e->getMessage());
}
?>
