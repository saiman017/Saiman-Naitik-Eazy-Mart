<?php
session_start(); // Start the session

require_once '../middlewares/checkAuthentication.php';

// Check if the user is logged in
checkIfUserIsLoggedIn();

// Establish connection to Oracle database
$conn = oci_connect('saiman', 'Stha_12', '//localhost/xe');
if (!$conn) {
    $m = oci_error();
    $_SESSION['error'] = $m['message'];
    exit();
} else {
    $_SESSION['notification'] = "Connected to Oracle!";
}


// Fetch the shop details
$traderid = $_SESSION['user']['TRADER_ID'];
$query_shop = "SELECT * FROM Shop WHERE Trader_id = '$traderid'";
$statement_shop = oci_parse($conn, $query_shop);
oci_execute($statement_shop);

// Fetch the shop details
$row_shop = oci_fetch_assoc($statement_shop);
$shopId = $row_shop['SHOP_ID'];

if (isset($_POST['addproduct'])) {
    $productName = $_POST['productName'];
    $productDescription = $_POST['productDes'];
    $productPrice = $_POST['productPrice'];
    $productStock = $_POST['productStock'];
    $minOrder = $_POST['minOrder'];
    $maxOrder = $_POST['maxOrder'];
    $productPhoto = $_POST['productPhoto'];
    $allergy = $_POST['allergy'];
    $discount = $_POST['discount'];
    $categories = $_POST['category'];

    // Check if product name already exists
    $query_check = "SELECT PRODUCT_NAME FROM Product WHERE PRODUCT_NAME = '$productName'";
    $statement_check = oci_parse($conn, $query_check);
    oci_execute($statement_check);
    $row = oci_fetch_assoc($statement_check);

    if ($row !== false) {
        $_SESSION['error'] = "Product name already exists. Please use a different name.";
        header("Location: view_product_detail.php");
        exit();
    }

    // Insert new product into database
    $query = "INSERT INTO Product (PRODUCT_NAME, DESCRIPTION, PRICE, STOCK_AVAILABLE, MIN_ORDER, MAX_ORDER, ALLERGY, PRODUCT_IMAGE, SHOP_ID, DISCOUNT_ID, CATEGORY_ID) 
          VALUES ('$productName', '$productDescription', '$productPrice', '$productStock', '$minOrder', '$maxOrder', '$allergy', '$productPhoto', '$shopId', '$discount', '$categories')";
    $statement = oci_parse($conn, $query);
    $result = oci_execute($statement);

    if ($result) {
        oci_commit($conn);
        header("Location: view_product_detail.php");
        exit();
    } else {
        $error = oci_error($statement);
        $_SESSION['error'] = $error['message'];
        exit();
    }
}

oci_close($conn); // Close database connection
?>





<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>View Shop Details</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
        <!-- Include Tailwind CSS -->
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="trader.css">
  </head>
  <body>
    <div class="">
    <div class="grid-container">

<header class="header">
<div class="menu-icon">
<span class="material-icons-outlined">menu</span>
</div>
<div class="search-bar">
<input type="text" placeholder="Search...">
</div>
<div class="profile-section">
<div class="profile-image">
  <img src="../assets/images/Shop/butcher 1.jpg" alt="Profile Image">
</div>
<div class="profile-name"></div>
</div>
</header>


 <!-- Sidebar -->
<aside id="sidebar">
<div class="sidebar-title">
<div class="sidebar-brand">
  <a href="./trader_dashboard.php"><img src="../assets/images/icons/logo.png" alt=""></a>
</div>
</div>

<ul class="sidebar-list">
<li class="sidebar-list-item">
  <a href="./trader_dashboard.php">
    <span class="material-icons-outlined">dashboard</span> Dashboard
  </a>
</li>
<li class="sidebar-list-item">
<a href="#">
<span class="material-icons-outlined">leaderboard</span> Report
</a>
<ul class="submenu">
<li><a href="#">Report 1</a></li>
<li><a href="#">Report 2</a></li>
<li><a href="#">Report 3</a></li>
</ul>
</li>
<li class="sidebar-list-item">
  <a href="trader_profile.php">
   <img src="#" alt=""> My Profile
  </a>
</li>
<li class="sidebar-list-item">
  <a href="./view_product_detail.php">
   <img src="view_product_detail.php" alt=""> Product Detail
  </a>
</li>
<li class="sidebar-list-item">
  <a href="#">
   <img src="#" alt=""> Shop Detail
  </a>
</li>
</ul>

<!-- Logout Button -->
<div class="logout-button">
<button>Logout</button>
</div>
</aside>
<!-- End Sidebar -->


  <!-- Main -->
  <main class="main-container">
    <div class="main-title">
      <h2>Product Detail</h2>
    </div>
    <!-- Product CRUD View -->
<div class="container mx-auto py-8 px-4">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Your Products</h2>
        <button id="addProductBtn" class="addbg-gradient-to-r from-purple-500 to-indigo-500 text-white px-4 py-2 rounded-md shadow-md hover:bg-gradient bg-gradient transition duration-300">Add Product</button>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 product-list">
        <!-- Product Card -->
        <div class="product-card">
            <img src="https://images.unsplash.com/photo-1529692236671-f1f6cf9683ba?q=80&w=1470&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Product Image">
            <div class="product-info p-4">
                <h3 class="text-lg font-semibold text-gray-800">Product Name</h3>
                <p class="text-sm text-gray-600 mt-2">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                <p class="text-sm text-gray-600 mt-2">Price: $10</p>
                <p class="text-sm text-gray-600 mt-2">Stock: 100</p>
                <p class="text-sm text-gray-600 mt-2">Upload Date: January 1, 2022</p>
            </div>
            <div class="product-actions flex justify-between items-center">
                <button class="text-xs text-gray-600 hover:text-indigo-600 transition duration-300 view-btn">View</button>
                <button class="text-xs text-gray-600 hover:text-indigo-600 transition duration-300 edit-btn">Edit</button>
                <button class="text-xs text-red-600 hover:text-red-700 transition duration-300">Delete</button>
            </div>
        </div>
        <!-- Repeat product cards here -->
    </div>
</div>

<!-- Product Modal -->
<div id="productModal" class="modal">
    <div class="modal-content bg-white">
        <span class="close">&times;</span>
        <div id="productFormContainer"></div>
    </div>
</div>

  </main>
</div>

    </div>
    

  </body>
  <script>
        // Get the modal
        var modal = document.getElementById("productModal");

        // Get the button that opens the modal
        var addProductBtn = document.getElementById("addProductBtn");

        // Get the <span> element that closes the modal
        var closeBtn = document.getElementsByClassName("close")[0];

        // When the user clicks the button to add a product, show the add product form
        addProductBtn.onclick = function() {
            showAddProductForm();
        }

        // Example function to show the add product form
        function showAddProductForm() {
            var productFormContainer = document.getElementById("productFormContainer");
            productFormContainer.innerHTML = `
                <div id="productAddForm">
                    <h2 class="text-xl font-semibold mb-4">Add Product</h2>
                    <form class="space-y-4" method="POST">
                        <div>
                            <label for="productName" class="block text-sm font-medium text-gray-700">Product Name</label>
                            <input type="text" name="productName" id="productName" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md placeholder-gray-400 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-300 bg-gray-100 hover:bg-gray-200">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="productPrice" class="block text-sm font-medium text-gray-700">Price</label>
                                <input type="number" name="productPrice" id="productPrice" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md placeholder-gray-400 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-300 bg-gray-100 hover:bg-gray-200">
                            </div>
                            <div>
                                <label for="productStock" class="block text-sm font-medium text-gray-700">Stock</label>
                                <input type="number" name="productStock" id="productStock" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md placeholder-gray-400 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-300 bg-gray-100 hover:bg-gray-200">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="minOrder" class="block text-sm font-medium text-gray-700">Min Order</label>
                                <input type="number" name="minOrder" id="minOrder" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md placeholder-gray-400 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-300 bg-gray-100 hover:bg-gray-200">
                            </div>
                            <div>
                                <label for="maxOrder" class="block text-sm font-medium text-gray-700">Max Order</label>
                                <input type="number" name="maxOrder" id="maxOrder" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md placeholder-gray-400 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-300 bg-gray-100 hover:bg-gray-200">
                            </div>
                        </div>
                        <div>
                        <div>
            <label for="productName" class="block text-sm font-medium text-gray-700">Product Name</label>
            <input type="text" name="productName" id="productName" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md placeholder-gray-400 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-300 bg-gray-100 hover:bg-gray-200">
        </div>
        <div>
                <label for="discount" class="block text-sm font-medium text-gray-700">Discount</label>
                <select name="discount" id="discount" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md placeholder-gray-400 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-300 bg-gray-100 hover:bg-gray-200">
                    <option value="0">No discount</option>
                    <?php
    // Include the PHP code for fetching discounts here
    $query_discounts = "SELECT * FROM Discount";
    $statement_discounts = oci_parse($conn, $query_discounts);
    oci_execute($statement_discounts);
    while ($row_discount = oci_fetch_assoc($statement_discounts)) {
        echo '<option value="' . $row_discount['DISCOUNT_ID'] . '">' . $row_discount['Discount_Percent'] . '</option>';
    }
?>
                </select>
            </div>
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                <select name="category" id="category" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md placeholder-gray-400 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-300 bg-gray-100 hover:bg-gray-200">
                    <option value="0">Select category</option>
                    <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                <select name="category" id="category" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md placeholder-gray-400 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-300 bg-gray-100 hover:bg-gray-200">
                    <option value="0">Select category</option>
                    <?php
                            // Include the PHP code for fetching categories here
                            $query_categories = "SELECT * FROM Category";
                            $statement_categories = oci_parse($conn, $query_categories);
                            oci_execute($statement_categories);
                            while ($row_category = oci_fetch_assoc($statement_categories)) {
                                echo '<option value="' . $row_category['CATEGORY_ID'] . '">' . $row_category['CATEGORY_TYPE'] . '</option>';
                            }
                            ?>
    
                </select>
            </div>
                        <div>
                            <label for="productPhoto" class="block text-sm font-medium text-gray-700">Photo</label>
                            <input type="file" name="productPhoto" id="productPhoto" accept="image/*" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md placeholder-gray-400 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-300 bg-gray-100 hover:bg-gray-200">
                        </div>
                        <div>
                            <label for="productDes" class="block text-sm font-medium text-gray-700">Description</label>
                            <input type="text" name="productDes" id="productDes" id="productDes" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md placeholder-gray-400 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-300 bg-gray-100 hover:bg-gray-200">
                        </div>
                        <div>
                            <label for="allergy" class="block text-sm font-medium text-gray-700">Allergy Information</label>
                            <input type="text" name="allergy" id="allergy" id="allergy" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md placeholder-gray-400 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-300 bg-gray-100 hover:bg-gray-200">
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" name="addproduct" class="bg-indigo-600 hover:bg-gradient bg-gradient text-white px-4 py-2 rounded-md shadow-md transition duration-300">Add Product</button>
                        </div>
                    </form>
                </div>
            `;
            modal.style.display = "block";
        }

        // Example event listeners for edit and view buttons
        document.querySelectorAll('.edit-btn').forEach(item => {
            item.addEventListener('click', event => {
                showEditProductForm();
            })
        });

        document.querySelectorAll('.view-btn').forEach(item => {
            item.addEventListener('click', event => {
                showViewProductForm();
            })
        });

        // Example function to show the edit product form
        function showEditProductForm() {
            var productFormContainer = document.getElementById("productFormContainer");
            productFormContainer.innerHTML = `
                <div id="productEditForm">
                    <h2 class="text-xl font-semibold mb-4">Edit Product</h2>
                    <form class="space-y-4">
                        <div>
                            <label for="productName" class="block text-sm font-medium text-gray-700">Product Name</label>
                            <input type="text" name="productName" id="productName" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md placeholder-gray-400 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-300 bg-gray-100 hover:bg-gray-200">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="productPrice" class="block text-sm font-medium text-gray-700">Price</label>
                                <input type="number" name="productPrice" id="productPrice" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md placeholder-gray-400 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-300 bg-gray-100 hover:bg-gray-200">
                            </div>
                            <div>
                                <label for="productStock" class="block text-sm font-medium text-gray-700">Stock</label>
                                <input type="number" name="productStock" id="productStock" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md placeholder-gray-400 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-300 bg-gray-100 hover:bg-gray-200">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="minOrder" class="block text-sm font-medium text-gray-700">Min Order</label>
                                <input type="number" name="minOrder" id="minOrder" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md placeholder-gray-400 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-300 bg-gray-100 hover:bg-gray-200">
                            </div>
                            <div>
                                <label for="maxOrder" class="block text-sm font-medium text-gray-700">Max Order</label>
                                <input type="number" name="maxOrder" id="maxOrder" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md placeholder-gray-400 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-300 bg-gray-100 hover:bg-gray-200">
                            </div>
                        </div>
                        <div>
                            <label for="productPhoto" class="block text-sm font-medium text-gray-700">Photo</label>
                            <input type="file" name="productPhoto" id="productPhoto" accept="image/*" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md placeholder-gray-400 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-300 bg-gray-100 hover:bg-gray-200">
                        </div>
                        <div>
                            <label for="productDes" class="block text-sm font-medium text-gray-700">Description</label>
                            <input type="text" name="productDes" id="productDes" id="productDes"   class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md placeholder-gray-400 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-300 bg-gray-100 hover:bg-gray-200">
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="bg-indigo-600 hover:bg-gradient bg-gradient text-white px-4 py-2 rounded-md shadow-md transition duration-300">Save Changes</button>
                        </div>
                    </form>
                </div>
            `;
            modal.style.display = "block";
        }

        // Example function to show the view product form
       // Example function to show the view product form
function showViewProductForm(productData) {
    var productFormContainer = document.getElementById("productFormContainer");
    productFormContainer.innerHTML = `
        <div id="productViewForm">
            <h2 class="text-xl font-semibold mb-4">Product Details</h2>
            <form class="space-y-4">
                <div>
                    <label for="productName" class="block text-sm font-medium text-gray-700">Product Name</label>
                    <input type="text" name="productName" id="productName" value="${productData.name}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md placeholder-gray-400 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-300 bg-gray-100 hover:bg-gray-200">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="productPrice" class="block text-sm font-medium text-gray-700">Price</label>
                        <input type="number" name="productPrice" id="productPrice" value="${productData.price}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md placeholder-gray-400 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-300 bg-gray-100 hover:bg-gray-200">
                    </div>
                    <div>
                        <label for="productStock" class="block text-sm font-medium text-gray-700">Stock</label>
                        <input type="number" name="productStock" id="productStock" value="${productData.stock}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md placeholder-gray-400 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-300 bg-gray-100 hover:bg-gray-200">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="minOrder" class="block text-sm font-medium text-gray-700">Min Order</label>
                        <input type="number" name="minOrder" id="minOrder" value="${productData.minOrder}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md placeholder-gray-400 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-300 bg-gray-100 hover:bg-gray-200">
                    </div>
                    <div>
                        <label for="maxOrder" class="block text-sm font-medium text-gray-700">Max Order</label>
                        <input type="number" name="maxOrder" id="maxOrder" value="${productData.maxOrder}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md placeholder-gray-400 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-300 bg-gray-100 hover:bg-gray-200">
                    </div>
                </div>
                <div>
                    <label for="productPhoto" class="block text-sm font-medium text-gray-700">Photo</label>
                    <input type="file" name="productPhoto" id="productPhoto" accept="image/*" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md placeholder-gray-400 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-300 bg-gray-100 hover:bg-gray-200">
                </div>
                <div>
                            <label for="productDes" class="block text-sm font-medium text-gray-700">Description</label>
                            <input type="text" name="productDes" id="productDes" id="productName" value="${productData.description}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md placeholder-gray-400 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-300 bg-gray-100 hover:bg-gray-200">
                        </div>
            </form>
        </div>
    `;
    modal.style.display = "block";
}

        // When the user clicks on <span> (x), close the modal
        closeBtn.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
        document.querySelectorAll('.view-btn').forEach(item => {
    item.addEventListener('click', event => {
        // Assuming productData is an object containing the product details
        var productData = {
            name: "Product Name",
            price: 10,
            stock: 100,
            minOrder: 1,
            maxOrder: 10,
            description:'Product Description'
            
            // Add more properties as needed
        };
        showViewProductForm(productData);
    })
});

    </script>

</html>


