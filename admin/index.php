<?php
require_once '../config/database.php';
include 'includes/header.php';

// Fetch statistics
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_bookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$total_revenue = $pdo->query("SELECT COALESCE(SUM(total_price),0) FROM bookings WHERE status = 'confirmed'")->fetchColumn();
$pending_custom = 0; // custom_packages table removed
$total_packages = $pdo->query("SELECT COUNT(*) FROM packages")->fetchColumn();
$total_hotels = $pdo->query("SELECT COUNT(*) FROM hotels")->fetchColumn();
$total_buses = $pdo->query("SELECT COUNT(*) FROM buses")->fetchColumn();
$total_vehicles = $pdo->query("SELECT COUNT(*) FROM rentals")->fetchColumn();
$cancelled_bookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='cancelled'")->fetchColumn();
$confirmed_bookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='confirmed'")->fetchColumn();

// Monthly revenue for chart (last 6 months)
$monthly_revenue = $pdo->query("
    SELECT DATE_FORMAT(booking_date, '%Y-%m') as month, SUM(total_price) as revenue
    FROM bookings WHERE status = 'confirmed'
    GROUP BY month ORDER BY month DESC LIMIT 6
")->fetchAll();
$monthly_revenue = array_reverse($monthly_revenue);

// Bookings by type for pie chart
$bookings_by_type = $pdo->query("
    SELECT booking_type, COUNT(*) as cnt FROM bookings GROUP BY booking_type
")->fetchAll();

// Recent bookings
$recent_bookings = $pdo->query("
    SELECT b.id, u.name as user_name, b.booking_type, b.total_price, b.status, b.booking_date 
    FROM bookings b JOIN users u ON b.user_id = u.id 
    ORDER BY b.booking_date DESC LIMIT 5
")->fetchAll();

// Recent reviews
$recent_reviews = $pdo->query("
    SELECT r.*, u.name FROM reviews r 
    JOIN users u ON r.user_id = u.id 
    ORDER BY r.created_at DESC LIMIT 5
")->fetchAll();

$avg_rating = $pdo->query("SELECT COALESCE(AVG(rating),0) FROM reviews")->fetchColumn();
$total_reviews = $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
?>

<h1 style="margin-bottom: 1.5rem;">&#128202; Dashboard Overview</h1>

<!-- Top Stats Row -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
    <div class="stat-card" style="border-left: 4px solid #2563eb;">
        <div class="stat-card-title">Total Revenue</div>
        <div class="stat-card-value" style="color: #2563eb;">&#8377;<?php echo number_format($total_revenue, 2); ?></div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #059669;">
        <div class="stat-card-title">Confirmed</div>
        <div class="stat-card-value" style="color: #059669;"><?php echo $confirmed_bookings; ?></div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #f59e0b;">
        <div class="stat-card-title">Users</div>
        <div class="stat-card-value" style="color: #f59e0b;"><?php echo $total_users; ?></div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #ef4444;">
        <div class="stat-card-title">Pending Requests</div>
        <div class="stat-card-value" style="color: #ef4444;"><?php echo $pending_custom; ?></div>
    </div>
</div>

<!-- Secondary Stats -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
    <div class="stat-card"><div class="stat-card-title">Packages</div><div class="stat-card-value"><?php echo $total_packages; ?></div></div>
    <div class="stat-card"><div class="stat-card-title">Hotels</div><div class="stat-card-value"><?php echo $total_hotels; ?></div></div>
    <div class="stat-card"><div class="stat-card-title">Buses</div><div class="stat-card-value"><?php echo $total_buses; ?></div></div>
    <div class="stat-card"><div class="stat-card-title">Vehicles</div><div class="stat-card-value"><?php echo $total_vehicles; ?></div></div>
    <div class="stat-card"><div class="stat-card-title">Total Bookings</div><div class="stat-card-value"><?php echo $total_bookings; ?></div></div>
    <div class="stat-card"><div class="stat-card-title">Cancelled</div><div class="stat-card-value" style="color: #ef4444;"><?php echo $cancelled_bookings; ?></div></div>
</div>

<!-- Charts Row -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <div class="admin-table" style="padding: 1.5rem;">
        <h3 style="margin-bottom: 1rem;">&#128200; Monthly Revenue</h3>
        <canvas id="revenueChart" height="200"></canvas>
    </div>
    <div class="admin-table" style="padding: 1.5rem;">
        <h3 style="margin-bottom: 1rem;">&#127849; Bookings by Type</h3>
        <canvas id="typeChart" height="200"></canvas>
    </div>
</div>

<!-- Customer Feedback -->
<div class="admin-table" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h3>&#11088; Customer Feedback</h3>
        <div style="background: #fef3c7; padding: 0.5rem 1rem; border-radius: 8px;">
            <span style="color: #f59e0b; font-size: 1.5rem; font-weight: 700;"><?php echo number_format($avg_rating, 1); ?></span>
            <span style="color: #92400e; font-size: 0.875rem;"> / 5 (<?php echo $total_reviews; ?> reviews)</span>
        </div>
    </div>
    <?php foreach($recent_reviews as $rev): ?>
    <div style="padding: 1rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: start;">
        <div>
            <strong><?php echo htmlspecialchars($rev['name']); ?></strong>
            <span style="color: #f59e0b; margin-left: 0.5rem;"><?php echo str_repeat('&#9733;', $rev['rating']) . str_repeat('&#9734;', 5-$rev['rating']); ?></span>
            <p style="color: #64748b; margin-top: 0.25rem; font-size: 0.9rem;"><?php echo htmlspecialchars($rev['comment']); ?></p>
        </div>
        <span style="color: #94a3b8; font-size: 0.8rem; white-space: nowrap;"><?php echo date('M d', strtotime($rev['created_at'])); ?></span>
    </div>
    <?php endforeach; ?>
    <?php if(empty($recent_reviews)): ?>
    <p style="color: #64748b; padding: 1rem;">No reviews yet.</p>
    <?php endif; ?>
</div>

<!-- Recent Bookings Table -->
<div class="admin-table">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h3>&#128203; Recent Bookings</h3>
        <a href="bookings.html" class="btn-secondary" style="font-size: 0.875rem; padding: 0.4rem 1rem;">View All</a>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr><th>ID</th><th>Customer</th><th>Type</th><th>Amount</th><th>Date</th><th>Status</th></tr>
            </thead>
            <tbody>
                <?php foreach($recent_bookings as $booking): ?>
                <tr>
                    <td>#<?php echo $booking['id']; ?></td>
                    <td><?php echo htmlspecialchars($booking['user_name']); ?></td>
                    <td><span class="badge" style="background: #e2e8f0; color: #475569;"><?php echo ucfirst($booking['booking_type']); ?></span></td>
                    <td>&#8377;<?php echo number_format($booking['total_price'], 2); ?></td>
                    <td><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></td>
                    <td><span class="badge badge-<?php echo $booking['status']; ?>"><?php echo ucfirst($booking['status']); ?></span></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($recent_bookings)): ?>
                <tr><td colspan="6" class="text-center">No bookings yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
var revenueLabels = [<?php echo implode(',', array_map(fn($m) => '"'.date('M Y', strtotime($m['month'].'-01')).'"', $monthly_revenue)); ?>];
var revenueData   = [<?php echo implode(',', array_map(fn($m) => $m['revenue'], $monthly_revenue)); ?>];
var typeLabels    = [<?php echo implode(',', array_map(fn($b) => '"'.ucfirst($b['booking_type']).'"', $bookings_by_type)); ?>];
var typeData      = [<?php echo implode(',', array_map(fn($b) => $b['cnt'], $bookings_by_type)); ?>];

// Revenue Chart
new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: revenueLabels,
        datasets: [{
            label: 'Revenue',
            data: revenueData,
            backgroundColor: 'rgba(37, 99, 235, 0.7)',
            borderRadius: 6,
            borderColor: '#2563eb',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { callback: function(v){ return '\u20B9' + v; } } } }
    }
});

// Booking Type Chart
new Chart(document.getElementById('typeChart'), {
    type: 'doughnut',
    data: {
        labels: typeLabels,
        datasets: [{
            data: typeData,
            backgroundColor: ['#2563eb', '#059669', '#f59e0b', '#8b5cf6', '#ef4444'],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12 } } }
    }
});
</script>

<?php include 'includes/footer.php'; ?>
