<?php include '../php/connect.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Add Product</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Amsterdam+One&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #2c2c2c 0%, #1a1a1a 100%);
      color: #fff;
      min-height: 100vh;
      overflow-x: hidden;
      padding: 0;
    }

    /* Navbar */
    nav {
      position: fixed;
      top: 0;
      width: 100%;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 50px;
      background: rgba(28, 28, 28, 0.95);
      backdrop-filter: blur(10px);
      box-shadow: 0 2px 20px rgba(0,0,0,0.5);
      z-index: 1000;
      transition: all 0.3s ease;
    }

    nav .logo {
      font-size: 2rem;
      font-weight: bold;
      color: #ff9d00;
      font-family: 'Amsterdam One', sans-serif;
      letter-spacing: 2px;
    }

    nav .nav-links {
      list-style: none;
      display: flex;
      gap: 35px;
      margin: 0;
      padding: 0;
    }

    nav .nav-links li a {
      color: #ff9d00;
      text-decoration: none;
      font-weight: 500;
      font-size: 1rem;
      transition: all 0.3s ease;
      position: relative;
    }

    nav .nav-links li a:hover {
      color: #fff;
    }

    nav .nav-links li a::after {
      content: '';
      position: absolute;
      bottom: -5px;
      left: 0;
      width: 0;
      height: 2px;
      background: #ff9d00;
      transition: width 0.3s ease;
    }

    nav .nav-links li a:hover::after {
      width: 100%;
    }

    /* Main Content */
    .admin-section {
      padding: 120px 20px 80px;
      position: relative;
      overflow: hidden;
    }

    .admin-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: radial-gradient(circle at 30% 50%, rgba(255, 157, 0, 0.1) 0%, transparent 50%),
                  radial-gradient(circle at 70% 50%, rgba(255, 157, 0, 0.05) 0%, transparent 50%);
      pointer-events: none;
    }

    h1 {
      text-align: center;
      margin-bottom: 50px;
      font-size: 2.5rem;
      font-weight: 700;
      color: #ff9d00;
      letter-spacing: 1px;
      position: relative;
      z-index: 1;
    }

    /* Container for both forms */
    .admin-container {
      display: flex;
      justify-content: center;
      align-items: flex-start;
      flex-wrap: wrap;
      gap: 30px;
      padding: 0 20px;
      max-width: 1400px;
      margin: 0 auto;
      position: relative;
      z-index: 1;
    }

    /* Form styling */
    form {
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(255, 157, 0, 0.1);
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
      width: 45%;
      min-width: 320px;
      transition: all 0.4s ease;
      animation: fadeInUp 0.8s ease;
    }

    form:hover {
      transform: translateY(-10px);
      background: rgba(255,255,255,0.08);
      border-color: rgba(255, 157, 0, 0.3);
      box-shadow: 0 20px 60px rgba(255, 157, 0, 0.2);
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Form headings */
    form h2 {
      text-align: center;
      margin-bottom: 30px;
      font-size: 1.8rem;
      font-weight: 700;
      color: #fff;
    }

    /* Labels */
    label {
      display: block;
      margin: 15px 0 8px 0;
      font-weight: 600;
      font-size: 0.95rem;
      color: #fff;
    }

    /* Inputs and selects */
    input[type="text"],
    input[type="number"],
    select,
    textarea,
    input[type="file"],
    input[type="date"] {
      width: 100%;
      padding: 12px 16px;
      border-radius: 10px;
      border: 1px solid rgba(255, 157, 0, 0.2);
      background: rgba(255, 255, 255, 0.08);
      color: #fff;
      margin-bottom: 15px;
      font-size: 1rem;
      font-family: 'Poppins', sans-serif;
      transition: all 0.3s ease;
    }

    input[type="text"]:focus,
    input[type="number"]:focus,
    select:focus,
    textarea:focus,
    input[type="date"]:focus {
      outline: none;
      border-color: #ff9d00;
      background: rgba(255, 255, 255, 0.12);
      box-shadow: 0 0 15px rgba(255, 157, 0, 0.2);
    }

    select option {
      background: #1a1a1a;
      color: #fff;
    }

    textarea {
      resize: vertical;
      min-height: 80px;
    }

    input[type="file"] {
      padding: 10px;
      cursor: pointer;
    }

    input[type="file"]::file-selector-button {
      padding: 8px 16px;
      background: linear-gradient(135deg, #ff9d00 0%, #ff7700 100%);
      color: #111;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      font-family: 'Poppins', sans-serif;
      transition: all 0.3s ease;
      margin-right: 10px;
    }

    input[type="file"]::file-selector-button:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(255, 157, 0, 0.4);
    }

    /* Buttons */
    button {
      width: 100%;
      padding: 15px 35px;
      background: linear-gradient(135deg, #ff9d00 0%, #ff7700 100%);
      color: #111;
      border: none;
      border-radius: 50px;
      cursor: pointer;
      font-weight: 700;
      font-size: 1.1rem;
      font-family: 'Poppins', sans-serif;
      transition: all 0.4s ease;
      box-shadow: 0 10px 30px rgba(255, 157, 0, 0.3);
      position: relative;
      overflow: hidden;
      margin-top: 10px;
    }

    button::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, #fff 0%, #ff9d00 100%);
      transition: left 0.4s ease;
      z-index: -1;
    }

    button:hover::before {
      left: 0;
    }

    button:hover {
      transform: translateY(-3px);
      box-shadow: 0 15px 40px rgba(255, 157, 0, 0.5);
    }

    /* Success Alert Styling */
    .success-alert {
      position: fixed;
      top: 100px;
      left: 50%;
      transform: translateX(-50%);
      background: rgba(0, 255, 0, 0.15);
      border: 1px solid #00ff00;
      color: #00ff00;
      padding: 15px 30px;
      border-radius: 10px;
      box-shadow: 0 10px 30px rgba(0, 255, 0, 0.3);
      z-index: 2000;
      animation: slideDown 0.5s ease;
      font-weight: 600;
    }

    @keyframes slideDown {
      from {
        top: -50px;
        opacity: 0;
      }
      to {
        top: 100px;
        opacity: 1;
      }
    }

    /* Responsive for small screens */
    @media screen and (max-width: 1100px) {
      .admin-container {
        flex-direction: column;
        align-items: center;
      }
      form {
        width: 90%;
        max-width: 600px;
      }
    }

    @media screen and (max-width: 768px) {
      nav {
        padding: 15px 30px;
      }

      nav .logo {
        font-size: 1.5rem;
      }

      nav .nav-links {
        gap: 20px;
      }

      nav .nav-links li a {
        font-size: 0.9rem;
      }

      .admin-section {
        padding: 100px 20px 60px;
      }

      h1 {
        font-size: 2rem;
      }

      form {
        width: 100%;
        padding: 30px 20px;
      }

      form h2 {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav>
    <div class="logo">SenSneaks Inc.</div>
    <ul class="nav-links">
      <li><a href="../index.html"><i class="fas fa-home"></i> Home</a></li>
      <li><a href="#"><i class="fas fa-cog"></i> Admin</a></li>
    </ul>
  </nav>

  <?php if(isset($_GET['success'])): ?>
    <div class="success-alert" id="successAlert">
      <i class="fas fa-check-circle"></i> Product added successfully!
    </div>
    <script>
      setTimeout(function() {
        const alert = document.getElementById('successAlert');
        if(alert) {
          alert.style.animation = 'slideDown 0.5s ease reverse';
          setTimeout(() => alert.remove(), 500);
        }
      }, 3000);
    </script>
  <?php endif; ?>

  <div class="admin-section">
    <h1>Admin Panel</h1>

    <div class="admin-container">
      <!-- Normal Product Form -->
      <form action="upload_product.php" method="POST" enctype="multipart/form-data">
        <h2><i class="fas fa-box"></i> Add Product</h2>
        <label><i class="fas fa-tag"></i> Brand:</label>
        <select name="brand" required>
          <option value="nike">Nike</option>
          <option value="adidas">Adidas</option>
          <option value="puma">Puma</option>
        </select>
        <label><i class="fas fa-shopping-bag"></i> Product Name:</label>
        <input type="text" name="name" required>
        <label><i class="fas fa-dollar-sign"></i> Price:</label>
        <input type="number" name="price" step="0.01" required>
        <label><i class="fas fa-align-left"></i> Description:</label>
        <textarea name="description" rows="4" required></textarea>
        <label><i class="fas fa-image"></i> Image:</label>
        <input type="file" name="image" accept="image/*" required>
        <button type="submit"><i class="fas fa-plus-circle"></i> Add Product</button>
      </form>

      <!-- Limited Product Form -->
      <form action="upload_limited.php" method="POST" enctype="multipart/form-data">
        <h2><i class="fas fa-star"></i> Add Limited Product</h2>
        <label><i class="fas fa-tag"></i> Brand:</label>
        <select name="brand" required>
          <option value="nike">Nike</option>
          <option value="adidas">Adidas</option>
          <option value="puma">Puma</option>
        </select>
        <label><i class="fas fa-shopping-bag"></i> Product Name:</label>
        <input type="text" name="name" required>
        <label><i class="fas fa-dollar-sign"></i> Price:</label>
        <input type="text" name="price" id="limitedPriceInput" placeholder="₱ 0.00" required>
        <label><i class="fas fa-align-left"></i> Description:</label>
        <textarea name="description" rows="3" required></textarea>
        <label><i class="fas fa-image"></i> Image:</label>
        <input type="file" name="image" accept="image/*" required>
        <label><i class="far fa-calendar-alt"></i> Start Date:</label>
        <input type="date" name="start_date" required>
        <label><i class="far fa-calendar-check"></i> End Date:</label>
        <input type="date" name="end_date" required>
        <button type="submit"><i class="fas fa-plus-circle"></i> Add Limited Product</button>
      </form>
    </div>
  </div>

  <script>
    // Format Limited Product Price
    const limitedPriceInput = document.getElementById('limitedPriceInput');
    limitedPriceInput.addEventListener('blur', function() {
      let value = this.value.replace(/[^\d.]/g, '');
      let number = parseFloat(value);
      if(!isNaN(number)) {
        this.value = '₱ ' + number.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
      } else {
        this.value = '';
      }
    });
  </script>
</body>
</html>