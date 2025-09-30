<?php
session_start();
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Agregar producto desde JS mediante POST
if (isset($_POST['agregar_carrito_js'])) {
    $producto = $_POST['producto'];
    $precio = $_POST['precio'];
    $cantidad = $_POST['cantidad'];

    $_SESSION['carrito'][] = [
        'producto' => $producto,
        'precio' => $precio,
        'cantidad' => $cantidad
    ];
}

// Vaciar carrito
if (isset($_POST['vaciar_carrito'])) {
    $_SESSION['carrito'] = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Tienda Tech Online</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>

<h1>Tienda Tech Online</h1>

<div id="results-container"></div>

<h2>Carrito de Compras</h2>
<div id="carrito">
<?php
$total = 0;
if(!empty($_SESSION['carrito'])){
    echo "<ul>";
    foreach($_SESSION['carrito'] as $item){
        $subtotal = $item['precio'] * $item['cantidad'];
        $total += $subtotal;
        echo "<li>{$item['producto']} - {$item['cantidad']} x \${$item['precio']} = \${$subtotal}</li>";
    }
    echo "</ul>";
    echo "<p><strong>Total:</strong> \$$total</p>";
    echo '<form method="POST"><button type="submit" name="vaciar_carrito">Vaciar Carrito</button></form>';
} else {
    echo "<p>El carrito está vacío.</p>";
}
?>
</div>

<form id="formCarrito" method="POST" style="display:none;">
    <input type="hidden" name="producto" id="inputProducto">
    <input type="hidden" name="precio" id="inputPrecio">
    <input type="hidden" name="cantidad" id="inputCantidad" value="1">
    <input type="hidden" name="agregar_carrito_js" value="1">
</form>

<script src="app.js"></script>
<script>
// Sobrescribimos la función addToCart para enviar a PHP
function addToCart(id) {
    const product = products.find(p => p.id === id);
    if(!product) return;

    // Colocamos los valores en el formulario oculto
    document.getElementById('inputProducto').value = product.name;
    document.getElementById('inputPrecio').value = product.price;
    document.getElementById('inputCantidad').value = 1; // puedes cambiar a cantidad seleccionada

    // Enviamos el formulario para que PHP lo agregue a la sesión
    document.getElementById('formCarrito').submit();
}
</script>
</body>
</html>

