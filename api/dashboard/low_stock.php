<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/db.php';
header('Content-Type: application/json');

try {
    $products = $pdo->query("
        SELECT id, name, category, stock, low_stock_alert
        FROM products
        WHERE stock <= low_stock_alert
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