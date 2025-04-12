<?php
date_default_timezone_set("America/Bogota");
$server="localhost";
$user="root";
$pass="";
$db="inversionesg";

// $server="sql102.epizy.com";
// $user="epiz_31797071";
// $pass="3pQNZGhT8j67XRG";
// $db="epiz_31797071_inversionesg";

try{
    $conn = new PDO("mysql:host=$server;dbname=$db;",$user,$pass);
}catch(PDOException $e){
    die("Fallo la conexión: ".$e->getMessage());
}
?>