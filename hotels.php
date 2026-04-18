<?php
require_once 'config/database.php';
include 'includes/header.php';

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$whereClause = '';
$params = [];

if ($search) {
    $whereClause = "WHERE location LIKE ? OR name LIKE ?";
    $params = ["%$search%", "%$search%"];
}

$hotels = $pdo->prepare("SELECT * FROM hotels $whereClause ORDER BY rating DESC");
$hotels->execute($params);
$hotels = $hotels->fetchAll();
?>

<div style="background: linear-gradient(to right, #0ea5e9, #3b82f6); margin: -2rem -2rem 2rem; padding: 4rem 2rem; text-align: center; color: white;">
    <h1 style="font-size: 2.5rem; margin-bottom: 1rem;" data-lang="find_stay">Find the Perfect Stay</h1>
    <p style="font-size: 1.1rem; max-width: 600px; margin: 0 auto;">Book luxury hotels and comfortable stays at the best prices.</p>
</div>

<div class="filters-section">
    <form method="GET" action="" class="filters-grid">
        <div class="form-group" style="margin-bottom: 0;">
            <label>Search Location</label>
            <input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($search); ?>" placeholder="e.g., Mumbai, Goa...">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <button type="submit" class="btn-primary" style="height: 42px;">Search Hotels</button>
            <?php if($search): ?>
                <a href="hotels.html" class="btn-secondary" style="height: 42px; line-height: 26px; margin-left: 0.5rem;">Clear</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card-grid">
    <?php foreach($hotels as $hotel): ?>
    <?php
        $room_types_json = $hotel['room_types'] ?? '{"standard":1,"deluxe":1.5,"suite":2.5}';
        $room_types = json_decode($room_types_json, true);
        if (!$room_types) $room_types = ['standard'=>1,'deluxe'=>1.5,'suite'=>2.5];
    ?>
    <div class="card">
        <img src="<?php echo htmlspecialchars($hotel['image']); ?>" alt="<?php echo htmlspecialchars($hotel['name']); ?>" class="card-img" onerror="this.src='https://images.unsplash.com/photo-1542314831-c6a4d14d2328?q=80&w=800'; this.onerror=null;">
        <div class="card-body">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                <h3 class="card-title" style="margin: 0;"><?php echo htmlspecialchars($hotel['name']); ?></h3>
                <span style="background: #fef08a; color: #854d0e; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: bold; font-size: 0.875rem;">★ <?php echo $hotel['rating']; ?></span>
            </div>
            <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 0.5rem;">📍 <?php echo htmlspecialchars($hotel['location']); ?></p>
            <?php if(!empty($hotel['amenities'])): ?>
            <p style="color: #059669; font-size: 0.8rem; margin-bottom: 0.75rem; font-weight: 500;">✓ <?php echo htmlspecialchars($hotel['amenities']); ?></p>
            <?php endif; ?>
            <p class="card-text" style="font-size: 0.9rem;"><?php echo substr(htmlspecialchars($hotel['description']), 0, 80) . '...'; ?></p>
            
            <?php if($hotel['latitude'] && $hotel['longitude']): ?>
            <div style="margin-bottom: 0.75rem; border-radius: 6px; overflow: hidden; height: 100px;">
                <iframe src="https://maps.google.com/maps?q=<?php echo $hotel['latitude']; ?>,<?php echo $hotel['longitude']; ?>&z=13&output=embed" 
                        width="100%" height="100" style="border:0;" loading="lazy"></iframe>
            </div>
            <?php endif; ?>
            
            <div style="margin-top: auto;">
                <div style="display: flex; align-items: baseline; gap: 0.5rem; margin-bottom: 0.75rem;">
                    <span class="card-price" style="margin: 0;" id="hotel-price-<?php echo $hotel['id']; ?>">₹<?php echo number_format($hotel['price_per_night'], 2); ?></span>
                    <span style="font-size: 0.875rem; color: #64748b;">/ night</span>
                </div>
                
                <form action="payment.html" method="POST" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <input type="hidden" name="booking_type" value="hotel">
                    <input type="hidden" name="item_id" value="<?php echo $hotel['id']; ?>">
                    <input type="hidden" name="price" id="hotel-real-price-<?php echo $hotel['id']; ?>" value="<?php echo $hotel['price_per_night']; ?>">
                    
                    <div style="width: 100%;">
                        <select name="room_type" class="form-control" style="font-size: 0.875rem;" onchange="updateRoomPrice(<?php echo $hotel['id']; ?>, <?php echo $hotel['price_per_night']; ?>, this.value)">
                            <?php foreach($room_types as $rtype => $mult): ?>
                            <option value="<?php echo $mult; ?>">
                                <?php echo ucfirst($rtype); ?> Room — ₹<?php echo number_format($hotel['price_per_night'] * $mult, 2); ?>/night
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div style="flex: 1; min-width: 110px;">
                        <input type="date" name="start_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>" title="Check-in" style="font-size: 0.875rem;">
                    </div>
                    <div style="flex: 1; min-width: 110px;">
                        <input type="date" name="end_date" class="form-control" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" title="Check-out" style="font-size: 0.875rem;">
                    </div>
                    <?php if(isLoggedIn()): ?>
                        <button type="submit" class="btn-primary" style="width: 100%; font-size: 0.9rem;">Book Room</button>
                    <?php else: ?>
                        <a href="login.html" class="btn-secondary" style="width: 100%; text-align: center; font-size: 0.9rem;">Login to Book</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    
    <?php if(empty($hotels)): ?>
    <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem; background: var(--card-bg); border-radius: 8px;">
        <h3>No hotels found</h3>
        <p style="color: #64748b; margin-top: 0.5rem;">Try adjusting your search criteria.</p>
    </div>
    <?php endif; ?>
</div>

<script>
function updateRoomPrice(hotelId, basePrice, multiplier) {
    const newPrice = (basePrice * parseFloat(multiplier)).toFixed(2);
    document.getElementById('hotel-price-' + hotelId).textContent = '₹' + newPrice;
    document.getElementById('hotel-real-price-' + hotelId).value = newPrice;
}
</script>

<?php include 'includes/footer.php'; ?>
