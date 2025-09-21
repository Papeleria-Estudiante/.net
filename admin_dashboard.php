<?php
session_start();

// Verificar sesión de admin
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "Sebastian.1501", "papeleria");
if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

// ------------------------
// Gestionar compras
// ------------------------
if (isset($_GET['delete_compra'])) {
    $id = intval($_GET['delete_compra']);
    $conn->query("DELETE FROM compras WHERE id = $id");
    header("Location: admin_dashboard.php");
    exit();
}

// ------------------------
// Gestionar productos
// ------------------------

// Eliminar producto
if (isset($_GET['delete_producto'])) {
    $id = intval($_GET['delete_producto']);
    $conn->query("DELETE FROM productos WHERE id = $id");
    header("Location: admin_dashboard.php");
    exit();
}

// Cambiar estado de producto
if (isset($_POST['cambiar_estado'])) {
    $id_producto = intval($_POST['id_producto']);
    $nuevo_estado = $_POST['estado'];
    $stmt = $conn->prepare("UPDATE productos SET estado=? WHERE id=?");
    $stmt->bind_param("si", $nuevo_estado, $id_producto);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_dashboard.php");
    exit();
}

// Agregar producto
$mensaje_producto = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_producto'])) {
    $nombre = trim($_POST['nombre']);
    $precio = floatval($_POST['precio']);
    $estado = 'Disponible';

    // Subir imagen
    $imagen = '';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $imagen = 'uploads/' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['imagen']['tmp_name'], $imagen);
    }

    $stmt = $conn->prepare("INSERT INTO productos (nombre, precio, imagen, estado) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdss", $nombre, $precio, $imagen, $estado);
    if ($stmt->execute()) {
        $mensaje_producto = "✅ Producto agregado correctamente.";
    } else {
        $mensaje_producto = "❌ Error al agregar producto: " . $conn->error;
    }
    $stmt->close();
}

// Traer todas las compras
$compras = $conn->query("SELECT * FROM compras ORDER BY fecha DESC");

// Traer todos los productos
$productos = $conn->query("SELECT * FROM productos ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel Administrador</title>
<link rel="stylesheet" href="style.css">
<style>
body { font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background:#f0f4f8; margin:0; padding:0; }
.container { max-width:1200px; margin:40px auto; padding:30px; background:#fff; border-radius:15px; box-shadow:0 10px 25px rgba(0,0,0,0.1);}
h1,h2 { color:#0072ff; text-align:center; margin-bottom:20px; }
table { width:100%; border-collapse:collapse; margin-bottom:20px;}
th, td { border:1px solid #ccc; padding:10px; text-align:center; }
thead { background:#0072ff; color:#fff; }
tr:nth-child(even) { background:#f9f9f9; }
a.button { display:inline-block; padding:10px 20px; background:#0072ff; color:#fff; border-radius:8px; text-decoration:none; font-weight:bold; margin-top:10px; }
a.button:hover { background:#005bb5; }
a.delete-btn { color:white; background:#ff4d4d; padding:5px 10px; border-radius:5px; text-decoration:none; }
a.delete-btn:hover { background:#cc0000; }
.actions { display:flex; justify-content:center; gap:10px; flex-wrap:wrap; margin-top:20px; }
.success-message { background:#d4edda; color:#155724; padding:10px; border-radius:8px; margin-bottom:20px; white-space: pre-line; }
input, select { padding:10px; width:100%; margin:5px 0; border-radius:5px; border:1px solid #ccc; box-sizing:border-box;}
img.product-img { width:50px; height:50px; object-fit:cover; border-radius:5px; }
</style>
</head>
<body>

<div class="container">
<h1>👑 Panel de Administrador</h1>

<!-- ------------------------ -->
<h2>🛒 Compras</h2>
<?php if($compras && $compras->num_rows > 0): ?>
<table>
<thead>
<tr>
<th>ID</th>
<th>Usuario</th>
<th>Teléfono</th>
<th>Productos</th>
<th>Total</th>
<th>Código</th>
<th>Método Pago</th>
<th>Fecha</th>
<th>Acción</th>
</tr>
</thead>
<tbody>
<?php while($row = $compras->fetch_assoc()): ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= htmlspecialchars($row['usuario']) ?></td>
<td><?= htmlspecialchars($row['telefono']) ?></td>
<td><?= htmlspecialchars($row['productos']) ?></td>
<td>$<?= number_format($row['total']) ?></td>
<td><?= $row['codigo_compra'] ?></td>
<td><?= htmlspecialchars($row['metodo_pago']) ?></td>
<td><?= $row['fecha'] ?></td>
<td>
<a href="admin_dashboard.php?delete_compra=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('¿Eliminar esta compra?');">Eliminar</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
<?php else: ?>
<p style="text-align:center;">No hay compras registradas.</p>
<?php endif; ?>

<!-- ------------------------ -->
<h2>📦 Productos</h2>
<?php if($mensaje_producto): ?>
<div class="success-message"><?= $mensaje_producto ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
<input type="text" name="nombre" placeholder="Nombre del producto" required>
<input type="number" name="precio" placeholder="Precio" step="0.01" required>
<input type="file" name="imagen" accept="image/*" required>
<button type="submit" name="agregar_producto" class="button">Agregar Producto</button>
</form>

<?php if($productos && $productos->num_rows > 0): ?>
<table>
<thead>
<tr>
<th>ID</th>
<th>Nombre</th>
<th>Precio</th>
<th>Imagen</th>
<th>Estado</th>
<th>Acción</th>
</tr>
</thead>
<tbody>
<?php while($p = $productos->fetch_assoc()): ?>
<tr>
<td><?= $p['id'] ?></td>
<td><?= htmlspecialchars($p['nombre']) ?></td>
<td>$<?= number_format($p['precio']) ?></td>
<td><?php if($p['imagen']): ?><img src="<?= $p['imagen'] ?>" class="product-img"><?php endif; ?></td>
<td>
<form method="POST">
<input type="hidden" name="id_producto" value="<?= $p['id'] ?>">
<select name="estado" onchange="this.form.submit()">
<option value="Disponible" <?= $p['estado'] == 'Disponible' ? 'selected' : '' ?>>Disponible</option>
<option value="Agotado" <?= $p['estado'] == 'Agotado' ? 'selected' : '' ?>>Agotado</option>
</select>
<input type="hidden" name="cambiar_estado">
</form>
</td>
<td>
<a href="admin_dashboard.php?delete_producto=<?= $p['id'] ?>" class="delete-btn" onclick="return confirm('¿Eliminar este producto?');">Eliminar</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
<?php else: ?>
<p style="text-align:center;">No hay productos agregados.</p>
<?php endif; ?>

<div class="actions">
<a href="logout.php" class="button">Cerrar sesión</a>
</div>

</div>
</body>
</html>
