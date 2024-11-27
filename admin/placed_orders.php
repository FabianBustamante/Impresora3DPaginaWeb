<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

include '../utilities/db_connect.php';

// Obtener pedidos con manejo de errores
$query = "SELECT * FROM orders ORDER BY order_date DESC";
$orders = mysqli_query($conn, $query);

// Verificar si la consulta se ejecutó correctamente
if (!$orders) {
    die("Error en la consulta: " . mysqli_error($conn)); // Mostrar error de consulta si no se ejecutó correctamente
}

// Actualizar estado del pedido
if (isset($_POST['mark_shipped'])) {
    $order_id = $_POST['order_id'];
    $update_query = "UPDATE orders SET status = 'Enviado' WHERE id = $order_id";
    
    // Verificar si la actualización se realizó correctamente
    if (mysqli_query($conn, $update_query)) {
        header("Location: placed_orders.php");
        exit;
    } else {
        echo "Error al actualizar el estado del pedido: " . mysqli_error($conn);
    }
}

// Eliminar pedido
if (isset($_POST['delete_order'])) {
    $order_id = $_POST['order_id'];
    $delete_query = "DELETE FROM orders WHERE id = $order_id";
    
    // Verificar si la eliminación se realizó correctamente
    if (mysqli_query($conn, $delete_query)) {
        header("Location: placed_orders.php");
        exit;
    } else {
        echo "Error al eliminar el pedido: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <title>Pedidos Realizados</title>
</head>
<body>
    <header>
        <h1>Gestión de Pedidos</h1>
    </header>
    <main>
        <section class="orders-list">
            <h2>Lista de Pedidos</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Pedido</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($orders) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($orders)): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= $row['user_name'] ?></td>
                                <td>$<?= number_format($row['total_price'], 2) ?></td>
                                <td><?= $row['status'] ?></td>
                                <td><?= $row['order_date'] ?></td>
                                <td>
                                    <?php if ($row['status'] == 'Enviado'): ?>
                                        <span>Enviado</span>
                                    <?php else: ?>
                                        <form method="post">
                                            <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                            <button type="submit" name="mark_shipped">Marcar como Enviado</button>
                                        </form>
                                    <?php endif; ?>
                                    <!-- Opción para eliminar el pedido -->
                                    <?php if ($row['status'] == 'Enviado'): ?>
                                        <form method="post">
                                            <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                            <button type="submit" name="delete_order">Eliminar Pedido</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No hay pedidos disponibles.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <br>
            <a href="admin_dashboard.php" class="btn-regresar">Regresar</a>
        </section>
    </main>
</body>
</html>
