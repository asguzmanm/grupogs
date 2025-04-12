<?php
date_default_timezone_set("America/Bogota");
@session_start();
session_destroy();
header("Location: index.php");
?>