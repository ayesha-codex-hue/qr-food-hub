<?php require_once "auth.php"; ?>
<?php
include("db.php");

// Handle payment status update
if (isset($_POST['update_payment_status'])) {
    $order_id       = (int)$_POST['order_id'];
    $payment_status = $_POST['payment_status'];

    $stmt = mysqli_prepare($conn, "UPDATE orders SET payment_status = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $payment_status, $order_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: admin_payment.php");
    exit;
}

// Summary numbers
$paid_count   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM orders WHERE payment_status = 'Paid'"))['c'];
$pending_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM orders WHERE payment_status = 'Pending'"))['c'];

$today = date("Y-m-d");

$collected_row = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COALESCE(SUM(oi.price * oi.quantity), 0) AS t
    FROM orders o
    JOIN order_items oi ON oi.order_id = o.id
    WHERE DATE(o.order_time) = '$today' AND o.payment_status = 'Paid'
"));
$collected_today = $collected_row['t'];

$pending_row = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COALESCE(SUM(oi.price * oi.quantity), 0) AS t
    FROM orders o
    JOIN order_items oi ON oi.order_id = o.id
    WHERE o.payment_status = 'Pending'
"));
$pending_amount = $pending_row['t'];

// All orders with their totals
$orders = mysqli_query($conn, "
    SELECT o.id, o.table_no, o.payment_method, o.payment_status, o.order_time,
           COALESCE(SUM(oi.price * oi.quantity), 0) AS amount
    FROM orders o
    LEFT JOIN order_items oi ON oi.order_id = o.id
    GROUP BY o.id
    ORDER BY o.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="20">
    <title>QR Food Hub - Payments</title>
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
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="admin_menu.php">Menu Management</a></li>
            <li><a href="admin_orders.php">Orders</a></li>
            <li><a href="admin_payment.php" class="active">Payments</a></li>
            <li><a href="admin_qr.php">QR Codes</a></li>
            <li><a href="index.php">View Website</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">

        <div class="admin-header">
            <h1>Payment Management</h1>
        </div>

        <div class="pay-cards">
            <div class="pay-card green">
                <h3>Collected Today</h3>
                <div class="val">Rs. <?php echo number_format($collected_today); ?></div>
            </div>
            <div class="pay-card red">
                <h3>Pending Amount</h3>
                <div class="val">Rs. <?php echo number_format($pending_amount); ?></div>
            </div>
            <div class="pay-card">
                <h3>Paid Orders</h3>
                <div class="val"><?php echo $paid_count; ?></div>
            </div>
            <div class="pay-card">
                <h3>Pending Orders</h3>
                <div class="val"><?php echo $pending_count; ?></div>
            </div>
        </div>

        <div class="dashboard-card">

            <h2>All Orders</h2>
            <br>

            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Table</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php if ($orders && mysqli_num_rows($orders) > 0): ?>
                            <?php while ($o = mysqli_fetch_assoc($orders)): ?>

                                <?php
                                $cls = 'badge-pending';
                                if ($o['payment_status'] === 'Paid')   $cls = 'badge-paid';
                                if ($o['payment_status'] === 'Failed') $cls = 'badge-failed';
                                ?>

                                <tr>
                                    <td>#<?php echo $o['id']; ?></td>
                                    <td>Table <?php echo $o['table_no']; ?></td>
                                    <td>Rs. <?php echo number_format($o['amount']); ?></td>
                                    <td><?php echo htmlspecialchars($o['payment_method']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $cls; ?>">
                                            <?php echo htmlspecialchars($o['payment_status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($o['order_time']); ?></td>
                                    <td>
                                        <form method="POST" style="display:flex; gap:6px;">
                                            <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                            <select name="payment_status">
                                                <option value="Pending" <?php if ($o['payment_status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                                <option value="Paid"    <?php if ($o['payment_status'] == 'Paid')    echo 'selected'; ?>>Paid</option>
                                                <option value="Failed"  <?php if ($o['payment_status'] == 'Failed')  echo 'selected'; ?>>Failed</option>
                                            </select>
                                            <button type="submit" name="update_payment_status" class="admin-btn">
                                                Update
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align:center;">No orders yet.</td>
                            </tr>
                        <?php endif; ?>

                    </tbody>
                </table>
            </div>

        </div>

    </main>

</div>

</body>

</html>