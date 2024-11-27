<?php
// Iniciar la sesión
session_start();

// Incluir la conexión a la base de datos
include '../utilities/db_connect.php'; // Corregir la ruta si es necesario

// Obtener el ID del producto desde la URL
$product_id = isset($_GET['id']) ? $_GET['id'] : null;

// Comprobar si el producto existe
if ($product_id) {
    $query = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
}

// Verificar si se ha añadido el producto al carrito
if (isset($_GET['add_to_cart'])) {
    $product_id_to_add = $_GET['add_to_cart'];

    // Verificar si el carrito existe
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Añadir el producto al carrito si no está ya en él
    if (!in_array($product_id_to_add, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $product_id_to_add;
    }

    // Redirigir para evitar recargar la página con el mismo GET
    header("Location: quick_view.php?id=" . $product_id_to_add);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Producto - Tienda 3D</title>
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
                    <li><a href="products.php">Productos</a></li>
                    <li><a href="cart.php">Carrito</a></li>
                    <li><a href="login.php">Iniciar Sesión</a></li>
                    <li><a href="register.php">Registrarse</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="product-details">
            <?php if ($product): ?>
                <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                <img src="../admin/uploaded_img/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                <p><strong>Precio:</strong> $<?php echo number_format($product['price'], 2); ?></p>
                <form action="quick_view.php" method="get">
                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                    <button type="submit" name="add_to_cart" value="<?php echo $product['id']; ?>">Añadir al carrito</button>
                </form>
            <?php else: ?>
                <p class="error-message">Producto no encontrado.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Tienda de Impresiones 3D. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
