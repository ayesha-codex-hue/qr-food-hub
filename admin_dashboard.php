<?php require_once "auth.php"; ?>
<?php
include("db.php");

$today = date("Y-m-d");

// Total menu items
$total_menu = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS c FROM menu")
)['c'];

// Today's orders count
$today_orders = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS c FROM orders WHERE DATE(order_time) = '$today'")
)['c'];

// Today's revenue (only Paid)
$today_revenue = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COALESCE(SUM(oi.price * oi.quantity), 0) AS t
    FROM orders o
    JOIN order_items oi ON oi.order_id = o.id
    WHERE DATE(o.order_time) = '$today' AND o.payment_status = 'Paid'
"))['t'];

// Pending orders (not completed / cancelled)
$pending_orders = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(*) AS c FROM orders
        WHERE status NOT IN ('Completed', 'Cancelled')
    ")
)['c'];

// Latest 5 orders
$latest = mysqli_query($conn, "
    SELECT o.id, o.table_no, o.payment_method, o.status, o.payment_status, o.order_time,
           COALESCE(SUM(oi.price * oi.quantity), 0) AS amount
    FROM orders o
    LEFT JOIN order_items oi ON oi.order_id = o.id
    GROUP BY o.id
    ORDER BY o.id DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="20">
    <title>QR Food Hub - Dashboard</title>
    <link rel="stylesheet" href="admin.css">

   
</head>
<!-- New order alert -->
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

                // 1) Play a beep
                // Louder double beep using Web Audio API
try {
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();

    const playBeep = (freq, startTime, duration) => {
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.type = 'sine';
        osc.frequency.value = freq;
        gain.gain.setValueAtTime(0.4, startTime);
        gain.gain.exponentialRampToValueAtTime(0.001, startTime + duration);
        osc.start(startTime);
        osc.stop(startTime + duration);
    };

    const now = audioCtx.currentTime;
    playBeep(880, now, 0.2);        // first beep
    playBeep(1100, now + 0.25, 0.3); // second beep, higher pitch
} catch (e) {
    // audio not supported — banner still shows
}
                
                

                // 2) Flash the browser tab title
                if (!window._flashing) {
                    window._flashing = true;
                    const originalTitle = document.title;
                    let flip = true;
                    setInterval(() => {
                        document.title = flip ? '🔔 NEW ORDER!' : originalTitle;
                        flip = !flip;
                    }, 700);
                }

                // 3) Slide-in banner
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
                toast.textContent = '🔔 New order received! Tap to view.';
                toast.onclick = () => location.href = 'admin_orders.php';
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.style.transform = 'translateX(0)';
                    toast.style.opacity = '1';
                }, 50);

                setTimeout(() => {
                    toast.style.transform = 'translateX(400px)';
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 400);
                }, 6000);
            }

            // Remember this ID so we don't alert again next time
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
            <li><a href="admin_dashboard.php" class="active">Dashboard</a></li>
            <li><a href="admin_menu.php">Menu Management</a></li>
            <li><a href="admin_orders.php">Orders</a></li>
            <li><a href="admin_payment.php">Payments</a></li>
            <li><a href="admin_qr.php">QR Codes</a></li>
            <li><a href="index.php">View Website</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">

        <div class="admin-header">
            <h1>Dashboard</h1>
            <p style="color:#777;">Today: <?php echo date("d M Y"); ?></p>
        </div>

        <div class="dash-cards">
            <div class="dash-card blue">
                <h3>Today's Orders</h3>
                <div class="val"><?php echo $today_orders; ?></div>
            </div>
            <div class="dash-card green">
                <h3>Today's Revenue</h3>
                <div class="val">Rs. <?php echo number_format($today_revenue); ?></div>
            </div>
            <div class="dash-card orange">
                <h3>Pending Orders</h3>
                <div class="val"><?php echo $pending_orders; ?></div>
            </div>
            <div class="dash-card">
                <h3>Menu Items</h3>
                <div class="val"><?php echo $total_menu; ?></div>
            </div>
        </div>

        <div class="dashboard-card">
            <h2>Latest Orders</h2>
            <br>

            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Table</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($latest && mysqli_num_rows($latest) > 0): ?>
                            <?php while ($o = mysqli_fetch_assoc($latest)): ?>
                                <tr>
                                    <td>#<?php echo $o['id']; ?></td>
                                    <td>Table <?php echo $o['table_no']; ?></td>
                                    <td>Rs. <?php echo number_format($o['amount']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($o['payment_method']); ?>
                                        (<?php echo $o['payment_status']; ?>)
                                    </td>
                                    <td><?php echo htmlspecialchars($o['status']); ?></td>
                                    <td><?php echo htmlspecialchars($o['order_time']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align:center;">No orders yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

</div>

</body>
</html>