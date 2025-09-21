<?php
// Conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "Sebastian.1501";
$dbname = "papeleria";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener productos desde la base de datos
$sql = "SELECT * FROM productos";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papelería El Estudiante</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- HEADER -->
<header>
    <div class="logo">📚 Papelería El Estudiante</div>
    <nav id="menu">
        <a href="#inicio">Inicio</a>
        <a href="#portafolio">Portafolio</a>
        <a href="#tienda">Tienda</a>
        <a href="#quienes">Quiénes Somos</a>
        <a href="#ubicacion">Ubicación</a>
        <a href="#contacto">Contacto</a>
        <a href="mailto:papeleriaestudiante003@gmail.com">Correo</a>
    </nav>
    <div class="menu-toggle" onclick="toggleMenu()">☰</div>
</header>

<!-- INICIO -->
<section class="inicio fade-in" id="inicio">
    <div class="contenedor">
        <img src="img/logopapeleria.png" alt="Logo" class="inicio-logo">
        <h1>Bienvenido a Papelería El Estudiante</h1>
        <p>Todo lo que necesitas en artículos escolares, de oficina, hogar y aseo.</p>

        <!-- BOTONES INICIO -->
        <div class="botones-inicio fade-in">
            <a href="login.php" class="btn">Iniciar Sesión</a>
            <a href="register.php" class="btn">Registrarse</a>
            <a href="admin.php" class="btn btn-secundario">Entrar Admin</a>
        </div>

        <a href="#tienda" class="btn btn-secundario">Ver Productos</a>
    </div>
</section>

<!-- PORTAFOLIO -->
<section id="portafolio" class="section fade-in">
    <h2>Portafolio</h2>
    <div class="grid">
        <div class="card fade-in"><img src="img/articulo1.jpg" alt="Producto 1"></div>
        <div class="card fade-in"><img src="img/articulo2.jpg" alt="Producto 2"></div>
        <div class="card fade-in"><img src="img/articulo3.jpg" alt="Producto 3"></div>
        <div class="card fade-in"><img src="img/articulo4.jpg" alt="Producto 4"></div>
    </div>
</section>

<!-- TIENDA -->
<section id="tienda" class="section fade-in">
    <h2>Tienda</h2>
    <div class="grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="producto card fade-in">
                    <img src="img/<?php echo $row['imagen']; ?>" alt="<?php echo $row['nombre']; ?>">
                    <h3><?php echo $row['nombre']; ?></h3>
                    <p class="precio">$<?php echo number_format($row['precio'], 0, ',', '.'); ?></p>
                    <button class="btn">Comprar</button>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="fade-in">No hay productos disponibles en este momento.</p>
        <?php endif; ?>
    </div>
</section>

<!-- QUIÉNES SOMOS -->
<section id="quienes" class="section fade-in">
    <h2>Quiénes Somos</h2>
    <p>En Papelería El Estudiante creemos que cada idea merece ser escrita, cada proyecto merece tomar forma y cada meta debe estar bien equipada para hacerse realidad. Somos un emprendimiento nacido en mayo de 2024 con un propósito claro: ofrecer a nuestra comunidad un lugar donde encontrar todo lo necesario para la escuela, la oficina y el hogar, siempre acompañado de un servicio amable, cercano y confiable.</p>
</section>

<!-- UBICACIÓN -->
<section id="ubicacion" class="section fade-in">
    <h2>Ubicación</h2>
    <iframe src="https://www.google.com/maps/embed?pb=..." width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
</section>

<!-- CONTACTO -->
<section id="contacto" class="section fade-in">
    <h2>Contacto</h2>
    <p>Escríbenos por WhatsApp o correo electrónico.</p>
    <a href="https://wa.me/573209597677" class="btn">WhatsApp</a>
    <a href="mailto:papeleriaestudiante003@gmail.com" class="btn">Correo</a>
</section>

<!-- FOOTER -->
<footer>
    <p>&copy; 2025 Papelería El Estudiante. Todos los derechos reservados.</p>
</footer>

<script src="script.js"></script>
</body>
</html>

<?php $conn->close(); ?>
