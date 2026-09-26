export let products = [];
export let productContainer;
export let currentProducts = [];

export async function getProducts() {

  const response = await fetch(
    "http://0.0.0.0:8080/backend/products.php"
  );

  products = await response.json();

  currentProducts = products;

  renderUI(currentProducts);
}


export function renderUI(productsToDisplay = products) {

  productContainer =
    document.querySelector('.products-container');

  let html = '';

  productsToDisplay.forEach(function(product) {

    html += `
      <div class="${product.category} menu-category">

        <div class="product-image-container">
          <img src="${product.image}" alt="${product.name}">
        </div>

        <div class="product-details-container">

          <h2 class="product-name">
            ${product.name}
          </h2>

          <p class="product-discription">
            ${product.description}
          </p>

          <p class="product-price">
            ₦${Number(product.price).toLocaleString()}
          </p>

        </div>

        <div class="Add-to-cart-btn">

          <p class="btn-text">Added ✅</p>

          <button
            class="add-button"
            data-id="${product.id}">
            Add to Cart
          </button>

        </div>

      </div>
    `;
  });

  productContainer.innerHTML = html;
}


export function setCurrentProducts(newProducts) {
  currentProducts = newProducts;
}