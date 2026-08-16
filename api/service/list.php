<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/db.php';
header('Content-Type: application/json');

try {
    $search = trim($_GET['search'] ?? '');
    $status = trim($_GET['status'] ?? '');

    $sql = "SELECT * FROM services";
    $where = [];
    $params = [];

    if ($search !== '') {
        $where[] = "(customer_name LIKE ? OR customer_mobile LIKE ? OR mobile_brand LIKE ? OR mobile_model LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    $allowedStatus = ['pending', 'in_progress', 'completed', 'delivered'];
    if ($status !== '' && in_array($status, $allowedStatus, true)) {
        $where[] = "status = ?";
        $params[] = $status;
    }

    if ($where) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    $sql .= " ORDER BY id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $services = $stmt->fetchAll();

    if ($services) {
        $ids = array_column($services, 'id');
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $partsStmt = $pdo->prepare("
            SELECT sp.service_id, sp.product_id, sp.quantity, sp.price, sp.total, p.name AS product_name
            FROM service_parts sp
            INNER JOIN products p ON p.id = sp.product_id
            WHERE sp.service_id IN ($placeholders)
        ");
        $partsStmt->execute($ids);
        $allParts = $partsStmt->fetchAll();

        $partsByService = [];
        foreach ($allParts as $row) {
            $partsByService[$row['service_id']][] = $row;
        }

        foreach ($services as &$s) {
            $s['parts'] = $partsByService[$s['id']] ?? [];
        }
        unset($s);
    }

    echo json_encode(["status" => "success", "data" => $services]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}