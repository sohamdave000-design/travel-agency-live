<?php
require_once '../config/database.php';

if (!isAdmin()) redirect('login.html');

$msg = '';

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = (int)$_GET['id'];
    
    if ($action == 'confirm') {
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ?");
        $stmt->execute([$id]);
        $msg = "Booking #$id confirmed successfully.";
    } elseif ($action == 'cancel') {
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
        $stmt->execute([$id]);
        $msg = "Booking #$id cancelled.";
    }
}

$bookings = $pdo->query("
    SELECT b.*, u.name as user_name, u.email as user_email 
    FROM bookings b 
    JOIN users u ON b.user_id = u.id 
    ORDER BY b.booking_date DESC
")->fetchAll();

include 'includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h1>Manage Bookings</h1>
</div>

<?php if($msg): ?>
<div class="alert alert-success"><?php echo $msg; ?></div>
<?php endif; ?>

<div class="admin-table">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Payment</th>
                    <th>Type</th>
                    <th>Travel Dates</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($bookings as $booking): 
                    $p = $pdo->prepare("SELECT * FROM payments WHERE booking_id = ?");
                    $p->execute([$booking['id']]);
                    $pay = $p->fetch();
                ?>
                <tr>
                    <td>#<?php echo $booking['id']; ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($booking['user_name']); ?></strong><br>
                        <small style="color: #64748b;"><?php echo htmlspecialchars($booking['user_email']); ?></small>
                    </td>
                    <td>
                        <?php if($pay): ?>
                            <span style="font-size: 0.85rem; display: block;">
                                <?php 
                                    echo ucfirst(str_replace('_',' ',$pay['payment_method'])); 
                                    if($pay['card_last4']) echo " (****".$pay['card_last4'].")";
                                ?>
                            </span>
                            <small style="font-family: monospace; color: #64748b; font-size: 0.75rem;"><?php echo $pay['transaction_id']; ?></small>
                        <?php else: ?>
                            <span style="color: #94a3b8; font-size: 0.85rem;">No payment record</span>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge" style="background: #e2e8f0; color: #475569; text-transform: capitalize;"><?php echo $booking['booking_type']; ?></span></td>
                    <td>
                        <?php echo date('M d, Y', strtotime($booking['start_date'])); ?>
                        <?php if($booking['end_date']): ?>
                            <br>to <?php echo date('M d, Y', strtotime($booking['end_date'])); ?>
                        <?php endif; ?>
                    </td>
                    <td>₹<?php echo number_format($booking['total_price'], 2); ?></td>
                    <td>
                        <span class="badge badge-<?php echo $booking['status']; ?>">
                            <?php echo ucfirst($booking['status']); ?>
                        </span>
                    </td>
                    <td>
                        <?php if($booking['status'] == 'pending'): ?>
                        <a href="bookings.html?action=confirm&id=<?php echo $booking['id']; ?>" class="admin-action-btn btn-edit" style="color: #059669; background: #d1fae5;">Confirm</a>
                        <a href="bookings.html?action=cancel&id=<?php echo $booking['id']; ?>" class="admin-action-btn btn-delete">Cancel</a>
                        <?php else: ?>
                            <span style="color: #94a3b8; font-size: 0.875rem;">Action Taken</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($bookings)): ?>
                <tr><td colspan="7" class="text-center">No bookings found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

