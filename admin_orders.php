<?php require_once "auth.php"; ?>
<?php

include("db.php");
// Update Order Status

if (isset($_POST['update_status'])) {

    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    $update_sql = "UPDATE orders
                   SET status = ?
                   WHERE id = ?";

    $update_stmt = mysqli_prepare($conn, $update_sql);

    mysqli_stmt_bind_param(
        $update_stmt,
        "si",
        $status,
        $order_id
    );

    mysqli_stmt_execute($update_stmt);

    mysqli_stmt_close($update_stmt);

    header("Location: admin_orders.php");
    exit;
}
// Update Payment Status

if (isset($_POST['update_payment_status'])) {

    $order_id = $_POST['order_id'];
    $payment_status = $_POST['payment_status'];

    $payment_sql = "UPDATE orders
                    SET payment_status = ?
                    WHERE id = ?";

    $payment_stmt = mysqli_prepare($conn, $payment_sql);

    mysqli_stmt_bind_param(
        $payment_stmt,
        "si",
        $payment_status,
        $order_id
    );

    mysqli_stmt_execute($payment_stmt);

    mysqli_stmt_close($payment_stmt);

    header("Location: admin_orders.php");
    exit;
}

// Get all orders
$order_sql = "SELECT * FROM orders ORDER BY order_time DESC";

$order_result = mysqli_query($conn, $order_sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Food Hub - Orders</title>

    <link rel="stylesheet" href="admin.css">

</head>
<!-- New order alert + auto-refresh -->
<script>
(function() {
    async function checkForNewOrders() {
        try {
            const res = await fetch('latest-order-id.php');
            const data = await res.json();
            const maxId = data.max_id;

            const lastSeen = parseInt(localStorage.getItem('lastSeenOrder') || '0', 10);

            // New order detected
            if (maxId > lastSeen && lastSeen > 0) {

                // 1) Loud two-tone beep (Web Audio API)
                try {
                    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    const playBeep = (freq, startTime, duration) => {
                        const osc = audioCtx.createOscillator();
                        const gain = audioCtx.createGain();
                        osc.connect(gain);
                        gain.connect(audioCtx.destination);
                        osc.type = 'sine';
                        osc.frequency.value = freq;
                        gain.gain.setValueAtTime(1.0, startTime);
                        gain.gain.exponentialRampToValueAtTime(0.001, startTime + duration);
                        osc.start(startTime);
                        osc.stop(startTime + duration);
                    };
                    const now = audioCtx.currentTime;
                    playBeep(880, now, 0.3);
                    playBeep(1100, now + 0.35, 0.4);
                } catch (e) {}

                // 2) Flash browser tab title
                if (!window._flashing) {
                    window._flashing = true;
                    const originalTitle = document.title;
                    let flip = true;
                    setInterval(() => {
                        document.title = flip ? '🔔 NEW ORDER!' : originalTitle;
                        flip = !flip;
                    }, 700);
                }

                // 3) Gold slide-in banner
                const toast = document.createElement('div');
                toast.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: #FFD700;
                    color: #281C02;
                    padding: 15px 22px;
                    border-radius: 10px;
                    font-weight: bold;
                    font-family: Arial;
                    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
                    z-index: 99999;
                    transition: transform 0.4s, opacity 0.4s;
                    transform: translateX(400px);
                    opacity: 0;
                    cursor: pointer;
                `;
                toast.textContent = '🔔 New order received! Refreshing...';
                toast.onclick = () => location.reload();
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.style.transform = 'translateX(0)';
                    toast.style.opacity = '1';
                }, 50);

                // Update localStorage now so we don't re-alert
                localStorage.setItem('lastSeenOrder', maxId);

                // Reload page after 2.5 seconds to show new order in list
                setTimeout(() => location.reload(), 2500);

                return; // stop here — page is about to reload
            }

            // First run ever — just remember the current max
            if (maxId > 0) {
                localStorage.setItem('lastSeenOrder', maxId);
            }
        } catch (e) {
            // silent fail
        }
    }

    checkForNewOrders();
    setInterval(checkForNewOrders, 10000); // check every 10 seconds
})();
</script>
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
                <a href="admin_dashboard.php">
                    Dashboard
                </a>
            </li>

            <li>
                <a href="admin_menu.php">
                    Menu Management
                </a>
            </li>

            <li>
                <a href="admin_orders.php" class="active">
                    Orders
                </a>
            </li>

            <li>
                <a href="admin_payment.php">
                    Payments
                </a>
            </li>
<li><a href="admin_qr.php">QR Codes</a></li>
            <li>
                <a href="index.php">
                    View Website
                </a>
            </li>

            <li>
                <a href="logout.php">
                    Logout
                </a>
            </li>

        </ul>

    </aside>


    <!-- Main Content -->

    <main class="admin-main">

        <div class="admin-header">

            <h1>Orders Management</h1>

        </div>


        <?php while ($order = mysqli_fetch_assoc($order_result)): ?>

            <div class="dashboard-card order-card">

                <div class="order-header">

                    <div>

                        <h2>
                            Order #<?php echo $order['id']; ?>
                        </h2>

                        <p>
                            Table:
                            <?php echo $order['table_no']; ?>
                        </p>

                    </div>


                    <div>

                        <p>
                            Payment:
                            <?php echo htmlspecialchars($order['payment_method']); ?>
                        </p>

                        <p>
                            Time:
                            <?php echo $order['order_time']; ?>
                        </p>
                         <p>
    Status:
    <strong>
        <?php echo htmlspecialchars($order['status']); ?>
    </strong>
</p>
<p>
    Payment Status:
    <strong>
        <?php echo htmlspecialchars($order['payment_status']); ?>
    </strong>
</p>

<form method="POST">

    <input
        type="hidden"
        name="order_id"
        value="<?php echo $order['id']; ?>"
    >

    <select name="payment_status">

        <option value="Pending"
            <?php if ($order['payment_status'] == 'Pending') echo 'selected'; ?>>
            Pending
        </option>

        <option value="Paid"
            <?php if ($order['payment_status'] == 'Paid') echo 'selected'; ?>>
            Paid
        </option>

        <option value="Failed"
            <?php if ($order['payment_status'] == 'Failed') echo 'selected'; ?>>
            Failed
        </option>

    </select>

    <button
        type="submit"
        name="update_payment_status"
        class="admin-btn">
        Update Payment
    </button>

</form>

<form method="POST">

    <input
        type="hidden"
        name="order_id"
        value="<?php echo $order['id']; ?>"
    >

    <select name="status">

        <option value="Pending"
            <?php if ($order['status'] == 'Pending') echo 'selected'; ?>>
            Pending
        </option>

        <option value="Confirmed"
            <?php if ($order['status'] == 'Confirmed') echo 'selected'; ?>>
            Confirmed
        </option>

        <option value="Preparing"
            <?php if ($order['status'] == 'Preparing') echo 'selected'; ?>>
            Preparing
        </option>

        <option value="Ready"
            <?php if ($order['status'] == 'Ready') echo 'selected'; ?>>
            Ready
        </option>

        <option value="Completed"
            <?php if ($order['status'] == 'Completed') echo 'selected'; ?>>
            Completed
        </option>

        <option value="Cancelled"
            <?php if ($order['status'] == 'Cancelled') echo 'selected'; ?>>
            Cancelled
        </option>

    </select>

    <button
        type="submit"
        name="update_status"
        class="admin-btn">
        Update Status
    </button>

</form>

                    </div>

                </div>


                <hr>


                <h3>Order Items</h3>

                <br>


                <table class="admin-table">

                    <thead>

                        <tr>

                            <th>Item</th>

                            <th>Price</th>

                            <th>Quantity</th>

                            <th>Subtotal</th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php

                        $order_id = $order['id'];

                        $item_sql = "SELECT * FROM order_items
                                     WHERE order_id = ?";

                        $item_stmt = mysqli_prepare($conn, $item_sql);

                        mysqli_stmt_bind_param(
                            $item_stmt,
                            "i",
                            $order_id
                        );

                        mysqli_stmt_execute($item_stmt);

                        $item_result = mysqli_stmt_get_result($item_stmt);

                        $total = 0;

                        ?>


                        <?php while ($item = mysqli_fetch_assoc($item_result)): ?>

                            <?php

                            $subtotal =
                                $item['price'] * $item['quantity'];

                            $total += $subtotal;

                            ?>

                            <tr>

                                <td>
                                    <?php echo htmlspecialchars($item['item_name']); ?>
                                </td>

                                <td>
                                    Rs. <?php echo $item['price']; ?>
                                </td>

                                <td>
                                    <?php echo $item['quantity']; ?>
                                </td>

                                <td>
                                    Rs. <?php echo $subtotal; ?>
                                </td>

                            </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>


                <div class="order-total">

                    <strong>
                        Total: Rs. <?php echo $total; ?>
                    </strong>

                </div>


                <?php

                mysqli_stmt_close($item_stmt);

                ?>

            </div>

            <br>

        <?php endwhile; ?>


    </main>

</div>

</body>

</html>