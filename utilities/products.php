<?php
// Incluir la conexión a la base de datos
include '../utilities/db_connect.php'; // Corregir la ruta
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Tienda de Impresiones 3D</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <!-- Encabezado -->
    <header>
        <div class="navbar">
            <h1>Tienda de Impresiones 3D</h1>
            <nav>
                <ul>
                    <li><a href="../index.php">Inicio</a></li>
                    <li><a href="../utilities/cart.php">Carrito</a></li>
                    <li><a href="../utilities/login.php">Iniciar Sesión</a></li>
                    <li><a href="../utilities/register.php">Registrarse</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Sección de productos -->
    <main>
    <section class="productos-disponibles">
    <h2>Productos Disponibles</h2>
    <div class="productos">
        <?php
        // Consulta para obtener todos los productos
        $query = "SELECT * FROM products";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo '
                <div class="producto">
                    <img src="../admin/uploaded_img/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '">
                    <h3>' . htmlspecialchars($row['name']) . '</h3>
                    <p>$' . number_format($row['price'], 2) . '</p>
                    <a href="quick_view.php?id=' . $row['id'] . '" class="btn">Ver más</a>
                </div>';
            }
        } else {
            echo '<p>No hay productos disponibles.</p>';
        }
        ?>
    </div>
</section>

    </main>

    <!-- Pie de página -->
    <footer>
        <p>&copy; 2024 Tienda de Impresiones 3D. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
