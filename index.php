<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant website</title>
    <!-- linkking font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
     <!-- linking swiper CSS for -->
    <link rel="stylesheet"  href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css">
    <link rel="stylesheet" href="qr.css?v=2">
</head>
<body>
 <!-- header /navbar -->
   <header>
        <nav class="navbar section-content">
            <a href="#"  class="nav-logo">
            <h2 class="logo-text">QR FOOD HUB</h2>
            </a>
          <ul class="nav-menu">
    <button id="menu-close-button" class="fas fa-times"></button>

    <li class="nav-item">
        <a href="#" class="nav-link">Home</a>
    </li>

    <li class="nav-item">
        <a href="#about" class="nav-link">About</a>
    </li>

    <li class="nav-item">
        <a href="#menu" class="nav-link">Menu</a>
    </li>

    <li class="nav-item">
        <a href="#testimonials" class="nav-link">Testimonials</a>
    </li>

    <li class="nav-item">
        <a href="#contact" class="nav-link">Contact</a>
    </li>

    <!-- Admin Panel -->
    <li class="nav-item">
        <a href="login.php" class="nav-link">Admin Panel</a>
    </li>
</ul>

<button id="menu-open-button" class="fas fa-bars"></button>
        </nav>
    </header>
    <main>
        <!-- hero section -->
<section class="hero-section">
  <div class="section-content">
    
    <!-- LEFT SIDE TEXT -->
    <div class="hero-details">
      <h2 class="titles">Delicious Food</h2>
      <h3 class="subtitle"> Scan, Savor, Smile </h3>
      <p class="description">"Welcome to QR Food Hub,
        where flavors meet convenience!  Dine in, takeout, or delivery
        we've got you covered. Explore our menu, order with a scan,and
        enjoy your food your way. From local favorites to global
        cuisines, every bite is crafted with love."
      </p>

      <div class="buttons">
        <a href="#menu" class="button order-now">Order Now</a>
        <a href="#contact" class="button contact-us">Contact Us</a>
      </div>
    </div>

    <!-- RIGHT SIDE VIDEO CARD -->
    <div class="video-card">
      <video src="BG (2).mp4" autoplay loop muted playsinline></video>
    </div>

  </div>
</section>
     <!-- About section -->
      <section  class="about-section" id="about">
        <div  class="section-content">
        <div class="about-image-wrapper">
            <img src="images/about.jpg" alt="About" class="about-image">
        </div>
      <div class="about-details">
        <h2 class="section-title">About Us</h2>
        <p class="text">At QR Food Hub, we're passionate about serving up more than just delicious food 
           we're serving a seamless dining experience. Our QR Restaurant System is designed to make your visit effortless,
            from ordering to payment, and everything in between. With just a scan, you can dive into our menu, customize 
            your meal,and pay on the go.No more waiting for waiters, no more hassle with cash or cards.
            Our mission? To make dining out easier, faster, and more fun for you. We believe food is best enjoyed without
             fuss, so we're putting the power in your hands (or rather, your phone). Whether you're a foodie, a busy pro,
              or just love trying new eats, we're here to make every meal a breeze.
        </p>
        <div class="social-link-list">
          <a href="#" class="social-link"><i class="fa-brands fa-facebook"></i></a>
          <a href="#" class="social-link"><i class="fa-brands fa-instagram"></i></a>
          <a href="#" class="social-link"><i class="fa-brands fa-twitter"></i></a>
        </div>
      </div>
      </div>
    </section>
    <!-- Menu Section -->
        <h2 class="section-title">Explore Menu</h2>
    <!-- Categories -->
    <div class="categories">
        <button class="active" data-category="all">All</button>
       <button data-category="burgers">Burgers</button>
        <button data-category="pizza">Pizza</button>
        <button data-category="pasta">Pasta</button>
        <button data-category="desserts">Desserts</button>
        <button data-category="drinks">Drinks</button>
        <button data-category="deals">Deals</button>
    </div>
    <?php
include "db.php";

$sql = "SELECT * FROM menu WHERE availability = 1 ORDER BY id ASC";
$result = $conn->query($sql);
?>
<!-- Menu Items -->
<section class="menu-container" id="menu">

<?php
if ($result && $result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {
?>

    <div class="menu-item" data-category="<?php echo strtolower($row['category']); ?>">

        <img src="images/<?php echo htmlspecialchars($row['image']); ?>" 
             alt="<?php echo htmlspecialchars($row['item_name']); ?>">

        <h3><?php echo htmlspecialchars($row['item_name']); ?></h3>

        <p><?php echo htmlspecialchars($row['description']); ?></p>

        <span class="price">
            Rs <?php echo number_format($row['price'], 0); ?>
        </span>

        <button class="add-btn">Add to Cart</button>

    </div>

<?php
    }

} else {
    echo "<p>No menu items available.</p>";
}
?>

</section>
       
        <!-- Cart Sidebar -->
<div class="cart-sidebar" id="cartSidebar">
    <h2>Your Cart</h2>

    <div class="cart-items"></div>
<div class="cart-footer">

    <p class="cart-total">Total: Rs 0</p>

    <div class="checkout-field">
        <label for="tableNo">Table Number</label>
        <input 
    type="number" 
    id="tableNo" 
    min="1" 
    placeholder="Enter table number"
    value="<?php echo isset($_GET['table']) ? (int)$_GET['table'] : ''; ?>"
    <?php if (isset($_GET['table'])) echo 'readonly'; ?>>
    </div>

    <div class="checkout-field">
        <label for="paymentMethod">Payment Method</label>
        <select id="paymentMethod">
            <option value="">Select Payment Method</option>
            <option value="Cash">Cash</option>
            <option value="Card">Card</option>
            <option value="Online Payment">Online Payment</option>
        </select>
    </div>

    <button id="checkoutBtn" class="checkout-btn">Checkout</button>

</div>
  
</div>

<!-- Cart Toggle Button -->
<button class="cart-toggle" id="cartToggle">
    🛒
    <span id="cartCount" class="cart-count">0</span>
</button>
    </section>
     <!-- testimonial section -->
    <section class="testimonials-section" id="testimonials">
    <h2 class="section-title">Testimonials</h2>
    <div class="section-content">
      <div  class="slider-container swiper">
        <div class="slider-wrapper">
          <ul class="testimonials-list swiper-wrapper">
             <li class="testimonial swiper-slide">
                <img src="images/user-1.jpg" alt="User" class="user-image">
                <h3 class="name">Sarah Siddiqui</h3>
                <i class="feedback">"Loved the French roast.Perfectly
                  balanced and rich.Will order again Inshallah!"</i>
             </li>
             <li class="testimonial swiper-slide">
                <img src="images/user-2.jpg" alt="User" class="user-image">
                <h3 class="name">Danish Khan</h3>
                <i class="feedback">"Their cold coffee is just a masterpiece. 
                  Strongly Recommended! Cozy Environment,just Loved it!"</i>  
             </li>
             <li class="testimonial swiper-slide">
                <img src="images/user-3.jpg" alt="User" class="user-image">
                <h3 class="name">Shayaan Ali</h3>
                <i class="feedback">"Great Choclate lava Cake! Smooth and bold
                  flavor. Fast shipping too!" </i>
             </li>
             <li class="testimonial swiper-slide ">
                <img src="images/user-4.jpg" alt="User" class="user-image">
                <h3 class="name">Aish Khan</h3>
                <i class="feedback">"Fantastic deals!Fresh, Affordable and 
                  aromatic.Quick shipping"</i>
             </li>
             <li class="testimonial swiper-slide">
                <img src="images/user-5.jpg" alt="User" class="user-image">
                <h3 class="name">Kazal Arslan</h3>
                <i class="feedback">"Best decaf I've tried! Smooth and
                  flavorful. Great Ambiance."</i>
             </li> 
          </ul>
                <!-- these lines of code is only for slider view for testimonial section -->
          <div class="swiper-pagination"></div>
          <div class="swiper-slide-button swiper-button-prev"></div>
          <div class="swiper-slide-button swiper-button-next"></div>
        </div> 
      </div>
    </div>
    </section>
    <!-- Contact Section -->
<section class="contact-section" id="contact">
    <div class="section-content">
        <h2 class="section-title">Contact Us</h2>

        <div class="contact-wrapper">

            <!-- Left: Contact Info -->
            <div class="contact-info">
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <h3>Our Location</h3>
                        <p>College Chowk, Ghazi University Road,<br>Dera Ghazi Khan</p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <h3>Phone</h3>
                        <p>+92 300 1234567</p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <h3>Email</h3>
                        <p>hello@qrfoodhub.com</p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <h3>Opening Hours</h3>
                        <p>Monday – Sunday<br>11:00 AM – 11:00 PM</p>
                    </div>
                </div>
            </div>

            <!-- Right: Contact Form -->
            <form class="contact-form" action="contact.php" method="POST">
                <h3>Send us a Message</h3>

                <div class="form-field">
                    <label>Your Name</label>
                    <input type="text" name="name" placeholder="Enter your name" required>
                </div>

                <div class="form-field">
                    <label>Your Email</label>
                    <input type="email" name="email" placeholder="Enter your email" required>
                </div>

                <div class="form-field">
                    <label>Message</label>
                    <textarea name="message" rows="5" placeholder="Write your message here..." required></textarea>
                </div>

                <button type="submit" class="contact-btn">Send Message</button>
            </form>

        </div>
    </div>
</section>
<!-- Footer -->
<footer class="site-footer">
    <div class="footer-content">

        <div class="footer-col">
            <h2 class="footer-logo">QR FOOD HUB</h2>
            <p>Scan, Savor, Smile. A modern dining experience at your fingertips.</p>
            <div class="footer-socials">
                <a href="#"><i class="fa-brands fa-facebook"></i></a>
                <a href="#"><i class="fa-brands fa-instagram"></i></a>
                <a href="#"><i class="fa-brands fa-twitter"></i></a>
            </div>
        </div>

        <div class="footer-col">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#menu">Menu</a></li>
                <li><a href="#testimonials">Testimonials</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h3>Opening Hours</h3>
            <p>Monday – Sunday</p>
            <p>11:00 AM – 11:00 PM</p>
            <p class="footer-note">Dine-in • Takeaway • Delivery</p>
        </div>

        <div class="footer-col">
            <h3>Visit Us</h3>
            <p>College Chowk,<br>Ghazi University Road,<br>Dera Ghazi Khan</p>
            <p>+92 300 1234567</p>
        </div>

    </div>

    <div class="footer-bottom">
        <p>© <?php echo date("Y"); ?> QR Food Hub. All rights reserved.</p>
    </div>
</footer>
    </main>
    <!-- Linking Swiper script -->
     <script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js">
     </script>
     <!-- linking custom script -->
      <script src="qr.js"></script>
      
</body>
</html>