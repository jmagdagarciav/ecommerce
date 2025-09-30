<?php
// Revisar si se envió el formulario
if (isset($_POST['enviar_resena'])) {
    $usuario = trim($_POST['usuario']);
    $producto = trim($_POST['producto']);
    $calificacion = trim($_POST['calificacion']);
    $comentario = trim($_POST['comentario']);

    // Validar que no estén vacíos
    if ($usuario && $producto && $calificacion && $comentario) {
        // Guardar en archivo de texto (reseñas.txt)
        $linea = "$usuario|$producto|$calificacion|$comentario\n";
        file_put_contents("reseñas.txt", $linea, FILE_APPEND);
    }
}

// Mostrar reseñas
if (file_exists("reseñas.txt")) {
    $resenas = file("reseñas.txt", FILE_IGNORE_NEW_LINES);

    echo "<div class='resenas'>";
    foreach ($resenas as $resena) {
        list($usuario, $producto, $calificacion, $comentario) = explode("|", $resena);
        echo "<p><strong>$usuario</strong> sobre <em>$producto</em>: $comentario - ⭐$calificacion</p>";
    }
    echo "</div>";
} else {
    echo "<p>No hay reseñas todavía.</p>";
}
?>
