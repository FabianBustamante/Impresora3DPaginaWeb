<?php

include '../utilities/db_connect.php';

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
        
        // Comparar la contraseña (sin hash) directamente con la contraseña en la base de datos
        if ($admin_password === $admin['password']) {
            // Iniciar sesión si la contraseña es correcta
            session_start();
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: admin_dashboard.php'); // Redirigir al panel de administración
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
    <title>Iniciar Sesión - Admin</title>
    <link rel="stylesheet" href="../css/styles.css"> <!-- Ruta a tus estilos -->
</head>
<body>
    <header>
        <div class="navbar">
            <h1>Tienda de Impresiones 3D - Iniciar sesión Admin</h1>
            <nav>
                <ul>
                    <li><a href="../index.php">Inicio</a></li>
                    <li><a href="../utilities/products.php">Productos</a></li>
                    <li><a href="../cart.php">Carrito</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="login-section">
            <h2>Iniciar sesión como Admin</h2>

            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="admin_login.php" method="POST">
                <label for="admin_username">Usuario:</label>
                <input type="text" id="admin_username" name="admin_username" required>
                <br>

                <label for="admin_password">Contraseña:</label>
                <input type="password" id="admin_password" name="admin_password" required>
                <br>

                <button type="submit">Iniciar sesión</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Tienda de Impresiones 3D. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
