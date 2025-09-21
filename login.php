<?php
session_start();

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "Sebastian.1501", "papeleria");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Procesar formulario al enviar
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario_input = trim($_POST['usuario']);
    $password = $_POST['password'];

    if (empty($usuario_input) || empty($password)) {
        $error = "⚠️ Todos los campos son obligatorios.";
    } else {
        $stmt = $conn->prepare(
            "SELECT id, contraseña, rol 
             FROM usuarios 
             WHERE TRIM(LOWER(nombre_usuario)) = LOWER(?)"
        );
        $stmt->bind_param("s", $usuario_input);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $hash_password, $rol);
            $stmt->fetch();

            if (password_verify($password, $hash_password)) {
                $_SESSION['id'] = $id;
                $_SESSION['usuario'] = $usuario_input;
                $_SESSION['rol'] = $rol;

                if ($rol === 'admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: user_dashboard.php");
                }
                exit();
            } else {
                $error = "❌ Contraseña incorrecta.";
            }
        } else {
            $error = "❌ El usuario no existe.";
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
    <title>Iniciar Sesión</title>
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
    <h1>🔑 Iniciar Sesión</h1>

    <?php if (isset($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="usuario" placeholder="Nombre de usuario" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Iniciar Sesión</button>
    </form>

    <p><a href="register.php">¿No tienes cuenta? Regístrate aquí</a></p>
</div>

</body>
</html>
