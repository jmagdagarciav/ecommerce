<?php
session_start();
if(!isset($_SESSION['carrito'])) $_SESSION['carrito'] = [];

class Pedido {
    public $descripcion, $tipo, $producto, $unidades, $observaciones;
    public function __construct($d,$t,$p,$u,$o=""){
        $this->descripcion=$d; $this->tipo=$t; $this->producto=$p; $this->unidades=$u; $this->observaciones=$o;
    }
}

if(isset($_POST['registrar_pedido'])){
    $p=new Pedido($_POST['descripcion'],$_POST['tipo'],$_POST['producto'],$_POST['unidades'],$_POST['observaciones']);
    file_put_contents("pedidos2.txt","{$_POST['descripcion']}|{$_POST['tipo']}|{$_POST['producto']}|{$_POST['unidades']}|{$_POST['observaciones']}\n", FILE_APPEND);
}

if(isset($_POST['agregar_carrito'])){
    $_SESSION['carrito'][]=['producto'=>$_POST['producto'],'precio'=>$_POST['precio'],'cantidad'=>$_POST['cantidad']];
}

if(isset($_POST['vaciar_carrito'])) $_SESSION['carrito']=[];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tech World – Versión 2</title>
<style>
body{font-family:Arial, sans-serif;margin:0;background:#e0f7fa;color:#004d40;}
h1{text-align:center;color:#00796b;}
#results-container{display:grid;grid-template-columns:1fr;gap:12px;padding:15px;}
.product{display:flex;align-items:center;justify-content:space-between;background:linear-gradient(90deg,#80cbc4,#b2dfdb);padding:15px;border-radius:10px;color:#004d40;}
.product h3{margin:0;}
.product button{background:#004d40;color:white;border:none;padding:8px 12px;border-radius:5px;cursor:pointer;}
.product button:hover{background:#00695c;}
#cartDiv{position:fixed;left:0;top:0;width:320px;height:100%;background:#00796b;padding:15px;color:white;transform:translateX(-100%);transition:0.3s;overflow-y:auto;z-index:100;}
#cartDiv.show{transform:translateX(0);}
#closeCart{background:#c62828;padding:5px 10px;border:none;border-radius:5px;cursor:pointer;}
</style>
</head>
<body>

<h1>Tech World</h1>
<button id="cartBtn">🛒 Ver Carrito (<?php echo count($_SESSION['carrito']); ?>)</button>
<div id="results-container"></div>

<div id="cartDiv">
<h3>Carrito</h3>
<button id="closeCart">Cerrar</button>
<ul>
<?php $total=0; foreach($_SESSION['carrito'] as $item){ $subtotal=$item['precio']*$item['cantidad']; $total+=$subtotal;
echo "<li>{$item['producto']} x {$item['cantidad']} = \${$subtotal}</li>"; } ?>
</ul>
<p>Total: $<?php echo $total; ?></p>
<form method="POST"><button name="vaciar_carrito">Vaciar Carrito</button></form>
</div>

<h2>Registrar Pedido</h2>
<form method="POST">
<input type="text" name="descripcion" placeholder="Descripción" required>
<input type="text" name="tipo" placeholder="Tipo" required>
<input type="text" name="producto" placeholder="Producto" required>
<input type="number" name="unidades" placeholder="Unidades" required>
<textarea name="observaciones" placeholder="Observaciones"></textarea>
<button name="registrar_pedido">Registrar Pedido</button>
</form>

<script>
const products=[{id:1,name:"Notebook Gamer",price:1200000},{id:2,name:"Computador Oficina",price:800000},{id:3,name:"Smartphone Samsung",price:600000},{id:4,name:"Smartphone Iphone",price:450000},{id:5,name:"Auriculares Bluetooth",price:50000},{id:6,name:"Mouse Gamer",price:35000}];
const results=document.getElementById("results-container");
function displayProducts(){
results.innerHTML="";
products.forEach(p=>{
let div=document.createElement("div"); div.className="product";
div.innerHTML=`<h3>${p.name}</h3><form method="POST"><input type="hidden" name="producto" value="${p.name}"><input type="hidden" name="precio" value="${p.price}"><input type="number" name="cantidad" value="1" min="1"><button name="agregar_carrito">Agregar al Carrito</button></form>`;
results.appendChild(div);
});}
displayProducts();

document.getElementById("cartBtn").onclick=()=>{document.getElementById("cartDiv").classList.add("show");}
document.getElementById("closeCart").onclick=()=>{document.getElementById("cartDiv").classList.remove("show");}
</script>
</body>
</html>

