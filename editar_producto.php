<?php
session_start();

// VERIFICAR QUE SOLO EL ADMIN PUEDA ENTRAR
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: admin.php");
    exit();
}

$conn = new mysqli("localhost", "root", "Sebastian.1501", "papeleria");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$id = $_GET['id'];

// Obtener el producto
$sql = "SELECT * FROM productos WHERE id = $id";
$result = $conn->query($sql);
$producto = $result->fetch_assoc();

if (!$producto) {
    echo "Producto no encontrado.";
    exit();
}

// Si el formulario fue enviado, actualizar
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];

    $conn->query("UPDATE productos SET nombre='$nombre', precio='$precio' WHERE id=$id");
    header("Location: panel_admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <h1>✏️ Editar Producto</h1>

    <form method="POST">
        <input type="text" name="nombre" value="<?= $producto['nombre'] ?>" required>
        <input type="number" step="0.01" name="precio" value="<?= $producto['precio'] ?>" required>
        <button type="submit">Guardar Cambios</button>
    </form>

    <p><a href="panel_admin.php">⬅ Volver al panel</a></p>

</body>
</html>
