<?php
session_start();

// Incluir la conexión a la base de datos
include '../utilities/db_connect.php';

// Manejo del registro
if (isset($_POST['register'])) {
    // Obtener los valores del formulario
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validación básica
    if (empty($username) || empty($password)) {
        $error = "Por favor ingrese todos los campos.";
    } else {
        // Verificar si el nombre de usuario ya existe
        $query = "SELECT * FROM admins WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "El nombre de usuario ya está registrado.";
        } else {
            // Insertar el nuevo administrador en la base de datos sin cifrar la contraseña
            $insert_query = "INSERT INTO admins (username, password) VALUES (?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("ss", $username, $password);

            if ($stmt->execute()) {
                $success = "Administrador registrado con éxito.";
            } else {
                $error = "Hubo un problema al registrar el administrador.";
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
    <link rel="stylesheet" href="../css/styles.css">
    <title>Registrar Administrador</title>
</head>
<body>
    <header>
        <h1>Registrar Administrador</h1>
    </header>
    <main>
        <div class="register-admin-container">
            <form method="post">
                <input type="text" name="username" placeholder="Nombre de Usuario" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <button type="submit" name="register">Registrar</button>
                <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
                <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            </form>
            <!-- Botón regresar -->
            <a href="admin_dashboard.php" class="btn-regresar">Regresar</a>
        </div>
    </main>
</body>
</html>
