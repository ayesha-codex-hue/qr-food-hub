<?php require_once "auth.php"; ?>
<?php

include("db.php");

$message = "";


// =============================
// UPDATE MENU ITEM
// =============================

if (isset($_POST['update_item'])) {

    $edit_id = $_POST['edit_id'];
    $item_name = $_POST['item_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $image = $_POST['image'];
    $availability = $_POST['availability'];

    $update_sql = "UPDATE menu SET
                    item_name = ?,
                    description = ?,
                    price = ?,
                    category = ?,
                    image = ?,
                    availability = ?
                   WHERE id = ?";

    $update_stmt = mysqli_prepare($conn, $update_sql);

    mysqli_stmt_bind_param(
        $update_stmt,
        "ssdssii",
        $item_name,
        $description,
        $price,
        $category,
        $image,
        $availability,
        $edit_id
    );

    mysqli_stmt_execute($update_stmt);

    mysqli_stmt_close($update_stmt);

    header("Location: admin_menu.php");
    exit;
}


// =============================
// DELETE MENU ITEM
// =============================

if (isset($_GET['delete'])) {

    $delete_id = (int) $_GET['delete'];

    $delete_sql = "DELETE FROM menu WHERE id = ?";

    $delete_stmt = mysqli_prepare($conn, $delete_sql);

    mysqli_stmt_bind_param(
        $delete_stmt,
        "i",
        $delete_id
    );

    if (mysqli_stmt_execute($delete_stmt)) {

        mysqli_stmt_close($delete_stmt);

        header("Location: admin_menu.php");
        exit;

    } else {

        echo "Delete failed: " . mysqli_stmt_error($delete_stmt);

    }
}


// =============================
// ADD NEW MENU ITEM
// =============================

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['update_item'])) {

    $item_name = $_POST["item_name"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $category = $_POST["category"];
    $image = $_POST["image"];
    $availability = $_POST["availability"];

    $sql = "INSERT INTO menu
            (item_name, description, price, category, image, availability)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "ssdssi",
        $item_name,
        $description,
        $price,
        $category,
        $image,
        $availability
    );

    if (mysqli_stmt_execute($stmt)) {

        $message = "Menu item added successfully!";

    } else {

        $message = "Error: " . mysqli_error($conn);

    }

    mysqli_stmt_close($stmt);
}


// =============================
// GET ITEM FOR EDIT
// =============================

if (isset($_GET['edit'])) {

    $edit_id = (int) $_GET['edit'];

    $edit_sql = "SELECT * FROM menu WHERE id = ?";

    $edit_stmt = mysqli_prepare($conn, $edit_sql);

    mysqli_stmt_bind_param(
        $edit_stmt,
        "i",
        $edit_id
    );

    mysqli_stmt_execute($edit_stmt);

    $edit_result = mysqli_stmt_get_result($edit_stmt);

    $edit_item = mysqli_fetch_assoc($edit_result);

    mysqli_stmt_close($edit_stmt);
}


// =============================
// DISPLAY MENU ITEMS
// =============================

$sql = "SELECT * FROM menu ORDER BY id DESC";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>QR Food Hub - Admin Panel</title>

    <link rel="stylesheet" href="admin.css">
</head>

<body>
    <!-- Mobile top bar -->
<div class="admin-topbar">
    <button id="adminMenuBtn">☰</button>
    <h2>QR FOOD HUB</h2>
</div>

<script>
document.getElementById('adminMenuBtn')?.addEventListener('click', () => {
    document.querySelector('.admin-sidebar')?.classList.toggle('open');
});
</script>

<div class="admin-container">

    <!-- Sidebar -->
    <aside class="admin-sidebar">

        <h2 class="admin-logo">QR FOOD HUB</h2>

        <ul>
            <li>
                <a href="admin_dashboard.php">Dashboard</a>
            </li>

            <li>
                <a href="admin_menu.php" class="active">Menu Management</a>
            </li>

            <li>
                <a href="admin_orders.php">Orders</a>
            </li>

            <li>
                <a href="admin_payment.php">Payments</a>
            </li>
<li><a href="admin_qr.php">QR Codes</a></li>
            <li>
                <a href="index.php">View Website</a>
            </li>
<a href="logout.php" style="
    display:inline-block; margin:10px; padding:8px 14px;
    background:#b00; color:#fff; border-radius:6px;
    text-decoration:none; font-family:Arial;
">🚪 Logout</a>
            <!-- <li>
                <a href="logout.php">Logout</a>
            </li> -->
        </ul>

    </aside>


    <!-- Main Content -->
    <main class="admin-main">

        <div class="admin-header">

            <h1>Menu Management</h1>

        </div>


        <!-- Menu Section -->

       <!-- Add New Menu Item -->

<div class="dashboard-card">

    <h2>Add New Menu Item</h2>

    <br>
    <?php if ($message != ""): ?>

    <p class="success-message">
        <?php echo $message; ?>
    </p>

<?php endif; ?>

    <form action="" method="POST">

        <div class="form-group">

            <label>Item Name</label>

            <input type="text" name="item_name" placeholder="Enter food item name" required>

        </div>


        <div class="form-group">

            <label>Description</label>

            <textarea name="description" placeholder="Enter item description"></textarea>

        </div>


        <div class="form-group">

            <label>Price</label>

            <input type="number" name="price" placeholder="Enter price" required>

        </div>


        <div class="form-group">

            <label>Category</label>

            <select name="category" required>

                <option value="">Select Category</option>
                <option value="Burgers">Burgers</option>
                <option value="Pizza">Pizza</option>
                <option value="Pasta">Pasta</option>
                <option value="Desserts">Desserts</option>
                <option value="Drinks">Drinks</option>
                <option value="Deals">Deals</option>

            </select>

        </div>


        <div class="form-group">

            <label>Image</label>

            <input type="text" name="image" placeholder="Example: burger.jpg">

        </div>


        <div class="form-group">

            <label>Availability</label>

            <select name="availability">

                <option value="1">Available</option>
                <option value="0">Not Available</option>

            </select>

        </div>


        <button type="submit" class="admin-btn">
            Add Menu Item
        </button>

    </form>

</div>
<?php if (isset($edit_item)): ?>

<div class="dashboard-card">

    <h2>Edit Menu Item</h2>

    <br>

    <form action="" method="POST">

        <input type="hidden" name="edit_id"
               value="<?php echo $edit_item['id']; ?>">

        <div class="form-group">

            <label>Item Name</label>

            <input type="text"
                   name="item_name"
                   value="<?php echo htmlspecialchars($edit_item['item_name']); ?>"
                   required>

        </div>

        <div class="form-group">

            <label>Description</label>

            <textarea name="description"><?php
                echo htmlspecialchars($edit_item['description']);
            ?></textarea>

        </div>

        <div class="form-group">

            <label>Price</label>

            <input type="number"
                   name="price"
                   value="<?php echo $edit_item['price']; ?>"
                   required>

        </div>

        <div class="form-group">

            <label>Category</label>

            <select name="category" required>

                <option value="Burgers" <?php if ($edit_item['category'] == 'Burgers') echo 'selected'; ?>>
                    Burgers
                </option>

                <option value="Pizza" <?php if ($edit_item['category'] == 'Pizza') echo 'selected'; ?>>
                    Pizza
                </option>

                <option value="Pasta" <?php if ($edit_item['category'] == 'Pasta') echo 'selected'; ?>>
                    Pasta
                </option>

                <option value="Desserts" <?php if ($edit_item['category'] == 'Desserts') echo 'selected'; ?>>
                    Desserts
                </option>

                <option value="Drinks" <?php if ($edit_item['category'] == 'Drinks') echo 'selected'; ?>>
                    Drinks
                </option>

                <option value="Deals" <?php if ($edit_item['category'] == 'Deals') echo 'selected'; ?>>
                    Deals
                </option>

            </select>

        </div>

        <div class="form-group">

            <label>Image</label>

            <input type="text"
                   name="image"
                   value="<?php echo htmlspecialchars($edit_item['image']); ?>">

        </div>

        <div class="form-group">

            <label>Availability</label>

            <select name="availability">

                <option value="1"
                    <?php if ($edit_item['availability'] == 1) echo 'selected'; ?>>
                    Available
                </option>

                <option value="0"
                    <?php if ($edit_item['availability'] == 0) echo 'selected'; ?>>
                    Not Available
                </option>

            </select>

        </div>

        <button type="submit" name="update_item" class="admin-btn">
            Update Menu Item
        </button>

        <a href="admin_menu.php" class="cancel-btn">
            Cancel
        </a>

    </form>

</div>

<?php endif; ?>

<!-- Menu Items List -->

<div class="dashboard-card menu-list-card">

    <h2>Menu Items</h2>

    <br>

    <div class="table-container">

        <table class="admin-table">

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Item Name</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Availability</th>
                    <th>Action</th>
                </tr>

            </thead>

            <tbody>

                <?php while ($row = mysqli_fetch_assoc($result)): ?>

                    <tr>

                        <td>
                            <?php echo $row['id']; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['item_name']); ?>
                        </td>

                        <td>
                            Rs. <?php echo $row['price']; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['category']); ?>
                        </td>

                        <td>

                            <?php if ($row['availability'] == 1): ?>

                                <span class="available">
                                    Available
                                </span>

                            <?php else: ?>

                                <span class="not-available">
                                    Not Available
                                </span>

                            <?php endif; ?>

                        </td>

                        <td>

                           <a href="admin_menu.php?edit=<?php echo $row['id']; ?>" class="edit-btn">
    Edit
</a>
 <a href="admin_menu.php?delete=<?php echo $row['id']; ?>"
   class="delete-btn"
   onclick="return confirm('Are you sure you want to delete this item?');">
    Delete
</a>
                        </td>

                    </tr>

                <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</div>

    </main>

</div>

</body>

</html>