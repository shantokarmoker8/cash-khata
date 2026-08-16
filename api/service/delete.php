<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/db.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$id = (int) ($input['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid Service ID"]);
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ? FOR UPDATE");
    $stmt->execute([$id]);
    $service = $stmt->fetch();

    if (!$service) {
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => "Service record not found"]);
        exit;
    }

    // ============ Stock ফেরত দেওয়া (প্রতিটা Part) ============
    $partsStmt = $pdo->prepare("SELECT * FROM service_parts WHERE service_id = ?");
    $partsStmt->execute([$id]);
    $parts = $partsStmt->fetchAll();

    foreach ($parts as $part) {
        $pdo->prepare("UPDATE products SET stock = stock + ? WHERE id = ?")
            ->execute([$part['quantity'], $part['product_id']]);
    }

    // ============ Cash Balance Reverse (যা Paid হয়েছিল তা বিয়োগ) ============
    if ($service['paid_amount'] > 0) {
        $settings = $pdo->query("SELECT cash_balance FROM settings LIMIT 1 FOR UPDATE")->fetch();
        $currentCash = (float) $settings['cash_balance'];
        if ($currentCash < $service['paid_amount']) {
            $pdo->rollBack();
            echo json_encode(["status" => "error", "message" => "Cannot delete: insufficient current cash balance to reverse this transaction"]);
            exit;
        }
        $newCash = $currentCash - $service['paid_amount'];
        $pdo->prepare("UPDATE settings SET cash_balance = ?")->execute([$newCash]);
    } else {
        $newCash = (float) $pdo->query("SELECT cash_balance FROM settings LIMIT 1")->fetch()['cash_balance'];
    }

    // ============ Customer Due Reverse ============
    if ($service['due_amount'] > 0 && $service['customer_id']) {
        $pdo->prepare("UPDATE customers SET due = GREATEST(due - ?, 0) WHERE id = ?")
            ->execute([$service['due_amount'], $service['customer_id']]);
    }

    $pdo->prepare("DELETE FROM services WHERE id = ?")->execute([$id]);

    $pdo->commit();

    echo json_encode(["status" => "success", "message" => "Service entry deleted successfully", "cash_balance" => $newCash]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}