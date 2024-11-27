<?php
// Iniciar la sesión
session_start();

// Incluir la conexión a la base de datos
include '../utilities/db_connect.php'; // Corregir la ruta según tu estructura de archivos

// Verificar si el carrito ya existe, si no, inicializarlo
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Obtener los productos del carrito desde la base de datos
$cart_items = [];
if (!empty($_SESSION['cart'])) {
    $placeholders = implode(',', array_fill(0, count($_SESSION['cart']), '?'));
    $stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->bind_param(str_repeat('i', count($_SESSION['cart'])), ...$_SESSION['cart']);
    $stmt->execute();
    $result = $stmt->get_result();

    // Obtener los productos de la consulta
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Compra</title>
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
                    <li><a href="../utilities/login.php">Iniciar Sesión</a></li>
                    <li><a href="../utilities/register.php">Registrarse</a></li>
                    <li><a href="../admin/admin_login.php">Iniciar Sesión Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="checkout">
            <h2>Resumen de tu Compra</h2>

            <?php if (empty($cart_items)) { ?>
                <p>Tu carrito está vacío. No puedes proceder al pago.</p>
            <?php } else { ?>
                <div class="cart-summary">
                    <?php foreach ($cart_items as $item) { ?>
                        <div class="cart-item">
                            <img src="../admin/uploaded_img/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            <div class="cart-item-info">
                                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p>$<?php echo number_format($item['price'], 2); ?></p>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="cart-total">
                    <p class="total-label">Total:</p>
                    <p>$<?php
                        $total = 0;
                        foreach ($cart_items as $item) {
                            $total += $item['price'];
                        }
                        echo number_format($total, 2);
                    ?></p>
                </div>

                <!-- Formulario de Checkout -->
                <div class="checkout-form">
                    <form action="process_checkout.php" method="POST">
                        <label for="full_name">Nombre Completo:</label>
                        <input type="text" id="full_name" name="full_name" required>

                        <label for="address">Dirección de Envío:</label>
                        <input type="text" id="address" name="address" required>

                        <label for="payment_method">Método de Pago:</label>
                        <select id="payment_method" name="payment_method" required>
                            <option value="credit_card">Tarjeta de Crédito</option>
                            <option value="paypal">PayPal</option>
                        </select>

                        <button type="submit" class="btn">Finalizar Compra</button>
                    </form>
                </div>
            <?php } ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Tienda de Impresiones 3D. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
