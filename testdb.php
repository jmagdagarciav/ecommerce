<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=TIENDA", "root", "");
    echo "Conexión exitosa ✅";
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?>
