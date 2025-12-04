// Store limited product data globally
let limitedProductData = null;

// Fetch Limited Product
fetch('php/fetch_limited.php')
  .then(res => res.json())
  .then(product => {
    if(product){
      limitedProductData = product;
      
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
      
      const limitedCartBtn = document.getElementById('limited-add-cart');
      if (limitedCartBtn) {
        limitedCartBtn.addEventListener('click', () => {
          addToCart(product.id, product.brand);
        });
      }

      const shopNowBtn = document.getElementById('shop-now');
      if (shopNowBtn) {
        shopNowBtn.addEventListener('click', () => {
          buyNow(product.id, product.brand);
        });
      }
      
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
      const brand = p.image.split('/')[0];

      const priceNum = parseFloat(p.price.toString().replace(/,/g, ''));
      const formattedPrice = priceNum.toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });

      // Truncate description for preview
      const shortDesc = p.description.length > 100 
        ? p.description.substring(0, 100) + '...' 
        : p.description;

      html += `
        <div class="product-card" data-product-id="${p.id}">
          <div class="product-card-inner">
            <!-- FRONT SIDE -->
            <div class="product-card-front">
              <div class="wishlist-heart" onclick="toggleWishlist(${p.id}, '${brand}', event)" data-product-id="${p.id}" data-brand="${brand}">
                <i class="far fa-heart"></i>
              </div>
              <img src="${productImg}" alt="${p.name}" onerror="this.src='src/img/placeholder.png'">
              <h3>${p.name}</h3>
              <p class="price">₱${formattedPrice}</p>
              <div class="card-actions">
                <button class="view-details-btn" onclick="flipCard(${p.id}, event)">
                  <i class="fas fa-info-circle"></i>
                </button>
                <button class="add-cart-btn" onclick="addToCart(${p.id}, '${brand}', event)">
                  <i class="fas fa-cart-plus"></i> Add
                </button>
              </div>
            </div>

            <!-- BACK SIDE -->
            <div class="product-card-back">
              <div class="back-header">
                <h3>${p.name}</h3>
                <button class="back-close-btn" onclick="flipCard(${p.id}, event)">
                  <i class="fas fa-times"></i>
                </button>
              </div>
              
              <div class="product-details">
                <div class="detail-row">
                  <span class="detail-label"><i class="fas fa-tag"></i> Brand</span>
                  <span class="detail-value brand-tag">${brand.toUpperCase()}</span>
                </div>
                <div class="detail-row">
                  <span class="detail-label"><i class="fas fa-dollar-sign"></i> Price</span>
                  <span class="detail-value price-tag">₱${formattedPrice}</span>
                </div>
                <div class="detail-row">
                  <span class="detail-label"><i class="fas fa-box"></i> Product ID</span>
                  <span class="detail-value">#${p.id}</span>
                </div>
                <div class="detail-row">
                  <span class="detail-label"><i class="fas fa-check-circle"></i> Availability</span>
                  <span class="detail-value" style="color: #4caf50;">In Stock</span>
                </div>
                
                <div class="description-section">
                  <h4><i class="fas fa-align-left"></i> Description</h4>
                  <p class="description-text">${p.description}</p>
                </div>
              </div>

              <div class="back-actions">
                <button class="add-cart-btn" onclick="addToCart(${p.id}, '${brand}', event)">
                  <i class="fas fa-cart-plus"></i> Add to Cart
                </button>
              </div>
            </div>
          </div>
        </div>
      `;
    });

    productList.innerHTML = html;
    
    // Check wishlist status for all products
    checkAllWishlistStatus();
  })
  .catch(err => console.error('Error fetching products:', err));


// Flip Card Function
function flipCard(productId, event) {
  if (event) event.stopPropagation();
  const card = document.querySelector(`.product-card[data-product-id="${productId}"]`);
  if (card) {
    card.classList.toggle('flipped');
  }
}


// Check wishlist status for all products
async function checkAllWishlistStatus() {
  try {
    const hearts = document.querySelectorAll('.wishlist-heart');
    
    for (const heart of hearts) {
      const productId = heart.dataset.productId;
      const brand = heart.dataset.brand;
      
      const res = await fetch(`php/check_wishlist.php?product_id=${productId}&brand=${brand}`);
      const data = await res.json();
      
      if (data.success && data.in_wishlist) {
        const icon = heart.querySelector('i');
        icon.classList.remove('far');
        icon.classList.add('fas');
        heart.classList.add('active');
      }
    }
  } catch (err) {
    console.error('Error checking wishlist status:', err);
  }
}


// Toggle wishlist
async function toggleWishlist(productId, brand, event) {
  event.stopPropagation();
  
  try {
    const formData = new FormData();
    formData.append('action', 'toggle');
    formData.append('product_id', productId);
    formData.append('brand', brand);

    const res = await fetch('php/manage_wishlist.php', {
      method: 'POST',
      body: formData
    });
    
    const data = await res.json();
    
    if (data.success) {
      const heart = document.querySelector(`.wishlist-heart[data-product-id="${productId}"][data-brand="${brand}"]`);
      const icon = heart.querySelector('i');
      
      if (data.action === 'added') {
        icon.classList.remove('far');
        icon.classList.add('fas');
        heart.classList.add('active');
        showNotification('Added to wishlist!', 'success');
      } else {
        icon.classList.remove('fas');
        icon.classList.add('far');
        heart.classList.remove('active');
        showNotification('Removed from wishlist', 'success');
      }
    } else {
      showNotification(data.message, 'error');
      if (data.message === 'Please login first') {
        setTimeout(() => {
          window.location.href = 'login.php?redirect=HomePage.php';
        }, 1500);
      }
    }
  } catch (err) {
    console.error('Error toggling wishlist:', err);
    showNotification('Error updating wishlist', 'error');
  }
}


// Add to Cart Function
async function addToCart(productId, brand, event) {
  if (event) event.stopPropagation();
  
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
      showNotification(data.message, 'error');
      setTimeout(() => {
        window.location.href = 'login.php?redirect=HomePage.php';
      }, 1500);
    }
  } catch (err) {
    console.error('Error adding to cart:', err);
    showNotification('Error adding to cart', 'error');
  }
}


// Buy Now Function
async function buyNow(productId, brand) {
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
      window.location.href = 'checkout.php';
    } else {
      showNotification(data.message, 'error');
      setTimeout(() => {
        window.location.href = 'login.php?redirect=HomePage.php';
      }, 1500);
    }
  } catch (err) {
    console.error('Error:', err);
    showNotification('Please login first', 'error');
    setTimeout(() => {
      window.location.href = 'login.php?redirect=HomePage.php';
    }, 1500);
  }
}


// Update Cart Badge
async function updateCartBadge() {
  try {
    const res = await fetch('php/cart.php?action=count');
    const data = await res.json();
    const badge = document.getElementById('cart-badge');
    if (badge) {
      const count = data.count || 0;
      badge.textContent = count;
      if (count > 0) {
        badge.classList.remove('hidden');
      } else {
        badge.classList.add('hidden');
      }
    }
  } catch (err) {
    const badge = document.getElementById('cart-badge');
    if (badge) badge.classList.add('hidden');
  }
}


// Show Notification Toast
function showNotification(message, type = 'success') {
  const existing = document.querySelector('.notification-toast');
  if (existing) existing.remove();

  const toast = document.createElement('div');
  toast.className = `notification-toast ${type}`;
  toast.innerHTML = `
    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
    <span>${message}</span>
  `;
  
  document.body.appendChild(toast);
  setTimeout(() => toast.classList.add('show'), 10);
  setTimeout(() => {
    toast.classList.remove('show');
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}


// Initialize cart badge on page load
document.addEventListener('DOMContentLoaded', () => {
  updateCartBadge();
});