<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $admin = trim($_POST['admin']);
    $password = trim($_POST['password']);

    // Usuario admin fijo
    if ($admin === "admin" && $password === "1234") {
        // Guardamos la sesión igual que los usuarios normales
        $_SESSION['usuario'] = "admin";
        $_SESSION['rol'] = "admin";
        header("Location: admin_dashboard.php"); // o panel_admin.php
        exit();
    } else {
        $error = "❌ Credenciales incorrectas";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso Administrador</title>
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

        h2 {
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
    <h2>🔒 Acceso Administrador</h2>

    <?php if(isset($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="admin" placeholder="Usuario Admin" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Entrar</button>
    </form>
</div>

</body>
</html>
