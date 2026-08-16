<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/db.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$id     = (int) ($input['id'] ?? 0);
$status = trim($input['status'] ?? '');

$allowedStatus = ['pending', 'in_progress', 'completed', 'delivered'];

if ($id <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid Service ID"]);
    exit;
}
if (!in_array($status, $allowedStatus, true)) {
    echo json_encode(["status" => "error", "message" => "Invalid Status"]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id FROM services WHERE id = ?");
    $stmt->execute([$id]);
    if (!$stmt->fetch()) {
        echo json_encode(["status" => "error", "message" => "Service record not found"]);
        exit;
    }

    $pdo->prepare("UPDATE services SET status = ? WHERE id = ?")->execute([$status, $id]);

    echo json_encode(["status" => "success", "message" => "Status updated successfully"]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}