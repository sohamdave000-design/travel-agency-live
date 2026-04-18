<?php
require_once 'config/database.php';
include 'includes/header.php';

// Get distinct cities
$cities = $pdo->query("SELECT DISTINCT city FROM rentals WHERE available = 1 ORDER BY city ASC")->fetchAll(PDO::FETCH_COLUMN);

// Selected city / type filter
$selected_city = isset($_GET['city']) ? sanitize($_GET['city']) : '';
$selected_type = isset($_GET['type']) ? sanitize($_GET['type']) : '';

// Fetch vehicles
$query = "SELECT * FROM rentals WHERE 1=1";
$params = [];
if ($selected_city) { $query .= " AND city = ?"; $params[] = $selected_city; }
if ($selected_type) { $query .= " AND type = ?"; $params[] = $selected_type; }
$query .= " ORDER BY available DESC, city ASC, type ASC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$vehicles = $stmt->fetchAll();

$rentalsData = [
    [
        'id' => 1,
        'type' => 'bike',
        'name' => 'Honda Activa (Scooter)',
        'city' => 'Multiple',
        'seating' => 2,
        'fuel' => 'Petrol',
        'price' => 500,
        'image' => 'https://images.unsplash.com/photo-1591637333184-19aa84b3e01f?auto=format&fit=crop&w=800&q=80',
        'description' => 'Perfect scooter for navigating narrow city streets.'
    ],
    [
        'id' => 2,
        'type' => 'bike',
        'name' => 'Royal Enfield Classic 350',
        'city' => 'Multiple',
        'seating' => 2,
        'fuel' => 'Petrol',
        'price' => 1200,
        'image' => 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=800&q=80',
        'description' => 'Ideal for long mountain runs.'
    ],
    [
        'id' => 3,
        'type' => 'car',
        'name' => 'Toyota Innova Hycross',
        'city' => 'Bangalore',
        'seating' => 7,
        'fuel' => 'Hybrid',
        'price' => 4500,
        'image' => 'https://imgd.aeplcdn.com/664x374/n/cw/ec/140591/innova-hycross-exterior-right-front-three-quarter-72.jpeg',
        'description' => 'Premium hybrid SUV with maximum comfort.'
    ],
    [
        'id' => 4,
        'type' => 'car',
        'name' => 'Mahindra Scorpio-N',
        'city' => 'Delhi',
        'seating' => 7,
        'fuel' => 'Diesel',
        'price' => 4000,
        'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4b/Mahindra_Scorpio-N_Z8L_Diesel_AT_4WD.jpg/1200px-Mahindra_Scorpio-N_Z8L_Diesel_AT_4WD.jpg',
        'description' => 'Powerful SUV for all-terrain adventures.'
    ],
    [
        'id' => 5,
        'type' => 'car',
        'name' => 'Kia Carens',
        'city' => 'Mumbai',
        'seating' => 7,
        'fuel' => 'Petrol',
        'price' => 3200,
        'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d4/Kia_Carens_%28KY%29_IMG_5245.jpg/1200px-Kia_Carens_%28KY%29_IMG_5245.jpg',
        'description' => 'Modern family car with spacious interiors.'
    ],
    [
        'id' => 6,
        'type' => 'car',
        'name' => 'Hyundai Grand i10 Nios',
        'city' => 'Pune',
        'seating' => 5,
        'fuel' => 'Petrol',
        'price' => 1500,
        'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a2/Hyundai_Grand_i10_Nios_Asta_Petrol.jpg/1200px-Hyundai_Grand_i10_Nios_Asta_Petrol.jpg',
        'description' => 'Efficient city hatchback.'
    ],
    [
        'id' => 7,
        'type' => 'car',
        'name' => 'Maruti Suzuki Swift',
        'city' => 'Chandigarh',
        'seating' => 5,
        'fuel' => 'Petrol',
        'price' => 1400,
        'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/ff/2018_Suzuki_Swift_SZ5_Boosterjet_1.0.jpg/1200px-2018_Suzuki_Swift_SZ5_Boosterjet_1.0.jpg',
        'description' => 'Reliable and sporty hatchback.'
    ],
    [
        'id' => 8,
        'type' => 'car',
        'name' => 'Honda City',
        'city' => 'Hyderabad',
        'seating' => 5,
        'fuel' => 'Petrol',
        'price' => 3000,
        'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/be/2020_Honda_City_V_1.5.jpg/1200px-2020_Honda_City_V_1.5.jpg',
        'description' => 'Luxury executive sedan.'
    ],
    [
        'id' => 9,
        'type' => 'car',
        'name' => 'Mahindra Thar (SUV)',
        'city' => 'Goa',
        'seating' => 4,
        'fuel' => 'Diesel',
        'price' => 3500,
        'image' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=800&q=80',
        'description' => 'Iconic 4x4 rugged beast.'
    ]
];
?>

<style>
/* ===== Rentals Page ===== */
.rentals-hero {
    background: linear-gradient(135deg, rgba(15,23,42,0.85) 0%, rgba(37,99,235,0.75) 100%),
                url('https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80') center/cover;
    margin: -2rem -2rem 0;
    padding: 5rem 2rem 4rem;
    text-align: center;
    color: white;
}
.rentals-hero h1 { font-size: 3rem; margin-bottom: 0.75rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.4); }
.rentals-hero p  { font-size: 1.15rem; opacity: 0.9; }

/* City search banner */
.city-picker {
    max-width: 700px;
    margin: -2.5rem auto 3rem;
    background: white;
    border-radius: 16px;
    box-shadow: 0 12px 40px rgba(37,99,235,0.15);
    padding: 1.5rem 2rem;
    display: flex;
    gap: 1rem;
    align-items: flex-end;
    flex-wrap: wrap;
}
.city-picker .form-group { flex: 1; min-width: 140px; margin: 0; }
.city-picker .form-group label { font-weight: 600; font-size: 0.85rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }
.city-picker .form-control { border: 2px solid #e2e8f0; border-radius: 10px; }
.city-picker .form-control:focus { border-color: #2563eb; }
.city-picker .btn-primary { padding: 0.75rem 1.75rem; border-radius: 10px; font-size: 1rem; white-space: nowrap; }

/* Layout */
.rentals-main-layout {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 2.5rem;
    align-items: start;
    margin-top: 1rem;
}

/* Vehicle cards */
.vehicle-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.75rem; }

.vehicle-card {
    background: white;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
    display: flex;
    flex-direction: column;
}
.vehicle-card:hover { transform: translateY(-6px); box-shadow: 0 12px 28px rgba(37,99,235,0.12); }
.vehicle-card.booked { opacity: 0.65; pointer-events: none; }

.vehicle-img-wrap { position: relative; }
.vehicle-img { width: 100%; height: 190px; object-fit: cover; }

.badge-city {
    position: absolute; bottom: 0.75rem; left: 0.75rem;
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(6px);
    padding: 0.25rem 0.75rem; border-radius: 9999px;
    font-size: 0.78rem; font-weight: 700; color: #2563eb;
}
.badge-status-avail  { position: absolute; top: 0.75rem; right: 0.75rem; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.78rem; font-weight: 700; }
.avail-yes { background: #10b981; color: white; }
.avail-no  { background: #ef4444; color: white; }

.badge-vtype {
    position: absolute; top: 0.75rem; left: 0.75rem;
    background: linear-gradient(135deg, #2563eb, #7c3aed);
    color: white; padding: 0.25rem 0.7rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700;
}

.vehicle-body { padding: 1.25rem; flex: 1; display: flex; flex-direction: column; }
.vehicle-name { font-size: 1.15rem; font-weight: 700; color: #0f172a; margin-bottom: 0.25rem; }
.vehicle-price { font-size: 1.25rem; font-weight: 800; color: #2563eb; margin-bottom: 1rem; }
.vehicle-price small { font-size: 0.85rem; color: #94a3b8; font-weight: 400; }

.spec-row {
    display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;
    margin-bottom: 1.25rem; flex: 1;
}
.spec-pill {
    background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;
    padding: 0.4rem 0.6rem; font-size: 0.82rem; color: #334155;
    display: flex; align-items: center; gap: 0.35rem;
}
.spec-pill span.icon { font-size: 1rem; }

.select-btn {
    width: 100%; padding: 0.65rem; background: linear-gradient(135deg, #2563eb, #4f46e5);
    color: white; border: none; border-radius: 10px; font-weight: 700; font-size: 0.95rem;
    cursor: pointer; transition: opacity 0.2s, transform 0.1s;
}
.select-btn:hover { opacity: 0.9; transform: scale(1.02); }

/* Sticky Booking Sidebar */
.booking-sidebar {
    position: sticky; top: 90px;
    background: white;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 8px 24px rgba(37,99,235,0.08);
    overflow: hidden;
}
.sidebar-header {
    background: linear-gradient(135deg, #2563eb, #4f46e5);
    color: white; padding: 1.5rem 2rem;
    text-align: center;
}
.sidebar-header h3 { font-size: 1.35rem; margin: 0; }
.sidebar-body { padding: 1.75rem; }

.selected-vehicle-banner {
    background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
    border: 2px solid #93c5fd;
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    text-align: center;
    min-height: 60px;
    display: flex; align-items: center; justify-content: center;
}
.selected-vehicle-banner .sel-name { font-weight: 700; color: #1e40af; font-size: 1rem; }
.selected-vehicle-banner .sel-price { color: #64748b; font-size: 0.85rem; }

.price-mode-toggle {
    display: flex; border-radius: 10px; overflow: hidden; border: 2px solid #2563eb; margin-bottom: 1.25rem;
}
.price-mode-toggle label { flex: 1; text-align: center; padding: 0.6rem; cursor: pointer; font-weight: 600; font-size: 0.9rem; color: #2563eb; background: white; transition: background 0.2s, color 0.2s; }
.price-mode-toggle input:checked + label { background: #2563eb; color: white; }
.price-mode-toggle input { display: none; }

.addon-section { margin-bottom: 1.25rem; }
.addon-section h4 { font-size: 0.85rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem; }
.addon-checks { display: flex; flex-direction: column; gap: 0.5rem; }
.addon-check-item { display: flex; align-items: center; gap: 0.6rem; background: #f8fafc; border-radius: 8px; padding: 0.6rem 0.75rem; font-size: 0.9rem; cursor: pointer; }
.addon-check-item input { width: auto; margin: 0; cursor: pointer; }

.price-summary { background: #f0fdf4; border: 1px solid #86efac; border-radius: 10px; padding: 1rem 1.25rem; margin: 1.25rem 0; }
.price-summary .row { display: flex; justify-content: space-between; font-size: 0.9rem; color: #334155; margin-bottom: 0.35rem; }
.price-summary .row.total { border-top: 1px solid #86efac; margin-top: 0.5rem; padding-top: 0.5rem; font-weight: 700; font-size: 1.1rem; color: #065f46; }

.book-btn {
    width: 100%; padding: 0.9rem; background: linear-gradient(135deg, #10b981, #059669);
    color: white; border: none; border-radius: 12px; font-weight: 800; font-size: 1.1rem;
    cursor: pointer; transition: opacity 0.2s; letter-spacing: 0.02em;
}
.book-btn:hover { opacity: 0.9; }

.no-vehicles { text-align: center; padding: 4rem 2rem; color: #94a3b8; grid-column: 1/-1; }
.no-vehicles h3 { font-size: 1.5rem; margin-bottom: 0.75rem; }

@media (max-width: 960px) {
    .rentals-main-layout { grid-template-columns: 1fr; }
    .booking-sidebar { position: static; order: -1; }
    .rentals-hero h1 { font-size: 2.25rem; }
}
@media (max-width: 600px) {
    .city-picker { flex-direction: column; }
}
</style>

<!-- Hero -->
<div class="rentals-hero">
    <h1>🗺️ Local Rental Services</h1>
    <p>Rent a bike, scooter, car, or SUV directly at your destination city</p>
</div>

<!-- City / Type Filter -->
<form method="GET" action="" class="city-picker">
    <div class="form-group">
        <label>Destination City</label>
        <select name="city" class="form-control">
            <option value="">All Cities</option>
            <?php foreach($cities as $c): ?>
            <option value="<?php echo htmlspecialchars($c); ?>" <?php echo ($selected_city == $c) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($c); ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Vehicle Type</label>
        <select name="type" class="form-control">
            <option value="">All Types</option>
            <option value="bike"  <?php echo ($selected_type == 'bike')  ? 'selected' : ''; ?>>🏍️ Bike / Scooter</option>
            <option value="car"   <?php echo ($selected_type == 'car')   ? 'selected' : ''; ?>>🚙 Car / SUV</option>
            <option value="cab"   <?php echo ($selected_type == 'cab')   ? 'selected' : ''; ?>>🚖 Cab</option>
        </select>
    </div>
    <button type="submit" class="btn-primary">🔍 Search</button>
</form>

<?php if ($selected_city): ?>
<div style="text-align:center; margin-bottom: 1.5rem; color: #64748b; font-size: 0.95rem;">
    Showing <strong><?php echo count($vehicles); ?></strong> vehicles in <strong><?php echo htmlspecialchars($selected_city); ?></strong>
    <?php if($selected_type): ?> · Type: <strong><?php echo ucfirst($selected_type); ?></strong><?php endif; ?>
    <a href="rentals.html" style="margin-left: 0.75rem; color: #ef4444; text-decoration: underline; font-size: 0.85rem;">× Clear Filters</a>
</div>
<?php endif; ?>

<!-- Main Layout: Vehicles + Sidebar -->
<div class="rentals-main-layout">
    <!-- Left: Vehicle Cards -->
    <div class="vehicle-list">
        <?php if(empty($vehicles)): ?>
        <div class="no-vehicles">
            <div style="font-size: 3rem; margin-bottom: 1rem;">🏙️</div>
            <h3>No vehicles found</h3>
            <p>Try choosing a different city or vehicle type.</p>
        </div>
        <?php else: ?>
        <?php foreach($vehicles as $v):
            $typeIcon = ($v['type'] === 'bike') ? '🏍️' : (($v['type'] === 'car') ? '🚙' : '🚖');
            $typeLabel = ($v['type'] === 'bike') ? '2-Wheeler' : (($v['type'] === 'car') ? '4-Wheeler' : 'Cab');
        ?>
        <div class="vehicle-card <?php echo $v['available'] ? '' : 'booked'; ?>">
            <div class="vehicle-img-wrap">
                <img src="<?php echo htmlspecialchars($v['image']); ?>" alt="<?php echo htmlspecialchars($v['name']); ?>" class="vehicle-img">
                <span class="badge-vtype"><?php echo $typeIcon; ?> <?php echo $typeLabel; ?></span>
                <span class="badge-city">📍 <?php echo htmlspecialchars($v['city']); ?></span>
                <span class="badge-status-avail <?php echo $v['available'] ? 'avail-yes' : 'avail-no'; ?>">
                    <?php echo $v['available'] ? 'Available' : 'Booked'; ?>
                </span>
            </div>
            <div class="vehicle-body">
                <div class="vehicle-name"><?php echo htmlspecialchars($v['name']); ?></div>
                <div class="vehicle-price">
                    ₹<?php echo number_format($v['price_per_day'], 0); ?> <small>/ day</small>
                </div>
                <div class="spec-row">
                    <div class="spec-pill"><span class="icon">⛽</span><?php echo $v['fuel_type']; ?></div>
                    <div class="spec-pill"><span class="icon">👥</span><?php echo $v['seating_capacity']; ?> Seats</div>
                    <div class="spec-pill"><span class="icon">🧳</span><?php echo $v['luggage_capacity']; ?> Bags</div>
                    <div class="spec-pill"><span class="icon">📏</span><?php echo $v['km_limit_per_day']; ?> km/day</div>
                    <div class="spec-pill"><span class="icon">🔒</span>₹<?php echo number_format($v['security_deposit'], 0); ?> Dep.</div>
                    <div class="spec-pill"><span class="icon">🚗</span>Self/Driver</div>
                </div>
                <button class="select-btn" onclick='selectVehicle(<?php echo json_encode(["id"=>$v['id'],"name"=>$v['name'],"price_per_day"=>$v['price_per_day'],"city"=>$v['city']]); ?>)'>
                    <?php echo $v['available'] ? '✅ Select Vehicle' : '❌ Not Available'; ?>
                </button>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Right: Booking Form Sidebar -->
    <div class="booking-sidebar">
        <div class="sidebar-header">
            <h3>📋 Book Your Rental</h3>
        </div>
        <div class="sidebar-body">
            <div class="selected-vehicle-banner" id="vehicleBanner">
                <div style="color: #94a3b8; font-size: 0.9rem;">← Select a vehicle to start booking</div>
            </div>

            <form method="POST" action="payment.html" id="bookingForm" onsubmit="prepareRentalSubmission(event)">
                <input type="hidden" name="booking_type" value="rental">
                <input type="hidden" name="item_id" id="input_rental_id">
                <input type="hidden" name="price" id="input_price">
                <input type="hidden" name="total_price" id="input_total_price">
                <input type="hidden" name="extra_details" id="input_extra_details">

                <!-- Daily date inputs -->
                <div id="daily_fields">
                    <div class="form-group">
                        <label>Pickup Date</label>
                        <input type="date" name="start_date" id="input_start_date" class="form-control" onchange="updatePriceCalc()" required>
                    </div>
                    <div class="form-group">
                        <label>Return Date</label>
                        <input type="date" name="end_date" id="input_end_date" class="form-control" onchange="updatePriceCalc()" required>
                    </div>
                </div>

                <!-- Driver Option -->
                <div class="form-group">
                    <label>Driver Option</label>
                    <select name="driver" id="input_driver" class="form-control" onchange="updatePriceCalc()">
                        <option value="self">🚗 Self Drive (Free)</option>
                        <option value="with_driver">👨‍✈️ With Driver (+₹500/day)</option>
                    </select>
                </div>

                <!-- Add-ons -->
                <div class="addon-section">
                    <h4>Add-ons</h4>
                    <div class="addon-checks">
                        <label class="addon-check-item">
                            <input type="checkbox" name="addon_helmet" id="chk_helmet" onchange="updatePriceCalc()">
                            🪖 Helmet &nbsp;<span style="color:#2563eb; font-weight:600;">+₹50</span>
                        </label>
                        <label class="addon-check-item">
                            <input type="checkbox" name="addon_gps" id="chk_gps" onchange="updatePriceCalc()">
                            🛰️ GPS Device &nbsp;<span style="color:#2563eb; font-weight:600;">+₹100</span>
                        </label>
                        <label class="addon-check-item">
                            <input type="checkbox" name="addon_childseat" id="chk_childseat" onchange="updatePriceCalc()">
                            🪑 Child Seat &nbsp;<span style="color:#2563eb; font-weight:600;">+₹150</span>
                        </label>
                    </div>
                </div>

                <!-- Price Summary -->
                <div class="price-summary">
                    <div class="row"><span>Base Rate</span><span id="disp_base">₹0</span></div>
                    <div class="row"><span>Add-ons</span><span id="disp_addons">₹0</span></div>
                    <div class="row"><span>Driver</span><span id="disp_driver">₹0</span></div>
                    <div class="row total"><span>TOTAL</span><span id="disp_total">₹0</span></div>
                </div>

                <button type="submit" class="book-btn" id="bookBtn" disabled>🔒 Login to Book</button>
            </form>
        </div>
    </div>
</div>

<script>
let selectedVehicle = null;

function selectVehicle(v) {
    selectedVehicle = v;
    document.getElementById('input_rental_id').value = v.id;
    document.getElementById('input_price').value = v.price_per_day; // Base price reference
    document.getElementById('vehicleBanner').innerHTML =
        '<div><div class="sel-name">✅ ' + v.name + '</div>' +
        '<div class="sel-price">📍 ' + v.city + ' · ₹' + Number(v.price_per_day).toLocaleString('en-IN') + '/day</div></div>';
    
    document.getElementById('bookBtn').disabled = false;
    document.getElementById('bookBtn').textContent = '🚀 Confirm Booking';
    document.getElementById('bookBtn').style.background = 'linear-gradient(135deg, #10b981, #059669)';
    
    updatePriceCalc();
    if (window.innerWidth <= 960) {
        document.querySelector('.booking-sidebar').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function updatePriceCalc() {
    if (!selectedVehicle) return;

    let base = 0;
    const s = new Date(document.getElementById('input_start_date').value);
    const e = new Date(document.getElementById('input_end_date').value);
    const days = (s && e && !isNaN(s) && !isNaN(e)) ? Math.max(1, Math.round((e-s)/86400000)+1) : 1;
    base = parseFloat(selectedVehicle.price_per_day) * days;

    let addons = 0;
    if (document.getElementById('chk_helmet').checked)    addons += 50;
    if (document.getElementById('chk_gps').checked)       addons += 100;
    if (document.getElementById('chk_childseat').checked) addons += 150;

    const driverOpt = document.getElementById('input_driver').value;
    let driverCost = 0;
    if (driverOpt === 'with_driver') {
        const s = new Date(document.getElementById('input_start_date').value);
        const e = new Date(document.getElementById('input_end_date').value);
        const days = (s && e && !isNaN(s) && !isNaN(e)) ? Math.max(1, Math.round((e-s)/86400000)+1) : 1;
        driverCost = 500 * days;
    }

    const total = base + addons + driverCost;
    const fmt = n => '₹' + n.toLocaleString('en-IN');
    document.getElementById('disp_base').textContent = fmt(base);
    document.getElementById('disp_addons').textContent = fmt(addons);
    document.getElementById('disp_driver').textContent = fmt(driverCost);
    document.getElementById('disp_total').textContent = fmt(total);
    
    // Update hidden fields for payment
    document.getElementById('input_total_price').value = total;
}

function prepareRentalSubmission(e) {
    if (!selectedVehicle) {
        e.preventDefault();
        alert('Please select a vehicle first.');
        return;
    }

    const addons = [];
    if (document.getElementById('chk_helmet').checked)    addons.push('Helmet');
    if (document.getElementById('chk_gps').checked)       addons.push('GPS');
    if (document.getElementById('chk_childseat').checked) addons.push('Child Seat');

    const extra = {
        vehicle_name: selectedVehicle.name,
        city: selectedVehicle.city,
        pricing_mode: 'daily',
        driver: document.getElementById('input_driver').value,
        addons: addons
    };

    document.getElementById('input_extra_details').value = JSON.stringify(extra);
}

// Set min date to today
const td = new Date().toISOString().split('T')[0];
document.getElementById('input_start_date').min = td;
document.getElementById('input_end_date').min = td;
</script>

<?php include 'includes/footer.php'; ?>
