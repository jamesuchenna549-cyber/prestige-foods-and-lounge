import {newCart} from './03-cart.js';
export let checkoutContainer;

export function updateCheckOutPage(){
checkoutContainer= document.querySelector('.checkout-product-container');
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
             
            </div> 
       
        
<div class
="checkout-action-button">

    <div>       
 <p class='quantity'>
Quantity 
</p>
 </div>

<div>   
 <p class='quantity'>
 : ${item.quantity}
</p>
   </div>           
            
          
              
            </div>  
          
    
          
        
        
        
         
       </div><!-- product details div-->
     
   
     
     
</div><!-- menu category -->

    
    
    
    `
  })
  checkoutContainer.innerHTML=html;
}


























