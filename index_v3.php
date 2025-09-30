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
    file_put_contents("pedidos3.txt","{$_POST['descripcion']}|{$_POST['tipo']}|{$_POST['producto']}|{$_POST['unidades']}|{$_POST['observaciones']}\n", FILE_APPEND);
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
<title>Electronix Hub – Versión 3</title>
<style>
body{font-family:Tahoma,sans-serif;margin:0;background:#fffde7;color:#33691e;}
h1{text-align:center;color:#689f38;}
#results-container{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:20px;padding:15px;}
.product{background:#f1f8e9;padding:10px;border-radius:12px;text-align:center;box-shadow:0 4px 12px rgba(0,0,0,0.2);transition:0.3s;}
.product:hover{transform:scale(1.05);box-shadow:0 8px 20px rgba(0,0,0,0.3);}
.product button{background:#689f38;color:white;border:none;padding:6px 10px;border-radius:5px;cursor:pointer;}
.product button:hover{background:#33691e;}
#cartDiv{position:fixed;top:50px;right:20px;width:280px;background:#689f38;padding:15px;color:white;display:none;z-index:1000;border-radius:10px;}
#cartDiv.show{display:block;}
#closeCart{background:#c62828;padding:5px 10px;border:none;border-radius:5px;cursor:pointer;float:right;}
</style>
</head>
<body>

<h1>Electronix Hub</h1>
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
const products=[
  {id:1,name:"Notebook Gamer",price:1200000},
  {id:2,name:"Computador Oficina",price:800000},
  {id:3,name:"Smartphone Samsung",price:600000},
  {id:4,name:"Smartphone Iphone",price:450000},
  {id:5,name:"Auriculares Bluetooth",price:50000},
  {id:6,name:"Mouse Gamer",price:35000}
];

const results=document.getElementById("results-container");

function displayProducts(){
  results.innerHTML="";
  products.forEach(p=>{
    let div=document.createElement("div"); 
    div.className="product";
    div.innerHTML=`
      <h3>${p.name}</h3>
      <p>Precio: $${p.price}</p>
      <form method="POST">
        <input type="hidden" name="producto" value="${p.name}">
        <input type="hidden" name="precio" value="${p.price}">
        <input type="number" name="cantidad" value="1" min="1">
        <button name="agregar_carrito">Agregar al Carrito</button>
      </form>`;
    results.appendChild(div);
  });
}

displayProducts();

document.getElementById("cartBtn").onclick=()=>{document.getElementById("cartDiv").classList.add("show");}
document.getElementById("closeCart").onclick=()=>{document.getElementById("cartDiv").classList.remove("show");}
</script>
</body>
</html>
