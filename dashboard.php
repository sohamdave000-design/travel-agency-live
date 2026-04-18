<?php
require_once 'config/database.php';
include 'includes/header.php';

if (!isLoggedIn()) {
    redirect('login.html');
}

$user_id = $_SESSION['user_id'];
$msg = '';

if (isset($_GET['cancel_id'])) {
    $cancel_id = (int)$_GET['cancel_id'];
    $check = $pdo->prepare("SELECT id, booking_type, item_id, status FROM bookings WHERE id = ? AND user_id = ? AND status IN ('pending','confirmed')");
    $check->execute([$cancel_id, $user_id]);
    $bk = $check->fetch();
    if ($bk) {
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
        $stmt->execute([$cancel_id]);
        // Restore bus seats if bus booking
        if ($bk['booking_type'] == 'bus') {
            $pdo->prepare("UPDATE buses SET available_seats = available_seats + 1 WHERE id = ?")->execute([$bk['item_id']]);
        }
        $msg = "Booking #$cancel_id cancelled successfully.";
    }
}

// Fetch user bookings with item names
$bookings = $pdo->prepare("SELECT * FROM bookings WHERE user_id = ? ORDER BY booking_date DESC");
$bookings->execute([$user_id]);
$bookings = $bookings->fetchAll();

// Fetch wishlist
$wishlist = $pdo->prepare("SELECT w.*, p.name, p.destination, p.price, p.duration, p.image FROM wishlist w JOIN packages p ON w.package_id = p.id WHERE w.user_id = ? ORDER BY w.created_at DESC");
$wishlist->execute([$user_id]);
$wishlist = $wishlist->fetchAll();

// Fetch AI Trips
$ai_trips = $pdo->prepare("SELECT * FROM ai_plans WHERE user_id = ? ORDER BY created_at DESC");
$ai_trips->execute([$user_id]);
$ai_trips = $ai_trips->fetchAll();
?>

<div style="max-width: 1100px; margin: 0 auto;">
    <div style="background: linear-gradient(135deg, #2563eb, #7c3aed); padding: 2rem; border-radius: 12px; color: white; margin-bottom: 2rem; display: flex; align-items: center; gap: 1.5rem;">
        <div style="width: 80px; height: 80px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: bold; flex-shrink: 0;">
            <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
        </div>
        <div>
            <h1 style="margin-bottom: 0.25rem;">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
            <p style="opacity: 0.8;">Manage your bookings, wishlist, and custom requests all in one place.</p>
        </div>
    </div>

    <?php if($msg): ?>
        <div class="alert alert-success"><?php echo $msg; ?></div>
    <?php endif; ?>

    <!-- Quick Stats -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div style="background: white; padding: 1.25rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); text-align: center;">
            <div style="font-size: 2rem; font-weight: 700; color: #2563eb;"><?php echo count($bookings); ?></div>
            <div style="color: #64748b; font-size: 0.875rem;">Total Bookings</div>
        </div>
        <div style="background: white; padding: 1.25rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); text-align: center;">
            <div style="font-size: 2rem; font-weight: 700; color: #059669;"><?php echo count(array_filter($bookings, fn($b) => $b['status']=='confirmed')); ?></div>
            <div style="color: #64748b; font-size: 0.875rem;">Confirmed</div>
        </div>
        <div style="background: white; padding: 1.25rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); text-align: center;">
            <div style="font-size: 2rem; font-weight: 700; color: #f59e0b;"><?php echo count($wishlist); ?></div>
            <div style="color: #64748b; font-size: 0.875rem;">Wishlisted</div>
        </div>
    </div>

    <!-- Bookings -->
    <h2 style="margin-bottom: 1rem; color: var(--primary-dark);">Your Bookings</h2>
    <div class="table-responsive" style="margin-bottom: 3rem;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Dates</th>
                    <th>Total</th>
                    <th>Booked On</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($bookings as $booking): ?>
                <tr style="background: white;">
                    <td>#<?php echo $booking['id']; ?></td>
                    <td><span class="badge" style="background: #e2e8f0; color: #475569; text-transform: capitalize;"><?php echo $booking['booking_type']; ?></span></td>
                    <td>
                        <?php echo date('M d, Y', strtotime($booking['start_date'])); ?>
                        <?php if($booking['end_date']): ?>
                            <br><small style="color: #64748b;">to</small> <?php echo date('M d, Y', strtotime($booking['end_date'])); ?>
                        <?php endif; ?>
                    </td>
                    <td style="font-weight: 600;">₹<?php echo number_format($booking['total_price'], 2); ?></td>
                    <td><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></td>
                    <td>
                        <span class="badge badge-<?php echo $booking['status']; ?>"><?php echo ucfirst($booking['status']); ?></span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            <?php if($booking['status'] == 'confirmed'): ?>
                                <a href="invoice.html?id=<?php echo $booking['id']; ?>" class="btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" target="_blank">📄 Invoice</a>
                            <?php endif; ?>
                            <?php if(in_array($booking['status'], ['pending', 'confirmed'])): ?>
                                <a href="dashboard.html?cancel_id=<?php echo $booking['id']; ?>" class="btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" onclick="return confirm('Cancel this booking?');">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($bookings)): ?>
                <tr>
                    <td colspan="7" class="text-center" style="padding: 3rem; background: white;">
                        <span style="font-size: 3rem; display: block; margin-bottom: 1rem;">✈️</span>
                        <h3 style="color: #475569; margin-bottom: 0.5rem;">No bookings yet</h3>
                        <p style="color: #94a3b8; margin-bottom: 1.5rem;">Time to plan your next adventure!</p>
                        <a href="packages.html" class="btn-primary">Explore Packages</a>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Wishlist -->
    <h2 style="margin-bottom: 1rem; color: var(--primary-dark);">❤️ Your Wishlist</h2>
    <?php if(!empty($wishlist)): ?>
    <div class="card-grid" style="margin-bottom: 3rem;">
        <?php foreach($wishlist as $w): ?>
        <div class="card">
            <img src="<?php echo htmlspecialchars($w['image']); ?>" alt="<?php echo htmlspecialchars($w['name']); ?>" class="card-img">
            <div class="card-body">
                <h3 class="card-title"><?php echo htmlspecialchars($w['name']); ?></h3>
                <p style="color: #64748b; font-size: 0.9rem;">📍 <?php echo htmlspecialchars($w['destination']); ?> | ⏱️ <?php echo $w['duration']; ?> Days</p>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: auto;">
                    <div class="card-price">₹<?php echo number_format($w['price'], 2); ?></div>
                    <a href="package_details.html?id=<?php echo $w['package_id']; ?>" class="btn-primary" style="font-size: 0.875rem;">View Details</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div style="text-align: center; padding: 2rem; background: white; border-radius: 8px; margin-bottom: 3rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <p style="color: #64748b;">No saved packages. Browse and tap the ❤️ to save!</p>
    </div>
    <?php endif; ?>

    <!-- AI Trips -->
    <h2 style="margin-bottom: 1rem; color: var(--primary-dark); display: flex; align-items: center; gap: 0.5rem; margin-top: 2rem;">
        <span>✨ Your AI Planned Trips</span>
        <a href="ai_planner.html" class="btn-primary" style="font-size: 0.75rem; padding: 0.25rem 0.75rem;">Plan New</a>
    </h2>
    <?php if(!empty($ai_trips)): ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
        <?php foreach($ai_trips as $trip): ?>
        <div style="background: white; border-radius: 12px; padding: 1.5rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                <div>
                    <h3 style="color: #1e293b; margin-bottom: 0.25rem;"><?php echo htmlspecialchars($trip['destination']); ?></h3>
                    <span class="badge" style="background: #f1f5f9; color: #475569; font-size: 0.7rem;"><?php echo $trip['duration']; ?> Days • <?php echo ucfirst($trip['vibe']); ?></span>
                </div>
                <div style="font-size: 0.75rem; color: #94a3b8;"><?php echo date('M d', strtotime($trip['created_at'])); ?></div>
            </div>
            <p style="font-size: 0.9rem; color: #64748b; margin-bottom: 1.5rem; line-height: 1.5;">
                <?php 
                    $trip_data = json_decode($trip['itinerary_data'], true);
                    echo "Highlights: " . ($trip_data[0]['activities'][0]['activity'] ?? 'Sightseeing');
                ?>...
            </p>
            <a href="ai_planner.html" class="btn-secondary" style="width: 100%; text-align: center; font-size: 0.875rem;">View Full Plan</a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div style="text-align: center; padding: 3rem; background: #f8fafc; border: 2px dashed #e2e8f0; border-radius: 12px; margin-bottom: 3rem;">
        <div style="font-size: 2.5rem; margin-bottom: 1rem;">🤖</div>
        <h3 style="color: #475569; margin-bottom: 0.5rem;">No AI Trips yet</h3>
        <p style="color: #94a3b8; margin-bottom: 1.5rem;">Let our AI craft the perfect itinerary for you in seconds!</p>
        <a href="ai_planner.html" class="btn-primary">Try AI Planner</a>
    </div>
    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>
