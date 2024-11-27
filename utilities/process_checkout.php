<?php
// Iniciar la sesión
session_start();

// Incluir la conexión a la base de datos
include '../utilities/db_connect.php'; // Corregir la ruta según tu estructura de archivos

// Verificar si el carrito está vacío
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Verificar si el formulario de checkout fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger los datos del formulario
    $full_name = $_POST['full_name'];
    $address = $_POST['address'];
    $payment_method = $_POST['payment_method'];

    // Calcular el total de la compra
    $total_price = 0;
    foreach ($_SESSION['cart'] as $product_id) {
        $stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $total_price += $product['price'];
    }

    // Insertar los detalles de la compra en la tabla `orders`
    $stmt = $conn->prepare("INSERT INTO orders (user_name, shipping_address, payment_method, total_price, order_date) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $full_name, $address, $payment_method, $total_price);
    $stmt->execute();

    // Obtener el ID de la orden recién insertada
    $order_id = $stmt->insert_id;

    // Insertar los productos del carrito en la tabla `order_items`
    foreach ($_SESSION['cart'] as $product_id) {
        // Obtener detalles del producto
        $stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        // Insertar el producto en la tabla `order_items`
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, 1, ?)");
        $stmt->bind_param("iii", $order_id, $product_id, $product['price']);
        $stmt->execute();
    }

    // Vaciar el carrito después de la compra
    unset($_SESSION['cart']);

    // Redirigir a una página de confirmación o éxito
    header("Location: order_success.php?order_id=$order_id");
    exit();
} else {
    // Si no se accede al formulario por POST, redirigir a carrito
    header("Location: cart.php");
    exit();
}
