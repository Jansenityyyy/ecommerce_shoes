// Fetch Limited Product
fetch('php/fetch_limited.php')
  .then(res => res.json())
  .then(product => {
    if(product){
      const limitedImg = `src/img/${product.image}`;
      document.getElementById('limited-img').src = limitedImg;
      document.getElementById('limited-name').innerText = product.name;
      document.getElementById('limited-desc').innerText = product.description;

      const priceNum = parseFloat(product.price.toString().replace(/,/g, ''));
      const formattedPrice = priceNum.toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });
      document.getElementById('limited-price').innerText = `₱${formattedPrice}`;
      
      // Store limited product data for cart
      document.getElementById('shop-now').dataset.productId = product.id;
      document.getElementById('shop-now').dataset.brand = product.brand;
      
      if(product.end_date){
        document.getElementById('countdown-container').style.display = 'block';
        startCountdown(product.end_date);
      }
    } else {
      document.getElementById('limited-product').style.display = 'none';
    }
  })
  .catch(err => console.error('Error fetching limited product:', err));


// Countdown Timer
function startCountdown(endDate) {
  const end = new Date(endDate + ' 23:59:59').getTime();

  const timer = setInterval(() => {
    const now = new Date().getTime();
    const distance = end - now;

    if (distance < 0) {
      clearInterval(timer);
      document.getElementById('days').innerText = '00';
      document.getElementById('hours').innerText = '00';
      document.getElementById('minutes').innerText = '00';
      document.getElementById('seconds').innerText = '00';
      return;
    }

    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const mins = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const secs = Math.floor((distance % (1000 * 60)) / 1000);

    document.getElementById('days').innerText = String(days).padStart(2, '0');
    document.getElementById('hours').innerText = String(hours).padStart(2, '0');
    document.getElementById('minutes').innerText = String(mins).padStart(2, '0');
    document.getElementById('seconds').innerText = String(secs).padStart(2, '0');
  }, 1000);
}


// Fetch All Products
fetch('php/fetch_products.php?brand=all')
  .then(res => res.json())
  .then(products => {
    const productList = document.getElementById('productList');
    let html = '';

    products.forEach(p => {
      const productImg = `src/img/${p.image}`;
      // Extract brand from image path (e.g., "nike/5.png" -> "nike")
      const brand = p.image.split('/')[0];

      const priceNum = parseFloat(p.price.toString().replace(/,/g, ''));
      const formattedPrice = priceNum.toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });

      html += `
        <div class="product-card">
          <img src="${productImg}" alt="${p.name}" onerror="this.src='src/img/placeholder.png'">
          <h3>${p.name}</h3>
          <p class="price">₱${formattedPrice}</p>
          <button class="add-cart-btn" onclick="addToCart(${p.id}, '${brand}')">
            <i class="fas fa-cart-plus"></i> Add to Cart
          </button>
        </div>
      `;
    });

    productList.innerHTML = html;
  })
  .catch(err => console.error('Error fetching products:', err));


// Add to Cart Function
async function addToCart(productId, brand) {
  try {
    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('product_id', productId);
    formData.append('brand', brand);
    formData.append('quantity', 1);

    const res = await fetch('php/cart.php', {
      method: 'POST',
      body: formData
    });
    
    const data = await res.json();
    
    if (data.success) {
      showNotification('Added to cart!', 'success');
      updateCartBadge();
    } else {
      // Not logged in
      showNotification(data.message, 'error');
      setTimeout(() => {
        window.location.href = 'login.php?redirect=Homepage.php';
      }, 1500);
    }
  } catch (err) {
    console.error('Error adding to cart:', err);
    showNotification('Error adding to cart', 'error');
  }
}


// Update Cart Badge
async function updateCartBadge() {
  try {
    const res = await fetch('php/cart.php?action=count');
    const data = await res.json();
    const badge = document.getElementById('cart-badge');
    if (badge) {
      badge.textContent = data.count || 0;
      badge.style.display = data.count > 0 ? 'block' : 'none';
    }
  } catch (err) {
    // User not logged in, ignore
  }
}


// Show Notification Toast
function showNotification(message, type = 'success') {
  // Remove existing notification
  const existing = document.querySelector('.notification-toast');
  if (existing) existing.remove();

  const toast = document.createElement('div');
  toast.className = `notification-toast ${type}`;
  toast.innerHTML = `
    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
    <span>${message}</span>
  `;
  
  document.body.appendChild(toast);
  
  // Trigger animation
  setTimeout(() => toast.classList.add('show'), 10);
  
  // Remove after 3 seconds
  setTimeout(() => {
    toast.classList.remove('show');
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}


// Initialize cart badge on page load
document.addEventListener('DOMContentLoaded', () => {
  updateCartBadge();
});