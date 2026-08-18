<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/db.php';
header('Content-Type: application/json');

try {
    // শুধু ওই প্রোডাক্টগুলো দেখাবে যেগুলোতে সত্যিকারে Low Stock Alert সেট করা আছে
    // (low_stock_alert = NULL মানে "No Alert" — Query-তে কখনোই আসবে না)
    $products = $pdo->query("
        SELECT id, name, category, stock, low_stock_alert
        FROM products
        WHERE low_stock_alert IS NOT NULL
          AND stock <= low_stock_alert
        ORDER BY stock ASC
    ")->fetchAll();

    echo json_encode([
        "status" => "success",
        "count" => count($products),
        "data" => $products
    ]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}