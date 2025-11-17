// Countdown Timer Function
let countdownInterval;

function startCountdown(endDate) {
  const countdownContainer = document.getElementById('countdown-container');
  const daysEl = document.getElementById('days');
  const hoursEl = document.getElementById('hours');
  const minutesEl = document.getElementById('minutes');
  const secondsEl = document.getElementById('seconds');

  // Clear any existing interval
  if (countdownInterval) {
    clearInterval(countdownInterval);
  }

  function updateCountdown() {
    const now = new Date().getTime();
    const end = new Date(endDate).getTime();
    const distance = end - now;

    if (distance < 0) {
      clearInterval(countdownInterval);
      countdownContainer.innerHTML = '<div class="countdown-label" style="color: #e74c3c;"><i class="fas fa-exclamation-circle"></i> This offer has ended!</div>';
      // Optionally reload the page to remove the expired product
      setTimeout(() => {
        location.reload();
      }, 3000);
      return;
    }

    // Calculate time units
    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    // Update DOM
    daysEl.textContent = String(days).padStart(2, '0');
    hoursEl.textContent = String(hours).padStart(2, '0');
    minutesEl.textContent = String(minutes).padStart(2, '0');
    secondsEl.textContent = String(seconds).padStart(2, '0');

    // Add animation effect
    [daysEl, hoursEl, minutesEl, secondsEl].forEach(el => {
      el.style.animation = 'none';
      setTimeout(() => {
        el.style.animation = 'pulse 0.3s ease';
      }, 10);
    });
  }

  // Show countdown container
  countdownContainer.style.display = 'block';

  // Update immediately
  updateCountdown();

  // Update every second
  countdownInterval = setInterval(updateCountdown, 1000);
}

// Add pulse animation to CSS (dynamically)
const style = document.createElement('style');
style.textContent = `
  @keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
  }
`;
document.head.appendChild(style);

// Fetch Limited Product
fetch('php/fetch_limited.php')
  .then(res => res.json())
  .then(product => {
    if(product){
      const limitedImg = encodeURI(`src/img/${product.image}`);
      document.getElementById('limited-img').src = limitedImg;
      document.getElementById('limited-name').innerText = product.name;
      document.getElementById('limited-desc').innerText = product.description;

      // Format price as ₱ with commas and 2 decimals
      const formattedPrice = parseFloat(product.price).toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });
      document.getElementById('limited-price').innerText = `₱${formattedPrice}`;

      // Start countdown timer if end_date exists
      if(product.end_date) {
        startCountdown(product.end_date);
      }
    } else {
      // Hide section if no limited product
      document.getElementById('limited-product').style.display = 'none';
    }
  })
  .catch(err => console.error('Error fetching limited product:', err));


// Fetch Other Products (all brands dynamically)
const brands = ['nike', 'adidas', 'puma'];
const productList = document.getElementById('productList');
let allProductsHTML = '';

brands.forEach(brand => {
  fetch(`php/fetch_products.php?brand=${brand}`)
    .then(res => res.json())
    .then(data => {
      data.forEach(p => {
        const productImg = encodeURI(`src/img/${p.image}`);
        const formattedPrice = parseFloat(p.price).toLocaleString('en-PH', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        });

        allProductsHTML += `
          <div class="product-card">
            <img src="${productImg}" alt="${p.name}">
            <h3>${p.name}</h3>
            <p>₱${formattedPrice}</p>
          </div>
        `;
      });
      productList.innerHTML = allProductsHTML;
    })
    .catch(err => console.error(`Error fetching products for ${brand}:`, err));
});