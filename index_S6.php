<?php
session_start();
if (!isset($_SESSION['carrito'])) $_SESSION['carrito'] = [];

// ===============================
// 1️⃣ Conexión a la base TIENDA
// ===============================
$host = 'localhost';
$db   = 'TIENDA';
$user = 'root';  // Ajusta según tu usuario
$pass = '';      // Ajusta según tu contraseña
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

    // Buscar id_producto y precio en la tabla PRODUCTO
    $stmt = $pdo->prepare("SELECT id_producto, precio FROM PRODUCTO WHERE nombre = ?");
    $stmt->execute([$producto]);
    $prod = $stmt->fetch();
    if($prod){
        $id_producto = $prod['id_producto'];
        $total = $prod['precio'] * $unidades;

        // Aquí asumo id_cliente = 1 para ejemplo, puedes crear formulario de cliente
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

    // Revisar si ya existe en carrito
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
/* ...Aquí va tu CSS original ... */
</style>
</head>
<body>
<h1>Gadget Store</h1>
<button id="cartBtn" style="margin-left:20px;padding:10px 20px;border-radius:10px;background:#00796B;color:white;border:none;cursor:pointer;">🛒 Ver Carrito (<?php echo count($_SESSION['carrito']); ?>)</button>
<div id="results-container"></div>

<div id="cartDiv">
<h3>Carrito de Compras</h3>
<button id="closeCart">Cerrar Carrito</button>
<ul>
<?php 
$total=0; 
foreach($_SESSION['carrito'] as $item){ 
    $subtotal=$item['precio']*$item['cantidad']; 
    $total+=$subtotal;
    echo "<li>{$item['producto']} x {$item['cantidad']} = \${$subtotal}</li>"; 
} 
?>
</ul>
<p><strong>Total: $<?php echo $total; ?></strong></p>
<form method="POST"><button name="vaciar_carrito">Vaciar Carrito</button></form>
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
/* Tu JS original para mostrar productos y carrito */
const products=[
{id:1,name:"Notebook Gamer",price:1200000},
{id:2,name:"Computador Oficina",price:800000},
{id:3,name:"Smartphone Samsung",price:600000},
{id:4,name:"Smartphone Iphone",price:450000},
{id:5,name:"Auriculares Bluetooth",price:50000},
{id:6,name:"Mouse Gamer",price:35000}];
const results=document.getElementById("results-container");
function displayProducts(){results.innerHTML="";products.forEach(p=>{
let div=document.createElement("div");div.className="product";
div.innerHTML=`<h3>${p.name}</h3><p>Precio: $${p.price}</p>
<form method="POST">
<input type="hidden" name="producto" value="${p.name}">
<input type="hidden" name="precio" value="${p.price}">
<input type="number" name="cantidad" value="1" min="1">
<button name="agregar_carrito">Agregar al Carrito</button>
</form>`;results.appendChild(div);
});}
displayProducts();
document.getElementById("cartBtn").onclick=()=>{document.getElementById("cartDiv").classList.add("show");}
document.getElementById("closeCart").onclick=()=>{document.getElementById("cartDiv").classList.remove("show");}
</script>
</body>
</html>
