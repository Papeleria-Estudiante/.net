<?php
// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "Sebastian.1501", "papeleria");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Procesar formulario al enviar
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST['usuario']);
    $correo = trim($_POST['correo']);
    $password = $_POST['password'];

    if (empty($usuario) || empty($correo) || empty($password)) {
        $error = "⚠️ Todos los campos son obligatorios.";
    } else {
        // Verificar si el usuario o correo ya existen
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE nombre_usuario = ? OR correo = ?");
        $stmt->bind_param("ss", $usuario, $correo);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "⚠️ El nombre de usuario o el correo ya están registrados, elige otro.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Insertar nuevo usuario con rol 'usuario'
            $stmt_insert = $conn->prepare("INSERT INTO usuarios (nombre_usuario, correo, contraseña, rol) VALUES (?, ?, ?, ?)");
            $rol = 'usuario';
            $stmt_insert->bind_param("ssss", $usuario, $correo, $password_hash, $rol);

            if ($stmt_insert->execute()) {
                $success = "✅ Usuario registrado correctamente. Ya puedes iniciar sesión.";
            } else {
                $error = "❌ Error al registrar: " . $conn->error;
            }
            $stmt_insert->close();
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #00c6ff, #0072ff);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0px 10px 25px rgba(0,0,0,0.2);
            width: 350px;
            text-align: center;
        }

        h1 {
            margin-bottom: 30px;
            color: #0072ff;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #0072ff;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background: #005bb5;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }

        .success {
            color: green;
            margin-bottom: 15px;
        }

        a {
            display: block;
            margin-top: 15px;
            color: #0072ff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>📝 Registro de Usuario</h1>

    <?php if (isset($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
        <p><a href="login.php">Inicia sesión aquí</a></p>
    <?php else: ?>
        <form method="POST">
            <input type="text" name="usuario" placeholder="Nombre de usuario" required>
            <input type="email" name="correo" placeholder="Correo electrónico" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Registrarse</button>
        </form>
        <p><a href="login.php">¿Ya tienes cuenta? Inicia sesión</a></p>
    <?php endif; ?>
</div>

</body>
</html>
