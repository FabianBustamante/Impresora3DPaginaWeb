<?php
// Iniciar la sesión
session_start();

// Incluir la conexión a la base de datos
include '../utilities/db_connect.php'; // Corregir la ruta según tu estructura de archivos

// Verificar si el ID del pedido se pasa en la URL (esto sería después de completar el pago)
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;

// Verificar si el pedido existe
$order_details = null;
$order_items = [];

if ($order_id) {
    // Buscar el pedido en la base de datos
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $order_details = $result->fetch_assoc(); // Almacenar detalles del pedido
    } else {
        // Si no se encuentra el pedido, asignamos un mensaje de error
        $error_message = "No se encontró el pedido con ID $order_id.";
    }

    // Obtener los productos asociados al pedido si se encontró el pedido
    if ($order_details) {
        $stmt = $conn->prepare("SELECT oi.*, p.name, p.price FROM order_items oi
                                JOIN products p ON oi.product_id = p.id
                                WHERE oi.order_id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $order_items[] = $row;
        }
    }
} else {
    $error_message = "No se proporcionó un ID de pedido válido.";
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Exitoso</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header>
        <div class="navbar">
            <h1>Tienda de Impresiones 3D</h1>
            <nav>
                <ul>
                    <li><a href="../index.php">Inicio</a></li>
                    <li><a href="../utilities/products.php">Productos</a></li>
                    <li><a href="../utilities/cart.php">Carrito</a></li>
                    <li><a href="../utilities/login.php">Iniciar Sesión</a></li>
                    <li><a href="../utilities/register.php">Registrarse</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="order-success-container">
            <h2>¡Gracias por tu compra!</h2>
            <?php if (isset($order_details)): ?>
                <div class="success-message">
                    <p>Tu pedido ha sido procesado exitosamente. El número de tu pedido es <strong>#<?php echo $order_details['id']; ?></strong>.</p>
                </div>

                <div class="order-details">
                    <h3>Detalles del pedido:</h3>
                    <ul>
                        <?php foreach ($order_items as $item): ?>
                            <li>
                                <p><strong><?php echo htmlspecialchars($item['name']); ?></strong> - $<?php echo number_format($item['price'], 2); ?> x <?php echo $item['quantity']; ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <p class="total-price"><strong>Total:</strong> $<?php
                        $total = 0;
                        foreach ($order_items as $item) {
                            $total += $item['price'] * $item['quantity'];
                        }
                        echo number_format($total, 2);
                    ?></p>

                    <p>El envío será procesado y recibirás un correo de confirmación pronto.</p>
                </div>

                <a href="../index.php" class="btn-back">Volver al inicio</a>
            <?php else: ?>
                <div class="error-message">
                    <p><?php echo isset($error_message) ? $error_message : 'Hubo un problema al procesar tu pedido.'; ?></p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Tienda de Impresiones 3D. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
