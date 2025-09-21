<?php
// Archivo de conexión a la base de datos en InfinityFree

$host = "sql111.infinityfree.com";      // Host de MySQL proporcionado por InfinityFree
$user = "if0_39989465";                 // Usuario de la base de datos
$password = "Saua2012";                 // Contraseña de la base de datos
$dbname = "if0_39989465_papeleria";     // Nombre de la base de datos en InfinityFree

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("❌ Error de conexión: " . mysqli_connect_error());
}
// echo "✅ Conectado a la base de datos!";
?>
