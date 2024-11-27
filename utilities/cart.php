<?php
// Iniciar la sesión
session_start();

// Incluir la conexión a la base de datos
include '../utilities/db_connect.php'; // Corregir la ruta según tu estructura de archivos

// Verificar si el carrito ya existe, si no, inicializarlo
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Verificar si se ha añadido un producto al carrito
if (isset($_GET['add_to_cart'])) {
    $product_id = $_GET['add_to_cart'];

    // Verificar si el producto ya está en el carrito
    if (!in_array($product_id, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $product_id; // Agregar el producto al carrito
    }

    // Redirigir de nuevo a la página del carrito
    header("Location: cart.php");
    exit();
}

// Verificar si se ha eliminado un producto del carrito
if (isset($_GET['remove_from_cart'])) {
    $product_id = $_GET['remove_from_cart'];

    // Eliminar el producto del carrito
    if (($key = array_search($product_id, $_SESSION['cart'])) !== false) {
        unset($_SESSION['cart'][$key]);
    }

    // Redirigir de nuevo a la página del carrito
    header("Location: cart.php");
    exit();
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
    <title>Carrito de Compras</title>
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
        <section class="cart">
            <h2>Tu Carrito de Compras</h2>

            <?php if (empty($cart_items)) { ?>
                <p>Tu carrito está vacío.</p>
            <?php } else { ?>
                <div class="cart-items">
                    <?php foreach ($cart_items as $item) { ?>
                        <div class="cart-item">
                            <img src="../admin/uploaded_img/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            <div class="cart-item-info">
                                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p>$<?php echo number_format($item['price'], 2); ?></p>
                                <a href="cart.php?remove_from_cart=<?php echo $item['id']; ?>" class="remove-item">Eliminar</a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="cart-total">
                    <h3>Total: $<?php
                        $total = 0;
                        foreach ($cart_items as $item) {
                            $total += $item['price'];
                        }
                        echo number_format($total, 2);
                    ?></h3>
                    <a href="checkout.php" class="btn">Proceder al Pago</a>
                </div>
            <?php } ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Tienda de Impresiones 3D. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
