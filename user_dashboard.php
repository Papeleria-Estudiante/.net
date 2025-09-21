<?php
session_start();

// Verificar usuario
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'usuario') {
    header("Location: login.php");
    exit();
}

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "Sebastian.1501", "papeleria");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Productos de ejemplo (puedes reemplazar con base de datos)
$productos = [
    ['id'=>1,'nombre'=>'Cuaderno','precio'=>5000],
    ['id'=>2,'nombre'=>'Lápiz','precio'=>1000],
    ['id'=>3,'nombre'=>'Bolígrafo','precio'=>1500],
];

// Inicializar carrito
if (!isset($_SESSION['carrito'])) $_SESSION['carrito'] = [];

// Agregar producto al carrito
if (isset($_POST['agregar'])) {
    $id_producto = $_POST['producto_id'];
    $_SESSION['carrito'][$id_producto] = ($_SESSION['carrito'][$id_producto] ?? 0) + 1;
}

// Eliminar producto del carrito
if (isset($_POST['eliminar'])) {
    $id_producto = $_POST['eliminar'];
    if (isset($_SESSION['carrito'][$id_producto])) {
        unset($_SESSION['carrito'][$id_producto]);
    }
}

// Procesar compra
if (isset($_POST['comprar'])) {
    $metodo_pago = $_POST['metodo_pago'];
    $telefono = trim($_POST['telefono']);

    if (empty($telefono)) {
        $mensaje_error = "⚠️ Debe ingresar su número de teléfono.";
    } elseif(empty($_SESSION['carrito'])) {
        $mensaje_error = "⚠️ No hay productos en el carrito.";
    } else {
        $codigo_compra = strtoupper(substr(md5(uniqid()),0,10));

        // Generar string de productos comprados
        $productos_comprados = [];
        $total = 0;
        foreach($_SESSION['carrito'] as $id=>$cant) {
            $pr_arr = array_filter($productos, function($x) use ($id) { return $x['id'] == $id; });
            $pr = array_values($pr_arr)[0];
            $subtotal = $pr['precio'] * $cant;
            $total += $subtotal;
            $productos_comprados[] = $pr['nombre']." x".$cant." ($".number_format($subtotal).")";
        }
        $productos_str = implode(", ", $productos_comprados);

        // Guardar en base de datos
        $stmt = $conn->prepare("INSERT INTO compras (usuario, telefono, productos, total, codigo_compra, metodo_pago) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiss", $_SESSION['usuario'], $telefono, $productos_str, $total, $codigo_compra, $metodo_pago);
        $stmt->execute();
        $stmt->close();

        // Enviar correo al administrador (ajusta tu correo)
        $to = "tucorreo@dominio.com"; 
        $subject = "Nueva compra - Código $codigo_compra";
        $message = "Usuario: ".$_SESSION['usuario']."\nTeléfono: ".$telefono."\nProductos: ".$productos_str."\nTotal: $".number_format($total)."\nCódigo de compra: ".$codigo_compra."\nMétodo de pago: ".$metodo_pago;
        $headers = "From: no-reply@tupapeleria.com";

        @mail($to, $subject, $message, $headers);

        // Mensaje al usuario
        if ($metodo_pago === 'efectivo') {
            $mensaje = "✅ Compra realizada con éxito.\nCódigo de compra: $codigo_compra\n💵 Diríjase al local de la papelería para pagar.";
        } else {
            $mensaje = "✅ Compra realizada con éxito.\nCódigo de compra: $codigo_compra\n📲 Realice el pago al número 324 526 6442 y envíe el comprobante al mismo número.";
        }

        if ($total > 50000) {
            $mensaje .= "\n🏠 Compra mayor a 50.000 se realiza domicilio, envío a nivel nacional.";
        }

        $_SESSION['carrito'] = [];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel Usuario</title>
<link rel="stylesheet" href="style.css">
<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f0f4f8;
    margin: 0;
    padding: 0;
}
.container {
    max-width: 1000px;
    margin: 40px auto;
    padding: 30px;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}
h1, h2 { color: #0072ff; text-align: center; }
.productos { display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; margin-bottom: 30px; }
.producto { background: #f9f9f9; padding: 20px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); text-align: center; width: 200px; transition: transform 0.2s; }
.producto:hover { transform: translateY(-5px); }
button { background: #0072ff; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: bold; transition: 0.3s; }
button:hover { background: #005bb5; }
select, input[type="text"] { padding: 10px; border-radius: 8px; border: 1px solid #ccc; margin-bottom: 10px; width: 100%; }
.mensaje { background: #e0ffe0; border: 1px solid #00a000; padding: 15px; border-radius: 10px; white-space: pre-line; text-align: center; font-weight: bold; margin-bottom: 20px; }
.error { background: #ffe0e0; border: 1px solid #a00000; padding: 15px; border-radius: 10px; text-align: center; font-weight: bold; margin-bottom: 20px; }
a.button { display: inline-flex; align-items: center; justify-content: center; padding: 10px 20px; background: #0072ff; color: #fff; border-radius: 8px; text-decoration: none; font-weight: bold; cursor: pointer; }
a.button:hover { background: #005bb5; }
table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
thead { background-color: #0072ff; color: white; }
</style>
</head>
<body>

<div class="container">
<h1>👤 Bienvenido, <?= htmlspecialchars($_SESSION['usuario']) ?>!</h1>
<p style="text-align:center;">Selecciona tus productos y realiza tu compra.</p>

<h2>🛒 Productos Disponibles</h2>
<div class="productos">
<?php foreach($productos as $p): ?>
<div class="producto">
    <h3><?= htmlspecialchars($p['nombre']) ?></h3>
    <p>Precio: $<?= number_format($p['precio']) ?></p>
    <form method="POST">
        <input type="hidden" name="producto_id" value="<?= $p['id'] ?>">
        <button type="submit" name="agregar">Agregar al carrito</button>
    </form>
</div>
<?php endforeach; ?>
</div>

<h2>🛍 Tu Carrito</h2>

<?php if(!empty($_SESSION['carrito'])): ?>
<table>
    <thead>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Subtotal</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $total = 0;
    foreach($_SESSION['carrito'] as $id=>$cant):
        $pr_arr = array_filter($productos, function($x) use ($id) { return $x['id']==$id; });
        $pr = array_values($pr_arr)[0];
        $subtotal = $pr['precio'] * $cant;
        $total += $subtotal;
    ?>
    <tr>
        <td><?= htmlspecialchars($pr['nombre']) ?></td>
        <td><?= $cant ?></td>
        <td>$<?= number_format($pr['precio']) ?></td>
        <td>$<?= number_format($subtotal) ?></td>
        <td>
            <form method="POST" style="margin:0;">
                <button type="submit" name="eliminar" value="<?= $id ?>" style="background:red;">Eliminar</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="3"><strong>Total</strong></td>
        <td colspan="2"><strong>$<?= number_format($total) ?></strong></td>
    </tr>
    </tbody>
</table>

<!-- Botones y teléfono -->
<div style="display:flex; justify-content:space-between; max-width:1000px; margin:20px auto;">
    <!-- Realizar Compra -->
    <form method="POST" style="flex:1; margin-right:10px;">
        <input type="text" name="telefono" placeholder="Número de teléfono" required>
        <select name="metodo_pago" required>
            <option value="">Seleccione método de pago</option>
            <option value="efectivo">Efectivo</option>
            <option value="nequi">Nequi</option>
            <option value="daviplata">Daviplata</option>
        </select>
        <button type="submit" name="comprar" style="width:100%;">Realizar Compra</button>
    </form>
    <!-- Cerrar Sesión -->
    <a href="logout.php" class="button" style="flex:1; margin-left:10px; height:100%; display:flex; align-items:center; justify-content:center;">Cerrar sesión</a>
</div>

<?php else: ?>
<p style="text-align:center;">No hay productos en el carrito.</p>
<a href="logout.php" class="button" style="display:block; width:150px; margin:20px auto; text-align:center;">Cerrar sesión</a>
<?php endif; ?>

<?php 
if(isset($mensaje_error)) echo '<div class="error">'.htmlspecialchars($mensaje_error).'</div>';
if(isset($mensaje)) echo '<div class="mensaje">'.nl2br(htmlspecialchars($mensaje)).'</div>';
?>

</div>
</body>
</html>
