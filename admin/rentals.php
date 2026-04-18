<?php
require_once '../config/database.php';

if (!isAdmin()) redirect('login.html');

$msg = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM rentals WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    redirect('rentals.html?msg=deleted');
}

// Handle Toggle Availability
if (isset($_GET['toggle'])) {
    $stmt = $pdo->prepare("UPDATE rentals SET available = NOT available WHERE id = ?");
    $stmt->execute([$_GET['toggle']]);
    redirect('rentals.html?msg=toggled');
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type              = sanitize($_POST['type']);
    $name              = sanitize($_POST['name']);
    $city              = sanitize($_POST['city']);
    $price_per_day     = floatval($_POST['price_per_day']);
    $seating_capacity  = intval($_POST['seating_capacity']);
    $fuel_type         = sanitize($_POST['fuel_type']);
    $luggage_capacity  = intval($_POST['luggage_capacity']);
    $security_deposit  = floatval($_POST['security_deposit']);
    $km_limit_per_day  = intval($_POST['km_limit_per_day']);
    $image             = sanitize($_POST['image']);
    $description       = sanitize($_POST['description']);
    $available         = isset($_POST['available']) ? 1 : 0;
    if (!empty($_POST['id'])) {
        $stmt = $pdo->prepare("UPDATE rentals SET type=?, name=?, city=?, price_per_day=?, seating_capacity=?, fuel_type=?, luggage_capacity=?, security_deposit=?, km_limit_per_day=?, image=?, description=?, available=? WHERE id=?");
        $stmt->execute([$type, $name, $city, $price_per_day, $seating_capacity, $fuel_type, $luggage_capacity, $security_deposit, $km_limit_per_day, $image, $description, $available, $_POST['id']]);
        $msg = "Vehicle updated successfully.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO rentals (type, name, city, price_per_day, seating_capacity, fuel_type, luggage_capacity, security_deposit, km_limit_per_day, image, description, available) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$type, $name, $city, $price_per_day, $seating_capacity, $fuel_type, $luggage_capacity, $security_deposit, $km_limit_per_day, $image, $description, $available]);
        $msg = "Vehicle added successfully.";
    }
}

if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'deleted') $msg = "Vehicle deleted successfully.";
    if ($_GET['msg'] == 'toggled') $msg = "Vehicle availability updated.";
}

$rentals = $pdo->query("SELECT * FROM rentals ORDER BY city ASC, type ASC, id DESC")->fetchAll();

include 'includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h1>🚗 Manage Local Rentals</h1>
    <button onclick="openModal('rentalModal')" class="btn-primary">+ Add New Vehicle</button>
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
                    <th>Image</th>
                    <th>Name</th>
                    <th>City</th>
                    <th>Type</th>
                    <th>Day ₹</th>
                    <th>Fuel</th>
                    <th>Seats</th>
                    <th>KM/Day</th>
                    <th>Deposit</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($rentals as $r): ?>
                <tr>
                    <td><?php echo $r['id']; ?></td>
                    <td><img src="<?php echo htmlspecialchars(get_image_url($r['image'])); ?>" width="55" height="45" style="object-fit:cover; border-radius:6px;"></td>
                    <td><strong><?php echo htmlspecialchars($r['name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($r['city']); ?></td>
                    <td><span style="text-transform:capitalize;"><?php echo $r['type']; ?></span></td>
                    <td>₹<?php echo number_format($r['price_per_day'], 0); ?></td>
                    <td><?php echo $r['fuel_type']; ?></td>
                    <td><?php echo $r['seating_capacity']; ?></td>
                    <td><?php echo $r['km_limit_per_day']; ?></td>
                    <td>₹<?php echo number_format($r['security_deposit'], 0); ?></td>
                    <td>
                        <?php if($r['available']): ?>
                            <span class="badge badge-confirmed">Available</span>
                        <?php else: ?>
                            <span class="badge badge-cancelled">Booked</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button onclick='editRental(<?php echo json_encode($r); ?>)' class="admin-action-btn btn-edit">Edit</button>
                        <a href="rentals.html?toggle=<?php echo $r['id']; ?>" class="admin-action-btn" style="background:#fef9c3;color:#854d0e;">Toggle</a>
                        <a href="rentals.html?delete=<?php echo $r['id']; ?>" class="admin-action-btn btn-delete" onclick="return confirm('Delete this vehicle?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($rentals)): ?>
                <tr><td colspan="13" class="text-center" style="padding:2rem; color:#94a3b8;">No vehicles found. Add one above!</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="rentalModal" class="form-modal">
    <div class="form-modal-content" style="max-width: 650px;">
        <span class="close-modal" onclick="closeModal('rentalModal')">&times;</span>
        <h2 id="rentalModalTitle" style="margin-bottom: 1.5rem;">Add Vehicle</h2>
        <form method="POST" action="">
            <input type="hidden" name="id" id="r_id">
            <div class="grid-2">
                <div class="form-group">
                    <label>Vehicle Name</label>
                    <input type="text" name="name" id="r_name" class="form-control" placeholder="e.g. Royal Enfield Classic 350" required>
                </div>
                <div class="form-group">
                    <label>City</label>
                    <input type="text" name="city" id="r_city" class="form-control" placeholder="e.g. Goa, Manali, Kashmir" required>
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label>Vehicle Type</label>
                    <select name="type" id="r_type" class="form-control" required>
                        <option value="bike">Bike / Scooter</option>
                        <option value="car">Car / SUV</option>
                        <option value="cab">Cab</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Fuel Type</label>
                    <select name="fuel_type" id="r_fuel_type" class="form-control" required>
                        <option value="Petrol">Petrol</option>
                        <option value="Diesel">Diesel</option>
                        <option value="Electric">Electric</option>
                        <option value="CNG">CNG</option>
                        <option value="Hybrid">Hybrid</option>
                    </select>
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label>Price Per Day (₹)</label>
                    <input type="number" step="0.01" name="price_per_day" id="r_price_per_day" class="form-control" required>
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label>Seating Capacity</label>
                    <input type="number" name="seating_capacity" id="r_seating_capacity" class="form-control" value="2" required>
                </div>
                <div class="form-group">
                    <label>Luggage Capacity (bags)</label>
                    <input type="number" name="luggage_capacity" id="r_luggage_capacity" class="form-control" value="1">
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label>KM Limit / Day</label>
                    <input type="number" name="km_limit_per_day" id="r_km_limit_per_day" class="form-control" value="100">
                </div>
                <div class="form-group">
                    <label>Security Deposit (₹)</label>
                    <input type="number" step="0.01" name="security_deposit" id="r_security_deposit" class="form-control" value="0">
                </div>
            </div>
            <div class="form-group">
                <label>Image URL</label>
                <input type="text" name="image" id="r_image" class="form-control" placeholder="https://...">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" id="r_description" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group" style="display:flex; align-items:center; gap:0.75rem;">
                <input type="checkbox" name="available" id="r_available" checked style="width:auto;">
                <label for="r_available" style="margin:0;">Mark as Available</label>
            </div>
            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 0.5rem;">Save Vehicle</button>
        </form>
    </div>
</div>

<script>
function editRental(r) {
    document.getElementById('rentalModalTitle').innerText = 'Edit Vehicle';
    document.getElementById('r_id').value = r.id;
    document.getElementById('r_name').value = r.name;
    document.getElementById('r_city').value = r.city;
    document.getElementById('r_type').value = r.type;
    document.getElementById('r_fuel_type').value = r.fuel_type;
    document.getElementById('r_price_per_day').value = r.price_per_day;
    document.getElementById('r_seating_capacity').value = r.seating_capacity;
    document.getElementById('r_luggage_capacity').value = r.luggage_capacity;
    document.getElementById('r_km_limit_per_day').value = r.km_limit_per_day;
    document.getElementById('r_security_deposit').value = r.security_deposit;
    document.getElementById('r_image').value = r.image;
    document.getElementById('r_description').value = r.description;
    document.getElementById('r_available').checked = (r.available == 1 || r.available == true);
    openModal('rentalModal');
}
</script>

<?php include 'includes/footer.php'; ?>
