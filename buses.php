<?php
require_once 'config/database.php';
include 'includes/header.php';

$from = isset($_GET['from']) ? sanitize($_GET['from']) : '';
$to = isset($_GET['to']) ? sanitize($_GET['to']) : '';
$date = isset($_GET['date']) ? sanitize($_GET['date']) : '';

$whereClause = 'WHERE departure_date >= CURDATE()';
$params = [];

if ($from && $to && $date) {
    $whereClause .= " AND from_location LIKE ? AND to_location LIKE ? AND departure_date = ?";
    array_push($params, "%$from%", "%$to%", $date);
}

$locations = $pdo->query("SELECT DISTINCT from_location as loc FROM buses UNION SELECT DISTINCT to_location FROM buses ORDER BY loc")->fetchAll(PDO::FETCH_COLUMN);

$buses = $pdo->prepare("SELECT * FROM buses $whereClause ORDER BY departure_date ASC, departure_time ASC");
$buses->execute($params);
$buses = $buses->fetchAll();
?>

<div style="background: linear-gradient(to right, #f59e0b, #d97706); margin: -2rem -2rem 2rem; padding: 4rem 2rem; text-align: center; color: white;">
    <h1 style="font-size: 2.5rem; margin-bottom: 1rem;" data-lang="book_bus">Book Bus Tickets</h1>
    <p style="font-size: 1.1rem; max-width: 600px; margin: 0 auto;">Comfortable, safe, and on-time intercity travel experiences.</p>
</div>

<div class="filters-section" style="max-width: 800px; margin: 0 auto 3rem;">
    <form method="GET" action="" class="grid-2" style="grid-template-columns: 1fr 1fr 1fr auto; align-items: end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label>From</label>
            <select name="from" class="form-control" required>
                <option value="">Select Origin</option>
                <?php foreach($locations as $loc): ?>
                    <option value="<?php echo htmlspecialchars($loc); ?>" <?php echo $from==$loc?'selected':''; ?>><?php echo htmlspecialchars($loc); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label>To</label>
            <select name="to" class="form-control" required>
                <option value="">Select Destination</option>
                <?php foreach($locations as $loc): ?>
                    <option value="<?php echo htmlspecialchars($loc); ?>" <?php echo $to==$loc?'selected':''; ?>><?php echo htmlspecialchars($loc); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label>Date</label>
            <input type="date" name="date" class="form-control" value="<?php echo $date; ?>" min="<?php echo date('Y-m-d'); ?>" required>
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <button type="submit" class="btn-primary" style="height: 42px; width: 100%;">Search Buses</button>
        </div>
    </form>
    <?php if($from || $to || $date): ?>
        <div style="text-align: right; margin-top: 1rem;">
            <a href="buses.html" style="color: var(--primary-color);">Clear Search</a>
        </div>
    <?php endif; ?>
</div>

<div style="max-width: 900px; margin: 0 auto;">
    <?php foreach($buses as $bus): ?>
    <div style="background: white; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid var(--border-color);">
        
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1rem;">
            <div style="flex: 2; min-width: 250px;">
                <div style="font-weight: 700; color: #1e293b; font-size: 1.1rem; margin-bottom: 0.25rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 5px; color: var(--primary-color);"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-1.1 0-2 .9-2 2v7h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                    <?php echo htmlspecialchars($bus['bus_name']); ?>
                </div>
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                    <h3 style="margin: 0; color: #1e293b;"><?php echo date('h:i A', strtotime($bus['departure_time'])); ?></h3>
                    <span style="color: #94a3b8; font-size: 0.875rem;"><?php echo date('M d, Y', strtotime($bus['departure_date'])); ?></span>
                </div>
                <div style="display: flex; align-items: center; gap: 1rem; color: #475569; font-weight: 500;">
                    <span><?php echo htmlspecialchars($bus['from_location']); ?></span>
                    <span style="color: var(--primary-color);">➔</span>
                    <span><?php echo htmlspecialchars($bus['to_location']); ?></span>
                </div>
            </div>
            
            <div style="flex: 1; text-align: center; min-width: 130px; border-left: 1px solid var(--border-color); border-right: 1px solid var(--border-color); padding: 0 1rem;">
                <div style="color: #64748b; font-size: 0.8rem; margin-bottom: 0.25rem;">Available Seats</div>
                <div style="color: <?php echo $bus['available_seats']<5 ? 'var(--danger-color)' : '#059669'; ?>; font-weight: 700; font-size: 1.25rem;">
                    <?php echo $bus['available_seats']; ?> / <?php echo $bus['total_seats']; ?>
                </div>
                <?php if($bus['available_seats'] < 5 && $bus['available_seats'] > 0): ?>
                <div style="color: var(--danger-color); font-size: 0.75rem; font-weight: 600; margin-top: 0.25rem;">⚡ Filling Fast!</div>
                <?php endif; ?>
            </div>
            
            <div style="flex: 1; text-align: right; min-width: 150px;">
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary-dark);">₹<?php echo number_format($bus['price'], 2); ?></div>
                <div style="color: #64748b; font-size: 0.8rem;">per seat</div>
            </div>
        </div>

        <!-- Seat Map -->
        <div style="background: #f8fafc; border-radius: 8px; padding: 1rem; border: 1px solid var(--border-color);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <span style="font-weight: 600; font-size: 0.875rem; color: #475569;">Select Your Seats</span>
                <div style="display: flex; gap: 1rem; font-size: 0.75rem; color: #64748b;">
                    <span> Available</span> <span> Booked</span> <span> Selected</span>
                </div>
            </div>
            <div id="seatmap-<?php echo $bus['id']; ?>" style="display: grid; grid-template-columns: repeat(10, 1fr); gap: 4px; max-width: 350px;">
                <?php 
                $booked = $bus['total_seats'] - $bus['available_seats'];
                for($s = 1; $s <= $bus['total_seats']; $s++): 
                    $is_booked = $s <= $booked;
                ?>
                <div class="seat-cell <?php echo $is_booked ? 'seat-booked' : 'seat-avail'; ?>"
                     data-bus="<?php echo $bus['id']; ?>" data-seat="<?php echo $s; ?>"
                     <?php if(!$is_booked): ?>onclick="toggleSeat(this, <?php echo $bus['id']; ?>)"<?php endif; ?>
                     style="width: 28px; height: 28px; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 0.6rem; font-weight: 600; cursor: <?php echo $is_booked?'not-allowed':'pointer'; ?>;
                     background: <?php echo $is_booked ? '#fee2e2' : '#d1fae5'; ?>; color: <?php echo $is_booked ? '#991b1b' : '#065f46'; ?>; border: 1px solid <?php echo $is_booked ? '#fca5a5' : '#6ee7b7'; ?>;">
                    <?php echo $s; ?>
                </div>
                <?php endfor; ?>
            </div>
                Selected: <strong id="selected-count-<?php echo $bus['id']; ?>">0</strong> seat(s)
                — Total: <strong id="selected-total-<?php echo $bus['id']; ?>">₹0.00</strong>
        </div>

        <?php if($bus['available_seats'] > 0): ?>
        <form action="payment.html" method="POST" style="margin-top: 1rem; text-align: right;">
            <input type="hidden" name="booking_type" value="bus">
            <input type="hidden" name="item_id" value="<?php echo $bus['id']; ?>">
            <input type="hidden" name="price" value="<?php echo $bus['price']; ?>">
            <input type="hidden" name="start_date" value="<?php echo $bus['departure_date']; ?>">
            <input type="hidden" name="persons" id="persons-<?php echo $bus['id']; ?>" value="1">
            
            <?php if(isLoggedIn()): ?>
                <button type="submit" class="btn-primary" style="padding: 0.75rem 2rem;">Book Selected Seats</button>
            <?php else: ?>
                <a href="login.html" class="btn-secondary" style="padding: 0.75rem 2rem;">Login to Book</a>
            <?php endif; ?>
        </form>
        <?php else: ?>
            <div style="margin-top: 1rem; text-align: center;"><button class="btn-secondary" disabled style="background: #94a3b8; cursor: not-allowed;">Sold Out</button></div>
        <?php endif; ?>
        
    </div>
    <?php endforeach; ?>
    
    <?php if(empty($buses)): ?>
    <div style="text-align: center; padding: 4rem 2rem; background: var(--card-bg); border-radius: 8px;">
        <h3>No buses found</h3>
        <p style="color: #64748b; margin-top: 0.5rem;">Please check for another date or route.</p>
    </div>
    <?php endif; ?>
</div>

<script>
const busSeats = {};
function toggleSeat(el, busId) {
    if (!busSeats[busId]) busSeats[busId] = {count: 0, price: 0};
    const pricePerSeat = parseFloat(el.closest('div[style*="background: white"]').querySelector('input[name="price"]').value);
    
    if (el.classList.contains('seat-selected')) {
        el.classList.remove('seat-selected');
        el.style.background = '#d1fae5'; el.style.color = '#065f46'; el.style.borderColor = '#6ee7b7';
        busSeats[busId].count--;
    } else {
        el.classList.add('seat-selected');
        el.style.background = '#bfdbfe'; el.style.color = '#1e40af'; el.style.borderColor = '#60a5fa';
        busSeats[busId].count++;
    }
    const cnt = busSeats[busId].count;
    document.getElementById('selected-count-' + busId).textContent = cnt;
    document.getElementById('selected-total-' + busId).textContent = '₹' + (cnt * pricePerSeat).toFixed(2);
    document.getElementById('persons-' + busId).value = Math.max(1, cnt);
}
</script>

<?php include 'includes/footer.php'; ?>

