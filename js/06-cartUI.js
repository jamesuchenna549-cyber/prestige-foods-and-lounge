import {newCart} from './03-cart.js';

export let cartContainer;

export function renderCartUI(){
  cartContainer=document.querySelector('.products-container');
  
  let html='';
  newCart.forEach(function(item){
    html+=`
    <div  class="${item.category} menu-category">
     
     <div class="product-image-container">
       <img src="${item.image}" alt="${item.name}">
       </div>
       
       <div class="product-details-container">
        <div class="name-and-price">
          <div>
            
           <h2 class="product-name">
            ${item.name}
            </h2>  
            
              
           <p class="product-price price">
              ₦${item.price.toLocaleString()}
            </p>
        </div>
             
               <div class="cart-page-remove">
     
            <button class="remove-button" 
            data-id="${item.id}">
                  Remove 
             </button>
     </div>  
     
       
        </div>
         
            <div class="action-buttons">
            <button class="decrease-button" 
            data-id="${item.id}">
                  -
             </button>
             
       <p id='quantity'>${item.quantity}</p>
              
            
            <button class="increase-button" 
            data-id="${item.id}">
                  +
             </button>
             
              
            </div>  
          
    
          
        
        
        
         
       </div><!-- product details div-->
     
   
     
     
</div><!-- menu category -->

      
 


 
    
    
    `
  })
  cartContainer.innerHTML=html;
    
}