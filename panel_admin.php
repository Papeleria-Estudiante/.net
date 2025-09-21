<?php
session_start();

// VERIFICAR QUE SOLO EL ADMIN PUEDA ENTRAR
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: admin.php");
    exit();
}

// CONEXIÓN A LA BD
$conn = new mysqli("localhost", "root", "Sebastian.1501", "papeleria");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// --- AGREGAR PRODUCTO ---
if (isset($_POST['agregar'])) {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];

    $sql = "INSERT INTO productos (nombre, precio) VALUES ('$nombre', '$precio')";
    $conn->query($sql);
}

// --- ELIMINAR PRODUCTO ---
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $conn->query("DELETE FROM productos WHERE id=$id");
    header("Location: panel_admin.php");
    exit();
}

// --- OBTENER PRODUCTOS ---
$result = $conn->query("SELECT * FROM productos");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <h1>📦 Panel de Administración</h1>
    <a href="logout.php">Cerrar Sesión</a>

    <h2>Agregar Producto</h2>
    <form method="POST">
        <input type="text" name="nombre" placeholder="Nombre del producto" required>
        <input type="number" step="0.01" name="precio" placeholder="Precio" required>
        <button type="submit" name="agregar">Agregar</button>
    </form>

    <h2>Lista de Productos</h2>
    <table border="1" cellpadding="5">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Acciones</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['nombre'] ?></td>
            <td>$<?= number_format($row['precio'], 2) ?></td>
            <td>
                <a href="editar_producto.php?id=<?= $row['id'] ?>">✏️ Editar</a> |
                <a href="?eliminar=<?= $row['id'] ?>" onclick="return confirm('¿Seguro que deseas eliminar este producto?')">🗑️ Eliminar</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>
