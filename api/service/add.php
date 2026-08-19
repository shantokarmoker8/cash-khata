<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/db.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$customerId         = isset($input['customer_id']) && $input['customer_id'] !== '' ? (int) $input['customer_id'] : null;
$mobileBrand        = trim($input['mobile_brand'] ?? '');
$mobileModel        = '';
$problemDescription = trim($input['problem_description'] ?? '');
$serviceCharge      = (float) ($input['service_charge'] ?? 0);
$discountAmount     = (float) ($input['discount_amount'] ?? 0);
$paidAmount         = (float) ($input['paid_amount'] ?? 0);
$parts              = is_array($input['parts'] ?? null) ? $input['parts'] : [];

// ============ Customer Name/Mobile সবসময় Server-side থেকে বের করা হচ্ছে (Client কে বিশ্বাস করা হচ্ছে না) ============
if ($customerId) {
    $custStmt = $pdo->prepare("SELECT name, mobile FROM customers WHERE id = ?");
    $custStmt->execute([$customerId]);
    $custRow = $custStmt->fetch();
    if (!$custRow) {
        echo json_encode(["status" => "error", "message" => "Selected customer was not found"]);
        exit;
    }
    $customerName = $custRow['name'];
    $customerMobile = $custRow['mobile'];
} else {
    $customerName = 'Walk-in Customer';
    $customerMobile = '-';
}

if ($mobileBrand === '') {
    echo json_encode(["status" => "error", "message" => "Mobile Name is required"]);
    exit;
}
if ($problemDescription === '') {
    echo json_encode(["status" => "error", "message" => "Problem Description is required"]);
    exit;
}
if ($serviceCharge < 0) {
    echo json_encode(["status" => "error", "message" => "Service Charge cannot be negative"]);
    exit;
}
if ($discountAmount < 0) {
    echo json_encode(["status" => "error", "message" => "Discount cannot be negative"]);
    exit;
}
if ($paidAmount < 0) {
    echo json_encode(["status" => "error", "message" => "Pay Amount cannot be negative"]);
    exit;
}

// ============ Merge duplicate part rows (একই Product দুইবার দিলে Quantity যোগ হবে) ============
$mergedParts = [];
foreach ($parts as $p) {
    $pid = (int) ($p['product_id'] ?? 0);
    $qty = (int) ($p['quantity'] ?? 0);
    if ($pid <= 0 || $qty <= 0) continue;
    if (isset($mergedParts[$pid])) {
        $mergedParts[$pid] += $qty;
    } else {
        $mergedParts[$pid] = $qty;
    }
}

try {
    $pdo->beginTransaction();

    $partsTotal = 0;
    $partRows = []; // [product_id, quantity, price, total]

    // ============ Validate + Lock প্রতিটা Part Product (শুধু category = 'part') ============
    foreach ($mergedParts as $productId => $qty) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? FOR UPDATE");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if (!$product) {
            $pdo->rollBack();
            echo json_encode(["status" => "error", "message" => "One of the selected parts was not found"]);
            exit;
        }
        if ($product['category'] !== 'part') {
            $pdo->rollBack();
            echo json_encode(["status" => "error", "message" => "\"{$product['name']}\" is not in the Parts category. Only Parts can be used in Mobile Servicing."]);
            exit;
        }
        if ($product['stock'] < $qty) {
            $pdo->rollBack();
            echo json_encode(["status" => "error", "message" => "Insufficient Stock for \"{$product['name']}\". Available: {$product['stock']}"]);
            exit;
        }

        $price = (float) $product['sale_price'];
        $total = $price * $qty;
        $partsTotal += $total;

        $partRows[] = [
            'product_id' => $productId,
            'quantity'   => $qty,
            'price'      => $price,
            'total'      => $total
        ];

        // Stock সাথে সাথে কমিয়ে দেওয়া হচ্ছে
        $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?")->execute([$qty, $productId]);
    }

    $grossAmount = $serviceCharge + $partsTotal;

    if ($discountAmount > $grossAmount) {
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => "Discount cannot exceed the total amount"]);
        exit;
    }

    $totalAmount = $grossAmount - $discountAmount;

    if ($paidAmount > $totalAmount) {
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => "Pay Amount cannot exceed Total Amount (after discount)"]);
        exit;
    }

    $dueAmount = $totalAmount - $paidAmount;

    if ($dueAmount > 0 && !$customerId) {
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => "Please select an existing Customer when there is a Due amount"]);
        exit;
    }

    $paymentType = ($dueAmount <= 0) ? 'cash' : 'due';

    $insertService = $pdo->prepare("
        INSERT INTO services
            (customer_id, customer_name, customer_mobile, mobile_brand, mobile_model, problem_description,
             service_charge, parts_total, discount_amount, total_amount, payment_type, paid_amount, due_amount,
             status, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?)
    ");
    $insertService->execute([
        $customerId, $customerName, $customerMobile, $mobileBrand, $mobileModel, $problemDescription,
        $serviceCharge, $partsTotal, $discountAmount, $totalAmount, $paymentType, $paidAmount, $dueAmount,
        $_SESSION['user_id']
    ]);
    $serviceId = $pdo->lastInsertId();

    $insertPart = $pdo->prepare("INSERT INTO service_parts (service_id, product_id, quantity, price, total) VALUES (?, ?, ?, ?, ?)");
    foreach ($partRows as $row) {
        $insertPart->execute([$serviceId, $row['product_id'], $row['quantity'], $row['price'], $row['total']]);
    }

    // Cash Balance বৃদ্ধি (Customer টাকা পরিশোধ করছে)
    $settings = $pdo->query("SELECT cash_balance FROM settings LIMIT 1 FOR UPDATE")->fetch();
    $newCash = (float) $settings['cash_balance'] + $paidAmount;
    $pdo->prepare("UPDATE settings SET cash_balance = ?")->execute([$newCash]);

    if ($dueAmount > 0 && $customerId) {
        $pdo->prepare("UPDATE customers SET due = due + ? WHERE id = ?")->execute([$dueAmount, $customerId]);
    }

    $pdo->commit();

    echo json_encode([
        "status" => "success",
        "message" => "Service entry saved successfully",
        "service_id" => $serviceId,
        "cash_balance" => $newCash
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}