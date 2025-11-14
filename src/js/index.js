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
    } else {
      // Hide section if no limited product
      document.getElementById('limited-product').style.display = 'none';
    }
  })
  .catch(err => console.error('Error fetching limited product:', err));



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
