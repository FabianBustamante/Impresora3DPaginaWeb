<?php
// Incluir la conexión a la base de datos
include 'utilities/db_connect.php';

// Variables para mostrar mensajes de error o éxito al iniciar sesión
$error = '';
$success = '';

// Comprobar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_username'], $_POST['admin_password'])) {
    $admin_username = mysqli_real_escape_string($conn, $_POST['admin_username']);
    $admin_password = mysqli_real_escape_string($conn, $_POST['admin_password']);
    
    // Consultar si el administrador existe en la base de datos
    $query = "SELECT * FROM admins WHERE username = '$admin_username'";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $admin = mysqli_fetch_assoc($result);
        
        // Verificar la contraseña
        if (password_verify($admin_password, $admin['password'])) {
            // Iniciar sesión si la contraseña es correcta
            session_start();
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: admin/admin_dashboard.php'); // Redirigir al panel de administración
            exit;
        } else {
            $error = 'Contraseña incorrecta';
        }
    } else {
        $error = 'Usuario no encontrado';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda de Impresiones 3D</title>
    <link rel="stylesheet" href="css/styles.css"> <!-- Ruta a tus estilos -->
</head>
<body>
    <!-- Encabezado -->
    <header>
        <div class="navbar">
            <h1>Tienda de Impresiones 3D</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="utilities/products.php">Productos</a></li>
                    <li><a href="utilities/cart.php">Carrito</a></li>
                    <li><a href="utilities/login.php">Iniciar Sesión</a></li>
                    <li><a href="utilities/register.php">Registrarse</a></li>
                    <li><a href="utilities/contact.php">Contacto</a></li>
                    <li><a href="admin/admin_login.php">Iniciar Sesión Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Sección principal -->
    <main>
        <section class="hero">
            <h2>Bienvenido a nuestra Tienda de Impresiones 3D</h2>
            <p>Explora una amplia variedad de productos personalizados creados con la última tecnología de impresión 3D.</p>
        </section>

        <section class="productos-destacados">
            <h2>Productos Destacados</h2>
            <div class="productos">
                <?php
                // Consulta para obtener productos destacados
                $query = "SELECT * FROM products LIMIT 4";
                $result = mysqli_query($conn, $query);

                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '
                        <div class="producto">
                            <img src="admin/uploaded_img/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '">
                            <h3>' . htmlspecialchars($row['name']) . '</h3>
                            <p>$' . number_format($row['price'], 2) . '</p>
                            <a href="utilities/quick_view.php?id=' . $row['id'] . '" class="btn">Ver más</a>
                        </div>';
                    }
                } else {
                    echo '<p>No hay productos destacados disponibles.</p>';
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
