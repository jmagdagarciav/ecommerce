<?php
session_start();
if (!isset($_SESSION['carrito'])) $_SESSION['carrito'] = [];

// ===============================
// 1️⃣ Conexión a la base TIENDA
// ===============================
$host = 'localhost';
$db   = 'TIENDA';
$user = 'root';
$pass = '';
$charset = 'utf8';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// ===============================
// 2️⃣ Registrar pedido desde formulario
// ===============================
if(isset($_POST['registrar_pedido'])){
    $descripcion = $_POST['descripcion'];
    $tipo = $_POST['tipo'];
    $producto = $_POST['producto'];
    $unidades = (int)$_POST['unidades'];
    $observaciones = $_POST['observaciones'];

    $stmt = $pdo->prepare("SELECT id_producto, precio FROM PRODUCTO WHERE nombre = ?");
    $stmt->execute([$producto]);
    $prod = $stmt->fetch();
    if($prod){
        $id_producto = $prod['id_producto'];
        $total = $prod['precio'] * $unidades;
        $id_cliente = 1;

        $stmt2 = $pdo->prepare("INSERT INTO COMPRA (cantidad,total,id_producto,id_cliente) VALUES (?,?,?,?)");
        $stmt2->execute([$unidades, $total, $id_producto, $id_cliente]);

        echo "<script>alert('Pedido registrado en la base de datos');</script>";
    } else {
        echo "<script>alert('Producto no encontrado');</script>";
    }
}

// ===============================
// 3️⃣ Agregar al carrito
// ===============================
if(isset($_POST['agregar_carrito'])){
    $producto = $_POST['producto'];
    $precio = (float)$_POST['precio'];
    $cantidad = (int)$_POST['cantidad'];

    $found = false;
    foreach($_SESSION['carrito'] as &$item){
        if($item['producto']==$producto){
            $item['cantidad']+=$cantidad;
            $found = true;
            break;
        }
    }
    if(!$found){
        $_SESSION['carrito'][] = ['producto'=>$producto,'precio'=>$precio,'cantidad'=>$cantidad];
    }
}

// ===============================
// 4️⃣ Vaciar carrito
// ===============================
if(isset($_POST['vaciar_carrito'])){
    $_SESSION['carrito'] = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gadget Store</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
/* =============================== */
/* CSS ORIGINAL INTACTO */
/* =============================== */
body {
  font-family: 'Segoe UI', sans-serif;
  margin: 0;
  background: #f0f2f5;
}
.search-container { text-align:center; margin:15px 0; }
.search-container input, .search-container select, .search-container button { padding:8px; margin:5px 3px; border-radius:5px; border:1px solid #ccc; }
.search-container button { background:#1976d2; color:white; border:none; cursor:pointer; }
.search-container button:hover { background:#1565c0; }
#results-container { display:grid; grid-template-columns: repeat(auto-fill,minmax(200px,1fr)); gap:15px; padding:15px; }
.product { background:white; padding:15px; border-radius:12px; text-align:center; box-shadow:0 5px 15px rgba(0,0,0,0.1); transition:transform 0.2s, box-shadow 0.2s; }
.product:hover { transform:translateY(-3px); box-shadow:0 8px 20px rgba(0,0,0,0.15); }
.product h3 { margin:8px 0; font-size:17px; }
.product p { margin:5px 0; color:#555; }
.product button { background:#4caf50; color:white; border:none; padding:8px 12px; border-radius:6px; cursor:pointer; }
.product button:hover { background:#388e3c; }
#cart { position:fixed; top:100px; right:0; width:280px; background:#fff; padding:15px; border-left:1px solid #ccc; box-shadow:-3px 0 10px rgba(0,0,0,0.2); border-radius:8px 0 0 8px; z-index:1000; display:none; }
#cart button#closeCart { background:#ff3333; color:white; border:none; padding:5px 10px; float:right; cursor:pointer; border-radius:5px; }
#cart ul { list-style:none; padding:0; max-height:200px; overflow-y:auto; }
#cart li { border-bottom:1px solid #ddd; padding:4px 0; }
#payBtn, #submitPayment { background:#1976d2; color:white; border:none; padding:8px; border-radius:6px; cursor:pointer; width:100%; margin-top:8px; }
#payBtn:hover, #submitPayment:hover { background:#1565c0; }
#paymentForm input { width:100%; padding:6px; margin-bottom:6px; border-radius:5px; border:1px solid #ccc; }
.hidden { display:none; }
</style>
</head>
<body>

<h1>Gadget Store</h1>
<button id="cartBtn" style="margin-left:20px;padding:10px 20px;border-radius:10px;background:#00796B;color:white;border:none;cursor:pointer;">
🛒 Ver Carrito (<?php echo count($_SESSION['carrito']); ?>)
</button>

<div class="search-container">
  <input type="text" id="product-search" placeholder="Buscar producto..." onkeyup="searchProducts()">
  <select id="categoryFilter">
    <option value="all">Todas las categorías</option>
    <option value="Notebooks">Notebooks</option>
    <option value="Smartphone">Smartphone</option>
    <option value="Accesorios">Accesorios</option>
  </select>
</div>

<div id="results-container"></div>

<!-- Carrito -->
<div id="cart">
  <button id="closeCart">Cerrar</button>
  <h3>Carrito de Compras (<span id="cartCount"><?php echo count($_SESSION['carrito']); ?></span>)</h3>
  <ul id="cartItems">
    <?php
    $total = 0;
    foreach($_SESSION['carrito'] as $item){
        $subtotal = $item['precio'] * $item['cantidad'];
        $total += $subtotal;
        echo "<li>{$item['producto']} x {$item['cantidad']} = \${$subtotal}</li>";
    }
    ?>
  </ul>
  <p>Total: $<span id="cartTotal"><?php echo $total; ?></span></p>
  <form method="POST"><button name="vaciar_carrito">Vaciar Carrito</button></form>
  <button id="payBtn">Pagar</button>
</div>

<div id="paymentForm" class="hidden">
  <h3>Pago con tarjeta</h3>
  <input type="text" id="cardNumber" placeholder="Número de tarjeta">
  <input type="text" id="cardName" placeholder="Nombre del titular">
  <input type="text" id="expiry" placeholder="MM/AA">
  <input type="text" id="cvv" placeholder="CVV">
  <button id="submitPayment">Confirmar Pago</button>
</div>

<h2 style="text-align:center;">Registrar Pedido</h2>
<form method="POST" style="max-width:500px;margin:20px auto;">
<input type="text" name="descripcion" placeholder="Descripción" required>
<input type="text" name="tipo" placeholder="Tipo" required>
<input type="text" name="producto" placeholder="Producto" required>
<input type="number" name="unidades" placeholder="Unidades" required>
<textarea name="observaciones" placeholder="Observaciones"></textarea>
<button name="registrar_pedido">Registrar Pedido</button>
</form>

<script>
// Productos con palabras clave
const products = [
  {id: 1, name: "Notebook Gamer", category: "Notebooks", keywords: ["notebook", "gaming"], price: 1200000},
  {id: 2, name: "Computador Oficina", category: "Notebooks", keywords: ["notebook", "oficina"], price: 800000},
  {id: 3, name: "Smartphone Samsung", category: "Smartphone", keywords: ["celular", "teléfono"], price: 600000},
  {id: 4, name: "Smartphone Iphone", category: "Smartphone", keywords: ["celular", "teléfono"], price: 450000},
  {id: 5, name: "Auriculares Bluetooth", category: "Accesorios", keywords: ["audio", "auriculares"], price: 50000},
  {id: 6, name: "Mouse Gamer", category: "Accesorios", keywords: ["mouse", "gaming"], price: 35000},
];

let cart = <?php echo json_encode($_SESSION['carrito']); ?>;

// Mostrar productos
const resultsContainer = document.getElementById("results-container");
function displayProducts(list) {
  resultsContainer.innerHTML = "";
  if(list.length === 0){
    resultsContainer.innerHTML = "<p>No se encontraron productos</p>";
    return;
  }
  list.forEach(p => {
    const div = document.createElement("div");
    div.classList.add("product");
    div.innerHTML = `
      <h3>${p.name}</h3>
      <p>Precio: $${p.price}</p>
      <button onclick="addToCart(${p.id})">Agregar al carrito</button>
    `;
    resultsContainer.appendChild(div);
  });
}
displayProducts(products);

// Búsqueda por nombre, categoría o keywords
function searchProducts() {
  const text = document.getElementById("product-search").value.toLowerCase();
  const filtered = products.filter(p =>
    p.name.toLowerCase().includes(text) ||
    p.category.toLowerCase().includes(text) ||
    p.keywords.some(k => k.includes(text))
  );
  displayProducts(filtered);
}

// Filtrar por categoría
document.getElementById("categoryFilter").addEventListener("change", (e) => {
  const cat = e.target.value;
  if(cat === "all") displayProducts(products);
  else displayProducts(products.filter(p => p.category === cat));
});

// Carrito
const cartDiv = document.getElementById("cart");
const cartBtn = document.getElementById("cartBtn");
const cartItems = document.getElementById("cartItems");
const cartTotal = document.getElementById("cartTotal");
const cartCount = document.getElementById("cartCount");
const paymentForm = document.getElementById("paymentForm");
const submitPayment = document.getElementById("submitPayment");

// Abrir/cerrar carrito
cartBtn.addEventListener("click", () => {
  cartDiv.style.display = cartDiv.style.display === "none" ? "block" : "none";
  paymentForm.classList.add("hidden");
});

document.getElementById("closeCart").addEventListener("click", () => {
  cartDiv.style.display = "none";
  paymentForm.classList.add("hidden");
});

// Agregar al carrito
function addToCart(id) {
  const product = products.find(p => p.id === id);
  const existing = cart.find(item => item.producto === product.name);
  if(existing){
    existing.cantidad++;
  } else {
    cart.push({producto: product.name, precio: product.price, cantidad:1});
  }
  updateCart();
}

// Actualizar carrito
function updateCart() {
  cartItems.innerHTML = "";
  let total = 0;
  cart.forEach(item => {
    total += item.precio * item.cantidad;
    const li = document.createElement("li");
    li.textContent = `${item.producto} x ${item.cantidad} = $${item.precio*item.cantidad}`;
    cartItems.appendChild(li);
  });
  cartTotal.textContent = total;
  cartCount.textContent = cart.length;
}

// Mostrar formulario de pago
document.getElementById("payBtn").addEventListener("click", () => {
  if(cart.length === 0) return alert("Carrito vacío");
  paymentForm.classList.remove("hidden");
});

// Confirmar pago
submitPayment.addEventListener("click", () => {
  const cardNumber = document.getElementById("cardNumber").value;
  const cardName = document.getElementById("cardName").value;
  const expiry = document.getElementById("expiry").value;
  const cvv = document.getElementById("cvv").value;

  if(cardNumber.length !== 16 || !/^\d+$/.test(cardNumber)) return alert("Número de tarjeta inválido");
  if(cardName.trim() === "") return alert("Ingrese el nombre del titular");
  if(!/^\d{2}\/\d{2}$/.test(expiry)) return alert("Fecha inválida MM/AA");
  if(cvv.length !== 3 || !/^\d+$/.test(cvv)) return alert("CVV inválido");

  alert("Pago realizado ✅");
  cart = [];
  updateCart();
  paymentForm.classList.add("hidden");
  cartDiv.style.display = "none";
});

updateCart();
</script>

</body>
</html>
