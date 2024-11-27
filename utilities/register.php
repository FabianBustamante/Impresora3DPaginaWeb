<?php
// Iniciar sesión
session_start();

// Incluir la conexión a la base de datos
include '../utilities/db_connect.php'; // Ajusta la ruta según tu estructura de archivos

// Inicializar las variables de mensaje de error y éxito
$error_message = "";
$success_message = "";

// Procesar el formulario de registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';

    // Validar campos vacíos
    if ($name === '' || $email === '' || $password === '' || $confirm_password === '') {
        $error_message = "Por favor, completa todos los campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "El correo electrónico no es válido.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Las contraseñas no coinciden.";
    } else {
        // Verificar si el correo ya está registrado
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = "El correo electrónico ya está registrado.";
        } else {
            // Insertar nuevo usuario en la base de datos
            $query = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sss", $name, $email, $password); // Guardar contraseña en texto plano

            if ($stmt->execute()) {
                $success_message = "Registro exitoso. ¡Ahora puedes iniciar sesión!";
                // Redirigir al login después de unos segundos (opcional)
                header("refresh:3;url=login.php");
            } else {
                $error_message = "Ocurrió un error al registrar el usuario. Por favor, intenta de nuevo.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header>
        <div class="navbar">
            <h1>Tienda de Impresiones 3D</h1>
            <nav>
                <ul>
                    <li><a href="../index.php">Inicio</a></li>
                    <li><a href="products.php">Productos</a></li>
                    <li><a href="cart.php">Carrito</a></li>
                    <li><a href="login.php">Iniciar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="register-container">
            <section class="register-form">
                <h2>Crear Cuenta</h2>

                <!-- Mensaje de error -->
                <?php if ($error_message): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>

                <!-- Mensaje de éxito -->
                <?php if ($success_message): ?>
                    <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
                <?php endif; ?>

                <form action="register.php" method="post">
                    <label for="name">Nombre Completo:</label>
                    <input type="text" id="name" name="name" required>

                    <label for="email">Correo Electrónico:</label>
                    <input type="email" id="email" name="email" required>

                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required>

                    <label for="confirm_password">Confirmar Contraseña:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>

                    <button type="submit">Registrarse</button>
                </form>

                <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a>.</p>
            </section>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Tienda de Impresiones 3D. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
