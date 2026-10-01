import {newCart} from './03-cart.js';
import {
  productContainer,
currentProducts,
  products,
  renderUI,
  setCurrentProducts
} from './01-products.js';
import {cartContainer, renderCartUI} from './06-cartUI.js';
import {checkoutContainer, updateCheckOutPage} from './08-check-out-page.js';



function addToCart(productId){

  let product = currentProducts.find(function(item){
    return Number(item.id) === Number(productId);
  });

  if(!product){
    console.log("Product not found:", productId);
    return;
  }

  let productInNewCart = newCart.find(function(item){
    return Number(item.id) === Number(productId);
  });

  if(productInNewCart){

    productInNewCart.quantity++;

  }else{

    newCart.push({
      id: product.id,
      image: product.image,
      name: product.name,
      description: product.description,
      price: Number(product.price),
      quantity: 1
    });

  }

  updateUi();
}


export function updateCartQuantity(){
let quantity=document.querySelector('.cart-quantity-icon');
if(!quantity){
  return;
}
  let totalQuantity=0;
  newCart.forEach(function(item){
    totalQuantity+=item.quantity;
  })
  quantity.textContent=totalQuantity;
  console.log(totalQuantity);
}

export function eventListener(){
  productContainer.addEventListener('click', function(event){
if(event.target.classList.contains('add-button')){
    let productId=event.target.dataset.id;
      addToCart(productId);
      let card=event.target.closest('.menu-category');
     let added=card.querySelector('.btn-text')
     
     added.classList.add('show')
     setTimeout(function(){
       added.classList.remove('show');
     },1000)
    console.log(JSON.stringify(newCart));
    
  }
  
});

}
function decreaseBtn(productId){
  let item =newCart.find(function(item){
    return Number(item.id)===Number(productId);
  });
 if(!item){
   return;
 }
 if(item && item.quantity >1){
   item.quantity--;
 }else{
   removeBtn(productId);
 }
 
}

function increaseBtn(productId){
  let item =newCart.find(function(item){
    return Number(item.id)===Number(productId);
  });
 if(!item){
   return;
 }
 if(item){
   item.quantity++;
 }
  
}

function removeBtn(productId){
  let itemIndex=newCart.findIndex(function(item){
    return Number(item.id)===Number(productId);
  });
 
  if(itemIndex!==-1){
    newCart.splice(itemIndex,1);
  }
  
}

function getQty(productId){
  
  let product=newCart.find(function(product){
    return Number(product.id)===Number(productId);
  });
  
  if(product){
 return product.quantity;
  }
  return 0;
 
}


export function eventListener2(){
  cartContainer.addEventListener('click',function(event){
  
  if(event.target.classList.contains('increase-button')){
    let productId=event.target.dataset.id;
    
    increaseBtn(productId);
updateUi();
    
  } 
  
  if(event.target.classList.contains('decrease-button')){
    let productId=event.target.dataset.id;
    decreaseBtn(productId);
updateUi();
  } 
  
  
  if(event.target.classList.contains('remove-button')){
    let productId=event.target.dataset.id;
    removeBtn(productId);
updateUi();

  } 
  })
}


export function updateUi() {
  localStorage.setItem('newCart', JSON.stringify(newCart));

  updateCartQuantity();

  if (cartContainer) {
    renderCartUI();

    let total = document.querySelector('.cart-subtotal');

    if (total) {
      total.textContent = "₦" + getSubTotal().toLocaleString();
    }
  }
  
  if(checkoutContainer){
    updateCheckOutPage();
    
    let checkoutTotal = document.querySelector('.subTotal');
     if (checkoutTotal) {
      checkoutTotal.textContent = "₦" + getSubTotal().toLocaleString();
    }
    const discount=Number(1000);
  const deliveryFee=Number(1500);
  
     let deliveryFees= document.querySelector('.delivery-fee');
    let discounts= document.querySelector('.discount');
    let totalCart= document.querySelector('.checkout-total');
     
     deliveryFees.textContent="₦" +deliveryFee.toLocaleString();
     discounts.textContent=
      "- ₦" + discount.toLocaleString();
     totalCart.textContent="₦" + getTotal().toLocaleString();
  }
}



function getSubTotal(){
let subTotal=0;
  newCart.forEach(function(item){
    subTotal+=item.quantity*item.price;
  })
  return subTotal;
}


function getTotal() {
  const discount=Number(1000);
  const deliveryFee=Number(1500);
let  total=(getSubTotal()+deliveryFee)-discount;
return total;
}





export function persistColor() {
  let selectedCategory =
    document.querySelectorAll('.menu');

  selectedCategory.forEach(function(button) {

    button.addEventListener('click', function() {

    selectedCategory.forEach(function(item) {
        item.classList.remove('selected');
      });

      button.classList.add('selected');

    });

  });
}

export function getData(){

  let searchInput =
    document.getElementById('search-input');

  let searchButton =
    document.getElementById('search-button');

  searchButton.addEventListener('click', async function(event){

    event.preventDefault();

    const search = searchInput.value.trim();

    let response = await fetch(
      "backend/search.php?search=" +
      encodeURIComponent(search)
    );

    const searchResult = await response.json();

    console.log(searchResult);
setCurrentProducts(searchResult);
    renderUI(searchResult);

  });

}

























