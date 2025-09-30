// Productos con palabras clave
const products = [
  {id: 1, name: "Notebook Gamer", category: "Notebooks", keywords: ["notebook", "gaming"], price: 1200000},
  {id: 2, name: "Computador Oficina", category: "Notebooks", keywords: ["notebook", "oficina"], price: 800000},
  {id: 3, name: "Smartphone Samsung", category: "Smartphone", keywords: ["celular", "teléfono"], price: 600000},
  {id: 4, name: "Smartphone Iphone", category: "Smartphone", keywords: ["celular", "teléfono"], price: 450000},
  {id: 5, name: "Auriculares Bluetooth", category: "Accesorios", keywords: ["audio", "auriculares"], price: 50000},
  {id: 6, name: "Mouse Gamer", category: "Accesorios", keywords: ["mouse", "gaming"], price: 35000},
];

let cart = [];

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
  cartDiv.classList.toggle("hidden");
  paymentForm.classList.add("hidden"); // cerrar formulario si estaba abierto
});

document.getElementById("closeCart").addEventListener("click", () => {
  cartDiv.classList.add("hidden");
  paymentForm.classList.add("hidden");
});

// Agregar al carrito
function addToCart(id) {
  const product = products.find(p => p.id === id);
  cart.push(product);
  updateCart();
}

// Actualizar carrito
function updateCart() {
  cartItems.innerHTML = "";
  let total = 0;
  cart.forEach(item => {
    total += item.price;
    const li = document.createElement("li");
    li.textContent = `${item.name} - $${item.price}`;
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
  cartDiv.classList.add("hidden");
});

