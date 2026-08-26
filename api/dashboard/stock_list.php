<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/db.php';
header('Content-Type: application/json');

$search = trim($_GET['search'] ?? '');
$filter = $_GET['filter'] ?? 'all';

try {
    $overall = $pdo->query("
        SELECT
            COALESCE(SUM(stock * purchase_price), 0) AS total_value,
            COUNT(*) AS total_products,
            SUM(CASE WHEN low_stock_alert IS NOT NULL AND stock <= low_stock_alert THEN 1 ELSE 0 END) AS low_count
        FROM products
    ")->fetch();

    $sql = "SELECT id, name, category, stock, purchase_price, sale_price, low_stock_alert FROM products";
    $conditions = [];
    $params = [];

    if ($search !== '') {
        $conditions[] = "name LIKE ?";
        $params[] = "%$search%";
    }

    if ($filter === 'low') {
        $conditions[] = "low_stock_alert IS NOT NULL AND stock <= low_stock_alert";
    }

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }

    $sql .= " ORDER BY name ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();

    $data = [];
    foreach ($products as $p) {
        $stockValue = $p['stock'] * $p['purchase_price'];
        $isLow = $p['low_stock_alert'] !== null && $p['stock'] <= $p['low_stock_alert'];

        $data[] = [
            "id"              => (int) $p['id'],
            "name"            => $p['name'],
            "category"        => $p['category'],
            "stock"           => (int) $p['stock'],
            "purchase_price"  => (float) $p['purchase_price'],
            "sale_price"      => (float) $p['sale_price'],
            "low_stock_alert" => $p['low_stock_alert'] !== null ? (int) $p['low_stock_alert'] : null,
            "stock_value"     => (float) $stockValue,
            "is_low_stock"    => $isLow
        ];
    }

    echo json_encode([
        "status"          => "success",
        "total_value"     => (float) $overall['total_value'],
        "total_products"  => (int) $overall['total_products'],
        "low_stock_count" => (int) $overall['low_count'],
        "data"            => $data
    ]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}