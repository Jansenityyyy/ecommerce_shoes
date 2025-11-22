// Fetch Limited Product
fetch('php/fetch_limited.php')
  .then(res => res.json())
  .then(product => {
    if(product){
      // Image path is already "brand/filename.png" in database
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
      // Database already has "nike/5.png", just add "src/img/"
      const productImg = `src/img/${p.image}`;

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
          <button class="add-cart-btn" onclick="addToCart(${p.id})">
            <i class="fas fa-cart-plus"></i> Add to Cart
          </button>
        </div>
      `;
    });

    productList.innerHTML = html;
  })
  .catch(err => console.error('Error fetching products:', err));


// Add to Cart
function addToCart(productId) {
  alert('Added product #' + productId + ' to cart!');
}