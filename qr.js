const navlinks = document.querySelectorAll(".nav-menu .nav-link");
const menuOpenButton = document.querySelector("#menu-open-button");
const menuCloseButton = document.querySelector("#menu-close-button");

menuOpenButton.addEventListener("click",() => {
// toggle mobile menu visisbility
document.body.classList.toggle("show-mobile-menu");
});
//    this is used to for close button when clicked
  menuCloseButton.addEventListener("click",() => menuOpenButton.click());
  //    this is used to for close menu when nav link is clicked
 navlinks.forEach(link =>{
  link.addEventListener("click", () => menuOpenButton.click());
 });
/* ================================
   CATEGORY FILTER
================================ */
const categoryButtons = document.querySelectorAll(".categories button");
const menuItems = document.querySelectorAll(".menu-item");

categoryButtons.forEach(button => {
    button.addEventListener("click", () => {

        // Remove active class
        document.querySelector(".categories .active")?.classList.remove("active");
        button.classList.add("active");

        const category = button.getAttribute("data-category");

        menuItems.forEach(item => {
            if (item.getAttribute("data-category") === category || category === "all") {
                item.style.display = "block";
            } else {
                item.style.display = "none";
            }
        });

        // Smooth animation
        const menu = document.querySelector(".menu-container");
        menu.style.opacity = 0;
        setTimeout(() => { menu.style.opacity = 1 }, 200);
    });
});


/* ================================
   CART SYSTEM
================================ */

const cartToggle = document.getElementById("cartToggle");
const cartSidebar = document.getElementById("cartSidebar");
const cartItemsContainer = document.querySelector(".cart-items");
const cartTotalElement = document.querySelector(".cart-total");
const cartBadge = document.getElementById("cartCount");

let cart = [];

// Open/Close cart sidebar
cartToggle.addEventListener("click", () => {
    cartSidebar.classList.toggle("open");
});


/* ================================
   ADD TO CART FUNCTIONALITY
================================ */

document.querySelectorAll(".add-btn").forEach((btn) => {
    btn.addEventListener("click", () => {

        const item = btn.closest(".menu-item");
        const name = item.querySelector("h3").innerText;
        const price = parseInt(item.querySelector(".price").innerText.replace("Rs ", ""));
        const imgSrc = item.querySelector("img").src;

        let existingItem = cart.find(i => i.name === name);

        if (existingItem) {
            existingItem.quantity++;
        } else {
            cart.push({
                name: name,
                price: price,
                imgSrc: imgSrc,
                quantity: 1
            });
        }

        updateCart();
    });
});
function updateCart() {
    cartItemsContainer.innerHTML = "";
    let total = 0;

    cart.forEach((item, index) => {
        total += item.price * item.quantity;

        cartItemsContainer.innerHTML += `
            <div class="cart-item">
                <img src="${item.imgSrc}">
                
                <div class="cart-details">
                    <h4>${item.name}</h4>

                    <div class="qty-box">
                        <button class="qty-btn decrease" onclick="decreaseQty(${index})">-</button>
                        <span class="qty-number">${item.quantity}</span>
                        <button class="qty-btn increase" onclick="increaseQty(${index})">+</button>
                    </div>

                    <p>Price: Rs ${item.quantity * item.price}</p>
                </div>

                <button class="remove-btn" onclick="removeItem(${index})">X</button>
            </div>
        `;
    });

    cartTotalElement.innerText = `Total: Rs ${total}`;
    updateCartBadge();
}
/* ================================
   REMOVE ITEM FROM CART
================================ */
function removeItem(index) {
    cart.splice(index, 1);
    updateCart();
}
function increaseQty(index) {
    cart[index].quantity++;
    updateCart();
}

function decreaseQty(index) {
    if (cart[index].quantity > 1) {
        cart[index].quantity--;
    } else {
        cart.splice(index, 1);
    }
    updateCart();
}


/* ================================
   UPDATE CART BADGE (🛒 NUMBER)
================================ */
function updateCartBadge() {

    let totalQuantity = 0;

    cart.forEach(item => {
        totalQuantity += item.quantity;
    });

    cartBadge.innerText = totalQuantity;
}
/* ================================
   CHECKOUT SYSTEM
================================ */

const checkoutBtn = document.getElementById("checkoutBtn");

checkoutBtn.addEventListener("click", function () {

    if (cart.length === 0) {
        alert("Your cart is empty!");
        return;
    }

    const tableNo = document.getElementById("tableNo").value;
    const paymentMethod = document.getElementById("paymentMethod").value;

    console.log("TABLE NUMBER =", tableNo);
    console.log("PAYMENT METHOD =", paymentMethod);

    if (tableNo === "") {
        alert("Please enter your table number.");
        return;
    }

    if (Number(tableNo) <= 0) {
        alert("Please enter a valid table number.");
        return;
    }

    if (paymentMethod === "") {
        alert("Please select a payment method.");
        return;
    }

    const orderData = {
        table_no: Number(tableNo),
        payment_method: paymentMethod,
        cart_items: cart
    };

    console.log("ORDER DATA =", orderData);

    fetch("place-order.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(orderData)
    })
    .then(response => response.text())
    .then(data => {

        console.log("SERVER RESPONSE =", data);

        showSuccessCard(data);

        if (data.includes("Order saved successfully")) {
            cart = [];
            updateCart();
        }

    })
    .catch(error => {
        console.error("CHECKOUT ERROR =", error);
        alert("Error placing order.");
    });

});

// initialize swiper
 const swiper = new Swiper('.slider-wrapper', {
  loop: true,
  grabCursor: true,
  spaceBetween: 25,

  // If we need pagination
  pagination: {
    el: '.swiper-pagination',
    clickable: true,
    dynamicBullets: true,
  },

  // Navigation arrows
  navigation: {
    nextEl: '.swiper-button-next',
    prevEl: '.swiper-button-prev',
  },
  // responsive breakpoints
  breakpoints: {
    0: {
      slidesPerView: 1
    },
     768: {
      slidesPerView: 2
    },
     1024: {
      slidesPerView: 3
    }
  }
  });
  function showSuccessCard(message) {
    // Create the card element
    const card = document.createElement('div');
    
    // Style it to match your website theme
    card.style.cssText = `
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%) translateY(-100px);
        background: #281C02;
        color: #FFD700;
        padding: 20px 30px;
        border-radius: 12px;
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        font-size: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        z-index: 99999;
        text-align: center;
        transition: transform 0.4s ease, opacity 0.4s ease;
        opacity: 0;
        border: 2px solid #FFD700;
    `;
    
    // Add a checkmark icon and the message
    card.innerHTML = `<span style="font-size:24px; display:block; margin-bottom:5px;">✅</span> ${message}`;
    
    document.body.appendChild(card);
    
    // Slide it down
    setTimeout(() => {
        card.style.transform = 'translateX(-50%) translateY(0)';
        card.style.opacity = '1';
    }, 50);
    
    // Slide it back up and remove after 4 seconds
    setTimeout(() => {
        card.style.transform = 'translateX(-50%) translateY(-100px)';
        card.style.opacity = '0';
        setTimeout(() => card.remove(), 400);
    }, 4000);
}