let countdownInterval;

function startCountdown(endDate) {
  const countdownContainer = document.getElementById('countdown-container');
  const daysEl = document.getElementById('days');
  const hoursEl = document.getElementById('hours');
  const minutesEl = document.getElementById('minutes');
  const secondsEl = document.getElementById('seconds');

  if(countdownInterval) clearInterval(countdownInterval);

  function updateCountdown() {
    const now = new Date().getTime();
    // Add full day time to make countdown work till 23:59:59
    const end = new Date(endDate + "T23:59:59").getTime();
    const distance = end - now;

    if(distance < 0){
      clearInterval(countdownInterval);
      countdownContainer.innerHTML = '<div class="countdown-label" style="color: #e74c3c;"><i class="fas fa-exclamation-circle"></i> This offer has ended!</div>';
      setTimeout(() => location.reload(), 3000);
      return;
    }

    const days = Math.floor(distance / (1000*60*60*24));
    const hours = Math.floor((distance % (1000*60*60*24)) / (1000*60*60));
    const minutes = Math.floor((distance % (1000*60*60)) / (1000*60));
    const seconds = Math.floor((distance % (1000*60)) / 1000);

    daysEl.textContent = String(days).padStart(2,'0');
    hoursEl.textContent = String(hours).padStart(2,'0');
    minutesEl.textContent = String(minutes).padStart(2,'0');
    secondsEl.textContent = String(seconds).padStart(2,'0');

    [daysEl, hoursEl, minutesEl, secondsEl].forEach(el => {
      el.style.animation = 'none';
      setTimeout(() => el.style.animation = 'pulse 0.3s ease', 10);
    });
  }

  countdownContainer.style.display = 'flex'; // show container
  updateCountdown();
  countdownInterval = setInterval(updateCountdown, 1000);
}

// Add CSS pulse animation
const style = document.createElement('style');
style.textContent = `
@keyframes pulse {0%,100%{transform:scale(1);}50%{transform:scale(1.1);}}
`;
document.head.appendChild(style);

// Fetch limited product
fetch('php/fetch_limited.php')
  .then(res => res.json())
  .then(product => {
    if(product){
      document.getElementById('limited-img').src = encodeURI(`src/img/${product.image}`);
      document.getElementById('limited-name').innerText = product.name;
      document.getElementById('limited-desc').innerText = product.description;
      document.getElementById('limited-price').innerText = `₱${parseFloat(product.price).toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2})}`;

      if(product.end_date) startCountdown(product.end_date);
    } else {
      document.getElementById('limited-product').style.display = 'none';
    }
  })
  .catch(err => console.error('Error fetching limited product:', err));
