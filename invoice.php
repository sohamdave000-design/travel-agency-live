<?php
require_once 'config/database.php';

if (!isLoggedIn()) {
    redirect('login.html');
}

if (!isset($_GET['id'])) {
    redirect('dashboard.html');
}

$booking_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch booking with payment info
$stmt = $pdo->prepare("
    SELECT b.*, p.payment_method, p.transaction_id, p.card_last4, p.payment_date, p.amount as paid_amount, p.status as payment_status
    FROM bookings b
    LEFT JOIN payments p ON p.booking_id = b.id
    WHERE b.id = ? AND b.user_id = ?
");
$stmt->execute([$booking_id, $user_id]);
$booking = $stmt->fetch();

if (!$booking) {
    redirect('dashboard.html');
}

// Get user info
$user = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$user->execute([$user_id]);
$user = $user->fetch();

// Get item name
$item_name = 'N/A';
switch($booking['booking_type']) {
    case 'package': $r = $pdo->prepare("SELECT name FROM packages WHERE id=?"); $r->execute([$booking['item_id']]); $d=$r->fetch(); if($d) $item_name=$d['name']; break;
    case 'hotel': $r = $pdo->prepare("SELECT name FROM hotels WHERE id=?"); $r->execute([$booking['item_id']]); $d=$r->fetch(); if($d) $item_name=$d['name']; break;
    case 'bus': $r = $pdo->prepare("SELECT CONCAT(from_location,' → ',to_location) as name FROM buses WHERE id=?"); $r->execute([$booking['item_id']]); $d=$r->fetch(); if($d) $item_name=$d['name']; break;
    case 'rental': $r = $pdo->prepare("SELECT name FROM rentals WHERE id=?"); $r->execute([$booking['item_id']]); $d=$r->fetch(); if($d) $item_name=$d['name']; break;
}

// Parse extra details (for rentals/addons)
$extra = !empty($booking['extra_details']) ? json_decode($booking['extra_details'], true) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?php echo $booking_id; ?> - Travel Agency</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background: #f1f5f9; padding: 2rem; }
        .invoice-container { max-width: 800px; margin: 0 auto; background: white; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden; }
        .invoice-header { background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white; padding: 2rem; display: flex; justify-content: space-between; align-items: center; }
        .invoice-header h1 { font-size: 2rem; }
        .invoice-body { padding: 2rem; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem; }
        .info-block h3 { color: #64748b; font-size: 0.875rem; text-transform: uppercase; margin-bottom: 0.5rem; }
        .info-block p { color: #1e293b; font-weight: 500; }
        .invoice-table { width: 100%; border-collapse: collapse; margin-bottom: 2rem; }
        .invoice-table th { background: #f8fafc; padding: 1rem; text-align: left; color: #475569; border-bottom: 2px solid #e2e8f0; }
        .invoice-table td { padding: 1rem; border-bottom: 1px solid #e2e8f0; color: #334155; }
        .total-row td { font-weight: 700; font-size: 1.125rem; color: #1e293b; border-top: 2px solid #1e293b; }
        .invoice-footer { text-align: center; padding: 1.5rem 2rem; background: #f8fafc; color: #64748b; font-size: 0.875rem; border-top: 1px solid #e2e8f0; }
        .status-badge { padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; }
        .status-confirmed { background: #d1fae5; color: #065f46; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }
        .print-actions { text-align: center; margin: 1.5rem 0; }
        .print-actions button { background: #2563eb; color: white; padding: 0.75rem 2rem; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; margin: 0 0.5rem; }
        .print-actions button:hover { background: #1d4ed8; }
        .print-actions a { display: inline-block; padding: 0.75rem 2rem; color: #64748b; text-decoration: none; font-weight: 500; }
        @media print { .print-actions { display: none; } body { background: white; padding: 0; } .invoice-container { box-shadow: none; } }
    </style>
</head>
<body>
    <div class="print-actions">
        <button onclick="window.print();">📥 Download / Print Invoice</button>
        <a href="dashboard.html">← Back to Dashboard</a>
    </div>

    <div class="invoice-container">
        <div class="invoice-header">
            <div>
                <h1>Travel Agency</h1>
                <p>Your trusted travel partner</p>
            </div>
            <div style="text-align: right;">
                <h2>INVOICE</h2>
                <p>#INV-<?php echo str_pad($booking_id, 5, '0', STR_PAD_LEFT); ?></p>
            </div>
        </div>

        <div class="invoice-body">
            <div class="info-grid">
                <div class="info-block">
                    <h3>Billed To</h3>
                    <p><?php echo htmlspecialchars($user['name']); ?></p>
                    <p style="font-weight: 400; color: #64748b;"><?php echo htmlspecialchars($user['email']); ?></p>
                    <p style="font-weight: 400; color: #64748b;"><?php echo htmlspecialchars($user['phone']); ?></p>
                </div>
                <div class="info-block" style="text-align: right;">
                    <h3>Invoice Details</h3>
                    <p>Date: <?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></p>
                    <p>Payment: <?php 
                        $method = ucfirst(str_replace('_',' ',$booking['payment_method'] ?? 'N/A')); 
                        if ($booking['card_last4']) $method .= " (**** " . $booking['card_last4'] . ")";
                        echo $method;
                    ?></p>
                    <?php if($booking['transaction_id']): ?>
                    <p>Transaction ID: <span style="font-family: monospace; font-size: 0.8rem;"><?php echo $booking['transaction_id']; ?></span></p>
                    <?php endif; ?>
                    <p>Status: <span class="status-badge status-<?php echo $booking['status']; ?>"><?php echo ucfirst($booking['status']); ?></span></p>
                </div>
            </div>

            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Type</th>
                        <th>Dates</th>
                        <th style="text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($item_name); ?></strong>
                            <?php if($extra): ?>
                                <div style="font-size: 0.85rem; color: #64748b; margin-top: 0.25rem;">
                                    <?php if(isset($extra['driver'])): ?>
                                        🚗 <?php echo $extra['driver'] === 'with_driver' ? 'With Driver' : 'Self Drive'; ?>
                                    <?php endif; ?>
                                    <?php if(!empty($extra['addons'])): ?>
                                        · 🛠️ <?php echo implode(', ', $extra['addons']); ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td style="text-transform: capitalize;"><?php echo $booking['booking_type']; ?></td>
                        <td>
                            <?php echo date('M d, Y', strtotime($booking['start_date'])); ?>
                            <?php if(!empty($booking['end_date']) && $booking['end_date'] !== '0000-00-00'): ?>
                                &rarr; <?php echo date('M d, Y', strtotime($booking['end_date'])); ?>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: right;">₹<?php echo number_format($booking['total_price'], 2); ?></td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="3">Total Paid</td>
                        <td style="text-align: right;">₹<?php echo number_format($booking['total_price'], 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="invoice-footer">
            <p>Thank you for choosing Travel Agency! We hope you have a wonderful trip.</p>
            <p style="margin-top: 0.5rem;"><a href="mailto:ksetcse@gmail.com" style="color: #64748b; text-decoration: none;">ksetcse@gmail.com</a> | <a href="tel:9510243015" style="color: #64748b; text-decoration: none;">9510243015</a></p>
        </div>
    </div>
</body>
</html>

