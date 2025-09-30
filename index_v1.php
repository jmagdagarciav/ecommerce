<?php
session_start();
if (!isset($_SESSION['carrito'])) $_SESSION['carrito'] = [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gadget Store</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
body{
    font-family: 'Poppins', sans-serif;
    margin:0;
    background: linear-gradient(135deg, #e0f7fa, #f0f2f5);
}
h1{
    text-align:center;
    color:#00796B;
    padding:20px 0;
}
#results-container{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(220px,1fr));
    gap:20px;
    padding:20px;
}
.product{
    background:white;
    padding:20px;
    border-radius:15px;
    text-align:center;
    box-shadow:0 10px 20px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}
.product:hover{
    transform: translateY(-5px);
    box-shadow:0 15px 25px rgba(0,0,0,0.15);
}
.product button{
    background:#00796B;
    color:white;
    border:none;
    padding:10px 15px;
    border-radius:10px;
    cursor:pointer;
    margin-top:10px;
    transition: background 0.3s;
}
.product button:hover{
    background:#004D40;
}
#cartDiv{
    position:fixed;
    right:0;
    top:0;
    width:320px;
    height:100%;
    background:#004D40;
    padding:20px;
    color:white;
    transform:translateX(100%);
    transition:0.3s;
    overflow-y:auto;
    z-index:100;
}
#cartDiv.show{transform:translateX(0);}
#cartDiv h3{text-align:center;margin-top:0;}
#closeCart{
    background:#D32F2F;
    padding:8px 12px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    width:100%;
    margin-bottom:15px;
}
form input, form textarea{
    width:100%;
    padding:10px;
    margin:5px 0;
    border-radius:8px;
    border:1px solid #ccc;
    font-family:'Poppins', sans-serif;
}
form button{
    width:100%;
    padding:12px;
    background:#00796B;
    color:white;
    border:none;
    border-radius:10px;
    font-weight:600;
    cursor:pointer;
}
form button:hover{
    background:#004D40;
}
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

