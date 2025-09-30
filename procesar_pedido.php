<?php
// Revisar si se envió el formulario de registro de pedido
if (isset($_POST['registrar_pedido'])) {
    $descripcion = trim($_POST['descripcion']);
    $tipo = trim($_POST['tipo']);
    $producto = trim($_POST['producto']);
    $unidades = trim($_POST['unidades']);
    $observaciones = trim($_POST['observaciones']);

    // Validar que los campos obligatorios no estén vacíos
    if ($descripcion && $tipo && $producto && $unidades) {
        // Guardar en archivo de texto (pedidos.txt)
        $linea = "$descripcion|$tipo|$producto|$unidades|$observaciones\n";
        file_put_contents("pedidos.txt", $linea, FILE_APPEND);
    }
}

// Mostrar pedidos registrados
if (file_exists("pedidos.txt")) {
    $pedidos = file("pedidos.txt", FILE_IGNORE_NEW_LINES);

    echo "<div class='pedidos'>";
    foreach ($pedidos as $pedido) {
        list($descripcion, $tipo, $producto, $unidades, $observaciones) = explode("|", $pedido);
        echo "<p><strong>Producto:</strong> $producto | <strong>Tipo:</strong> $tipo | <strong>Unidades:</strong> $unidades | <strong>Descripción:</strong> $descripcion";
        if ($observaciones) {
            echo " | <strong>Observaciones:</strong> $observaciones";
        }
        echo "</p>";
    }
    echo "</div>";
} else {
    echo "<p>No hay pedidos registrados todavía.</p>";
}
?>
