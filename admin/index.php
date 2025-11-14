<?php include '../php/connect.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Add Product</title>
  <link rel="stylesheet" href="../src/css/style.css">
  <style>
    body {
      background: linear-gradient(135deg, #111111, #444444, #aaaaaa);
      color: #fff;
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 40px 0;
    }

    h1 {
      text-align: center;
      margin-bottom: 40px;
    }

    .admin-container {
      display: flex;
      justify-content: center;
      gap: 50px;
      flex-wrap: wrap;
    }

    form {
      background-color: rgba(255,255,255,0.1);
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 0 20px rgba(0,0,0,0.5);
      width: 350px;
    }

    form h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin: 12px 0 5px 0;
      font-weight: bold;
    }

    input[type="text"],
    input[type="number"],
    select,
    textarea,
    input[type="file"],
    input[type="date"] {
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      border: none;
      margin-bottom: 15px;
      font-size: 1rem;
    }

    button {
      width: 100%;
      padding: 12px;
      background-color: #fff;
      color: #111;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
      font-size: 1rem;
      transition: 0.3s;
    }

    button:hover {
      background-color: #ddd;
    }
  </style>
</head>
<body>
  <?php if(isset($_GET['success'])): ?>
  <script>alert("Product added successfully!");</script>
  <?php endif; ?>

  <h1>Admin Panel</h1>

  <div class="admin-container">
    <!-- Normal Product Form -->
    <form action="upload_product.php" method="POST" enctype="multipart/form-data">
      <h2>Add Product</h2>

      <label>Brand:</label>
      <select name="brand" required>
        <option value="nike">Nike</option>
        <option value="adidas">Adidas</option>
        <option value="puma">Puma</option>
      </select>

      <label>Product Name:</label>
      <input type="text" name="name" required>

      <label>Price:</label>
      <input type="number" name="price" step="0.01" required>

      <label>Description:</label>
      <textarea name="description" rows="4" required></textarea>

      <label>Image:</label>
      <input type="file" name="image" accept="image/*" required>

      <button type="submit">Add Product</button>
    </form>

    <!-- Limited Product Form -->
    <form action="upload_limited.php" method="POST" enctype="multipart/form-data">
      <h2>Add Limited Product</h2>

      <label>Brand:</label>
      <select name="brand" required>
        <option value="nike">Nike</option>
        <option value="adidas">Adidas</option>
        <option value="puma">Puma</option>
      </select>

      <label>Product Name:</label>
      <input type="text" name="name" required>

      <label>Price:</label>
      <input type="text" name="price" id="limitedPriceInput" placeholder="₱ 0.00" required>

      <label>Description:</label>
      <textarea name="description" rows="3" required></textarea>

      <label>Image:</label>
      <input type="file" name="image" accept="image/*" required>

      <label>Start Date:</label>
      <input type="date" name="start_date" required>

      <label>End Date:</label>
      <input type="date" name="end_date" required>

      <button type="submit">Add Limited Product</button>
    </form>
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
