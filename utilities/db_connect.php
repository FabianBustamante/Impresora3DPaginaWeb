<?php

$host = "localhost";  
$username = "root";   
$password = "";       
$database = "shop";   

// Crear la conexión
$conn = mysqli_connect($host, $username, $password, $database);


if (!$conn) {
    die("Error al conectar con la base de datos: " . mysqli_connect_error());
}
?>
