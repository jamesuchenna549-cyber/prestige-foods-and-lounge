<?php 
session_start();

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Home Page</title>

  <link rel="stylesheet" href="../style/01-homePageStyle.css">
  <link rel="stylesheet" href="../style/02-cart-page-style.css">
  <link rel="stylesheet" href="../style/03-checkout-page.css">
  <link rel="stylesheet" href="../style/04-user-page-style.css">
 <link rel="stylesheet" href="../style/footer-style.css">


</head>


<body style="background:
        linear-gradient(
            135deg,
            #1d353a,
            #263f43,
            #182b2f
        )";>

  <div class="general-container">


 

    <div id="general-navigation-container" class="homepage-header">

    <div class="homepage-brand">

        <h1>Prestige</h1>
        <p>Foods And Lounge</p>

    </div>

    <div class="welcome-user">

        <span>Welcome</span>

        <?php 
        echo $_SESSION["user_email"]; 
        ?>

    </div>

</div>

      
      


    

    <section class="hero">

      <div class="hero-content">

        <h1>
          Delicious Food Delivered to Y<span>our Door!</span>
        </h1>


        <p>
          Hot and fresh meals delivered fast!
        </p>


 <div class="homepage-hero-button">
          
        
         
        
        
  <a href="02-cart-page.html">
             Order Now!
          </a>
        
         
          
         
         
       
       <a href="01-shop.html">
             View Menu
          </a>
          
     
         
      
    
           
   
        
        
          

          
     
        
        
          
        </div>

      
      </div>

    </section>

  

    <section class="featured-products">

    


      <!-- Top two cards -->
      <div class="featured-top">

        <div class="feature-card">

          <div class="feature-icon">
            <img src="../image/delivery-icon.png" alt="Home Delivery">
          </div>

          <div class="text">
            <a href="#">
                <h3>Home Delivery</h3>
            <p>Fast delivery to your door</p>
            </a>
          
          </div>

        </div>


        <div class="feature-card">

          <div class="feature-icon">
            <img src="../image/discount-icon.png" alt="Discounts">
                      </div>

          <div class="text">
            <a href="#">
                <h3>Discounts</h3>
            <p>Get amazing discounts</p>
            </a>
          
          </div>
          
          </div>
          
          
            <div class="feature-card">
           
                  <div class="feature-icon">
            <img src="../image/snooker-image.png" alt="Discounts">
                      </div>

          <div class="text">
            <a href="./snooker/snooker.php">
        <h3>Snooker Lounge</h3> <p>Register For A Competition Now!</p>
         
            </a>
           
          </div>
        
        </div>
     
    </div>

    
         
        
     

    </section>
  
  <footer class="prestige-footer">

  <div class="footer-section">

    
    <div class="footer-brand">
      <h3>
        <span class="deprestige-text">de</span>Prestige
      </h3>

      <p>FOODS & LOUNGE</p>

      <span class="footer-tagline">
        Good food. Great drinks. Good vibes.
      </span>
    </div>


   
    <div class="prestige-text">
      <ol>
        <li>Food</li>
        <li>Lounge</li>
        <li>Experience</li>
      </ol>
    </div>


 
    <div class="description">
      <p>
        Delicious meals. Great drinks.
        Good vibes. Unforgettable moments.
      </p>
    </div>


   
    <div class="footer-order">
      <a href="01-shop.html">
        Order Your Meal now!
      </a>
    </div>


    
    <div class="services">

      <h3>Services</h3>

      <ul>
        <li><a href="#">Food Delivery</a></li>
        <li><a href="#">Dine-In</a></li>
        <li><a href="#">Snooker</a></li>
        <li><a href="#">Special Offers</a></li>
      </ul>

    </div>


    
    <div class="services">

      <h3>Contact Us</h3>

      <p>☎ 07055176125</p>

      <p>
        📍 Opposite Shopp Moore,
        Auchi Edo State, Nigeria
      </p>

      <p>
        ✉
        <span class="deprestige-text">de</span>Prestige@gmail.com
      </p>

    </div>


    
    <section class="follow-us">

      <h4>Follow Us</h4>

      <div class="follow-card">

        <div class="feature-icon">
          <img src="../image/instagram.png" alt="Instagram">
        </div>

        <div class="text">
          <a href="#">
            <h3>Instagram</h3>
          </a>
        </div>

      </div>


      <div class="follow-card">

        <div class="feature-icon">
          <img src="../image/facebook.png" alt="Facebook">
        </div>

        <div class="text">
          <a href="#">
            <h3>Facebook</h3>
          </a>
        </div>

      </div>


      <div class="follow-card">

        <div class="feature-icon">
          <img src="../image/whatsapp.png" alt="WhatsApp">
        </div>

        <div class="text">
          <a href="#">
            <h3>WhatsApp</h3>
          </a>
        </div>

      </div>

    </section>


    
    <div class="final-message">

      <p>
        © 2026
        <span class="deprestige-text">de</span>Prestige
        Foods & Lounge
        <br>
        All Rights Reserved
      </p>

    </div>

  </div>

</footer>
  

       
  </div>

</body>



</html>
