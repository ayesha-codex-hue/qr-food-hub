
<?php

$conn = new mysqli("localhost", "root", "", "qr_foodhub");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read JSON data sent from customer website
$data = json_decode(file_get_contents("php://input"), true);


// Check data
if (!$data) {
    die("No data received");
}

// Get customer information
$table_no = isset($data['table_no']) ? (int)$data['table_no'] : 0;
$payment_method = $data['payment_method'] ?? '';
$cart_items = $data['cart_items'] ?? [];

// Validate table number
if ($table_no <= 0) {
    die("Please enter a valid table number");
}

// Validate payment method
if ($payment_method == '') {
    die("Please select a payment method");
}

// Validate cart
if (!is_array($cart_items) || count($cart_items) == 0) {
    die("Cart is empty");
}

// Insert order
$stmt = $conn->prepare(
    "INSERT INTO orders (table_no, payment_method) VALUES (?, ?)"
);

$stmt->bind_param("is", $table_no, $payment_method);

if (!$stmt->execute()) {
    die("Order insert failed: " . $conn->error);
}

$order_id = $stmt->insert_id;

$stmt->close();

// Insert order items
$item_stmt = $conn->prepare(
    "INSERT INTO order_items
    (order_id, item_name, price, quantity)
    VALUES (?, ?, ?, ?)"
);

foreach ($cart_items as $item) {

    $name = $item['name'];
    $price = $item['price'];
    $qty = $item['quantity'];

    $item_stmt->bind_param(
        "isdi",
        $order_id,
        $name,
        $price,
        $qty
    );

    $item_stmt->execute();
}

$item_stmt->close();

$conn->close();

echo "Thank you! Your order #$order_id for Table No. $table_no is confirmed. It will be served in 15-20 mins.";

?>