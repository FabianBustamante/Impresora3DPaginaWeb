<?php
// Incluir la conexión a la base de datos
include '../utilities/db_connect.php';
session_start();

// Verificar si el admin está logueado
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php"); // Redirigir al login si no está logueado
    exit();
}

// Verificar si se ha recibido un ID de producto para eliminar
if (isset($_GET['id'])) {
    $id_producto = $_GET['id'];

    // Escapar el ID para evitar inyecciones SQL
    $id_producto = mysqli_real_escape_string($conn, $id_producto);

    // Primero, obtener el nombre de la imagen asociada al producto para eliminarla del servidor
    $query = "SELECT image FROM products WHERE id = '$id_producto'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $imagen = $row['image'];

        // Eliminar el producto de la base de datos
        $delete_query = "DELETE FROM products WHERE id = '$id_producto'";
        if (mysqli_query($conn, $delete_query)) {
            // Si se eliminó correctamente el producto, eliminar la imagen del servidor
            if (file_exists('uploaded_img/' . $imagen)) {
                unlink('uploaded_img/' . $imagen);
            }
            echo "<p class='success'>Producto eliminado correctamente.</p>";
        } else {
            echo "<p class='error'>Error al eliminar el producto: " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<p class='error'>Producto no encontrado.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Producto</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="eliminar-producto-container">
        <h2>Eliminar Producto</h2>

        <?php
        // Obtener todos los productos para mostrar en una lista
        $query = "SELECT * FROM products";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            echo "<ul>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<li>";
                echo htmlspecialchars($row['name']) . " - $". number_format($row['price'], 2);
                echo " <a href='eliminar_producto.php?id=" . $row['id'] . "' class='eliminar-btn'>Eliminar</a>";
                echo "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No hay productos disponibles para eliminar.</p>";
        }
        ?>
        <br>
        <a href="admin_dashboard.php" class="btn-regresar">Regresar</a>
    </div>
</body>
</html>
