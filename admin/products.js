const addProductButton = document.querySelector('.add-product-button');
const addProductForm = document.querySelector('.add-product-form');

const productFormTitle = addProductForm.querySelector('.product-form-title');
const productFormButton = addProductForm.querySelector('.product-form-button');

const productsContainer = document.querySelector('.products-container');
const productsMessage = document.querySelector('.products-message');

let products = [];
let editingProductId = null;

addProductForm.style.display = 'none';


addProductButton.addEventListener('click', function() {
    if (addProductForm.style.display === 'none') {
        editingProductId = null;
        addProductForm.reset();

        productFormTitle.textContent = 'Add Product';
        productFormButton.textContent = 'Save Product';

        addProductForm.style.display = 'block';
    } else {
        addProductForm.style.display = 'none';
    }
});


addProductForm.addEventListener('submit', async function(event) {
    event.preventDefault();

    const name = addProductForm.querySelector('input[name="name"]').value.trim();
    const description = addProductForm.querySelector('textarea[name="description"]').value.trim();
    const price = addProductForm.querySelector('input[name="price"]').value;
    const category = addProductForm.querySelector('input[name="category"]').value.trim();
    const image = addProductForm.querySelector('input[name="image"]').value.trim();

    const formData = new FormData();

    formData.append('name', name);
    formData.append('description', description);
    formData.append('price', price);
    formData.append('category', category);
    formData.append('image', image);

    try {
        const url = editingProductId
            ? '../backend/edit_product.php'
            : '../backend/add_product.php';

        if (editingProductId) {
            formData.append('id', editingProductId);
        }

        const response = await fetch(url, {
            method: 'POST',
            body: formData,
            credentials: 'include'
        });

        const result = await response.json();

        const productMessage = addProductForm.querySelector('.product-message');

        productMessage.textContent = result.message;
        productMessage.classList.add('admin-show-message');

        if (result.success) {
            addProductForm.reset();
            editingProductId = null;

            productFormTitle.textContent = 'Add Product';
            productFormButton.textContent = 'Save Product';

            getProducts();
        }

    } catch (error) {
        console.log('Product form error:', error);

        const productMessage = addProductForm.querySelector('.product-message');

        productMessage.textContent = 'Unable to connect to the server.';
        productMessage.classList.add('admin-show-message');
    }
});


async function getProducts() {
    try {
        const response = await fetch('../backend/products.php');

        products = await response.json();

        console.log(products);

        let html = '';

        products.forEach(function(product) {
            html += `
                <div class="admin-product">
                    <img src="${product.image}" alt="${product.name}">

                    <h2>${product.name}</h2>

                    <p>
                        Price: ₦${Number(product.price).toLocaleString()}
                    </p>

                    <p>
                        Category: ${product.category}
                    </p>

                    <div class="product-actions">
                        <button class="edit-product" data-id="${product.id}">
                            Edit
                        </button>

                        <button class="delete-product" data-id="${product.id}">
                            Delete
                        </button>
                    </div>
                </div>
            `;
        });

        productsContainer.innerHTML = html;

    } catch (error) {
        console.log('Products error:', error);

        productsContainer.textContent = 'Unable to load products.';
    }
}


getProducts();


productsContainer.addEventListener('click', function(event) {
    if (!event.target.classList.contains('edit-product')) {
        return;
    }

    const productId = event.target.dataset.id;

    editingProductId = productId;

    const product = products.find(function(item) {
        return Number(item.id) === Number(productId);
    });

    if (!product) {
        return;
    }

    productFormTitle.textContent = 'Edit Product';
    productFormButton.textContent = 'Update Product';

    addProductForm.style.display = 'block';

    addProductForm.querySelector('input[name="name"]').value = product.name;
    addProductForm.querySelector('textarea[name="description"]').value = product.description;
    addProductForm.querySelector('input[name="price"]').value = product.price;
    addProductForm.querySelector('input[name="category"]').value = product.category;
    addProductForm.querySelector('input[name="image"]').value = product.image;

    addProductForm.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
    });
});


productsContainer.addEventListener('click', async function(event) {
    if (!event.target.classList.contains('delete-product')) {
        return;
    }

    const productId = event.target.dataset.id;

    const formData = new FormData();

    formData.append('id', productId);

    try {
        const response = await fetch('../backend/delete_product.php', {
            method: 'POST',
            body: formData,
            credentials: 'include'
        });

        const result = await response.json();

        productsMessage.textContent = result.message;
        productsMessage.classList.add('admin-show-message');

        if (result.success) {
            getProducts();
        }

    } catch (error) {
        console.log('Delete product error:', error);

        productsMessage.textContent = 'Unable to connect to the server.';
        productsMessage.classList.add('admin-show-message');
    }
});





const logoutLink = document.querySelector('.logout-link');

logoutLink.addEventListener('click', async function(event) {
    event.preventDefault();

    try {
        const response = await fetch('../backend/admin_logout.php', {
            credentials: 'include'
        });

        const result = await response.json();

        if (result.success) {
            window.location.href = 'index.html';
        }

    } catch (error) {
        console.log('Logout error:', error);
    }
});
















