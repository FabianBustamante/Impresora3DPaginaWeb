<?php

include '../utilities/db_connect.php';
session_start();

// Verificar si el admin está logueado
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php"); // Redirigir al login si no está logueado
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="../css/styles.css"> 
</head>
<body>
    <div class="admin-dashboard">
        <h1>Bienvenido, Administrador</h1>
        <nav>
            <ul>
                <li><a href="agregar_producto.php">Agregar Producto</a></li>
                <li><a href="eliminar_producto.php">Eliminar Producto</a></li>
                <li><a href="placed_orders.php">Ordenes</a></li>
                <li><a href="register_admin.php">Registrar nuevo administrador</a></li>
                <li><a href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </div>
</body>
</html>
