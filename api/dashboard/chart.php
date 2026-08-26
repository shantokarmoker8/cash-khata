<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/db.php';
header('Content-Type: application/json');

$type = $_GET['type'] ?? 'sales';       // purchase | sales | profit | customer_due | supplier_due | expenses
$days = (int) ($_GET['days'] ?? 7);     // 7 | 30 | 365

$allowedTypes = ['purchase', 'sales', 'profit', 'customer_due', 'supplier_due', 'expenses'];
if (!in_array($type, $allowedTypes)) {
    echo json_encode(["status" => "error", "message" => "Invalid chart type"]);
    exit;
}

try {
    $labels = [];
    $values = [];

    switch ($type) {
        case 'purchase':
            $stmt = $pdo->prepare("
                SELECT DATE(created_at) AS d, SUM(total_amount) AS val
                FROM purchases
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
                GROUP BY DATE(created_at) ORDER BY d ASC
            ");
            $stmt->execute(['days' => $days]);
            $rows = $stmt->fetchAll();
            break;

        case 'sales':
            $stmt = $pdo->prepare("
                SELECT DATE(created_at) AS d, SUM(total_amount) AS val
                FROM sales
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
                GROUP BY DATE(created_at) ORDER BY d ASC
            ");
            $stmt->execute(['days' => $days]);
            $rows = $stmt->fetchAll();
            break;

        case 'expenses':
            $stmt = $pdo->prepare("
                SELECT DATE(created_at) AS d, SUM(amount) AS val
                FROM expenses
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
                GROUP BY DATE(created_at) ORDER BY d ASC
            ");
            $stmt->execute(['days' => $days]);
            $rows = $stmt->fetchAll();
            break;

        case 'customer_due':
            $stmt = $pdo->prepare("SELECT name, due FROM customers WHERE due > 0 ORDER BY due DESC LIMIT 10");
            $stmt->execute();
            $rows = $stmt->fetchAll();
            foreach ($rows as $r) {
                $labels[] = $r['name'];
                $values[] = (float) $r['due'];
            }
            echo json_encode(["status" => "success", "labels" => $labels, "values" => $values, "chart_type" => "bar"]);
            exit;

        case 'supplier_due':
            $stmt = $pdo->prepare("SELECT name, due FROM suppliers WHERE due > 0 ORDER BY due DESC LIMIT 10");
            $stmt->execute();
            $rows = $stmt->fetchAll();
            foreach ($rows as $r) {
                $labels[] = $r['name'];
                $values[] = (float) $r['due'];
            }
            echo json_encode(["status" => "success", "labels" => $labels, "values" => $values, "chart_type" => "bar"]);
            exit;

        case 'profit':
            $salesStmt = $pdo->prepare("
                SELECT DATE(s.created_at) AS d,
                       SUM(s.total_amount) AS sales_val,
                       SUM(s.quantity * p.purchase_price) AS cogs_val
                FROM sales s
                INNER JOIN products p ON p.id = s.product_id
                WHERE s.created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
                GROUP BY DATE(s.created_at)
            ");
            $salesStmt->execute(['days' => $days]);
            $salesRows = $salesStmt->fetchAll();

            $serviceStmt = $pdo->prepare("
                SELECT DATE(created_at) AS d,
                       SUM(service_charge) AS service_charge_val,
                       SUM(parts_total) AS parts_total_val,
                       SUM(discount_amount) AS discount_val
                FROM services
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
                GROUP BY DATE(created_at)
            ");
            $serviceStmt->execute(['days' => $days]);
            $serviceRows = $serviceStmt->fetchAll();

            $servicePartsCogsStmt = $pdo->prepare("
                SELECT DATE(sv.created_at) AS d,
                       SUM(sp.quantity * pr.purchase_price) AS parts_cogs_val
                FROM service_parts sp
                INNER JOIN services sv ON sv.id = sp.service_id
                INNER JOIN products pr ON pr.id = sp.product_id
                WHERE sv.created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
                GROUP BY DATE(sv.created_at)
            ");
            $servicePartsCogsStmt->execute(['days' => $days]);
            $servicePartsCogsRows = $servicePartsCogsStmt->fetchAll();

            $dailyProfit = [];

            foreach ($salesRows as $r) {
                $dailyProfit[$r['d']] = ($dailyProfit[$r['d']] ?? 0) + ((float) $r['sales_val'] - (float) $r['cogs_val']);
            }

            $servicePartsCogsByDate = [];
            foreach ($servicePartsCogsRows as $r) {
                $servicePartsCogsByDate[$r['d']] = (float) $r['parts_cogs_val'];
            }

            foreach ($serviceRows as $r) {
                $partsCogs = $servicePartsCogsByDate[$r['d']] ?? 0;
                $partsProfit = (float) $r['parts_total_val'] - $partsCogs;
                $serviceProfit = (float) $r['service_charge_val'] + $partsProfit - (float) $r['discount_val'];
                $dailyProfit[$r['d']] = ($dailyProfit[$r['d']] ?? 0) + $serviceProfit;
            }

            ksort($dailyProfit);

            foreach ($dailyProfit as $d => $val) {
                $labels[] = date('M d', strtotime($d));
                $values[] = (float) $val;
            }

            echo json_encode(["status" => "success", "labels" => $labels, "values" => $values, "chart_type" => "line"]);
            exit;
    }

    foreach ($rows as $r) {
        $labels[] = date('M d', strtotime($r['d']));
        $values[] = (float) $r['val'];
    }

    echo json_encode(["status" => "success", "labels" => $labels, "values" => $values, "chart_type" => "line"]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}