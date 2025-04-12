<?php
date_default_timezone_set("America/Bogota");

// Reemplaza estos valores con las credenciales proporcionadas por Render
$host = "dpg-cvsvmp49c44c73c7t0cg-a";          // El host de la base de datos de Render
$user = "dbgrupogs_user";       // El usuario de la base de datos de Render
$password = "SqNttbLSMe7k32yZZ2BElw6O5Nz2TDo1";// La contraseña de la base de datos de Render
$db = "dbgrupogs";   // El nombre de la base de datos en Render

try {
    // Intentamos la conexión con PostgreSQL
    $conn = new PDO("pgsql:host=$host;dbname=$db", $user, $password);
    // Establecer el modo de error de PDO a excepción
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Conexión exitosa";
} catch (PDOException $e) {
    die("Fallo la conexión: " . $e->getMessage());
}
?>

