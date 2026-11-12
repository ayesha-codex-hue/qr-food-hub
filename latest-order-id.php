<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "qr_foodhub");
if ($conn->connect_error) {
    echo json_encode(['max_id' => 0]);
    exit;
}

$row = $conn->query("SELECT MAX(id) AS m FROM orders")->fetch_assoc();
echo json_encode(['max_id' => (int)($row['m'] ?? 0)]);