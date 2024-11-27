<?php
// Incluir la conexión a la base de datos
include '../utilities/db_connect.php';
session_start();

// Verificar si el admin está logueado
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php"); // Redirigir al login si no está logueado
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $nombre = mysqli_real_escape_string($conn, $_POST['nombre']);
    $descripcion = mysqli_real_escape_string($conn, $_POST['descripcion']);
    $precio = mysqli_real_escape_string($conn, $_POST['precio']);
    
    // Subir la imagen
    $imagen = $_FILES['imagen']['name'];
    $imagen_tmp = $_FILES['imagen']['tmp_name'];
    $imagen_folder = 'uploaded_img/' . $imagen;
    
    // Mover la imagen al directorio de destino
    if (move_uploaded_file($imagen_tmp, $imagen_folder)) {
        // Insertar el producto en la base de datos
        $query = "INSERT INTO products (name, description, price, image) VALUES ('$nombre', '$descripcion', '$precio', '$imagen')";
        if (mysqli_query($conn, $query)) {
            echo "Producto agregado correctamente.";
        } else {
            echo "Error al agregar el producto: " . mysqli_error($conn);
        }
    } else {
        echo "Error al subir la imagen.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <!-- Contenedor específico para la página agregar_producto -->
    <div class="agregar-producto">
        <h2>Agregar Nuevo Producto</h2>
        <form action="agregar_producto.php" method="POST" enctype="multipart/form-data">
            <label for="nombre">Nombre del producto:</label>
            <input type="text" name="nombre" required><br>

            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" required></textarea><br>

            <label for="precio">Precio:</label>
            <input type="text" name="precio" required><br>

            <label for="imagen">Imagen:</label>
            <input type="file" name="imagen" required><br>

            <input type="submit" value="Agregar Producto">
        </form>

        <!-- Botón "Regresar" -->
        <br>
        <a href="admin_dashboard.php" class="btn-regresar">Regresar</a>
    </div>
</body>
</html>
