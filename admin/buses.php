<?php
require_once '../config/database.php';

if (!isAdmin()) redirect('login.html');

$msg = '';

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM buses WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    redirect('buses.html?msg=deleted');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $from = sanitize($_POST['from_location']);
    $to = sanitize($_POST['to_location']);
    $date = sanitize($_POST['departure_date']);
    $time = sanitize($_POST['departure_time']);
    $price = $_POST['price'];
    $seats = $_POST['total_seats'];
    
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $stmt = $pdo->prepare("UPDATE buses SET from_location=?, to_location=?, departure_date=?, departure_time=?, price=?, total_seats=?, available_seats=? WHERE id=?");
        $stmt->execute([$from, $to, $date, $time, $price, $seats, $seats, $_POST['id']]); // Reset available seats on update for simplicity
        $msg = "Bus route updated successfully.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO buses (from_location, to_location, departure_date, departure_time, price, total_seats, available_seats) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$from, $to, $date, $time, $price, $seats, $seats]);
        $msg = "Bus route added successfully.";
    }
}

if(isset($_GET['msg']) && $_GET['msg'] == 'deleted') $msg = "Bus route deleted successfully.";

$buses = $pdo->query("SELECT * FROM buses ORDER BY departure_date DESC")->fetchAll();

include 'includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h1>Manage Bus Routes</h1>
    <button onclick="openModal('busModal')" class="btn-primary">+ Add New Bus</button>
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
                    <th>Route</th>
                    <th>Date & Time</th>
                    <th>Price</th>
                    <th>Seats (Avail/Total)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($buses as $bus): ?>
                <tr>
                    <td><?php echo $bus['id']; ?></td>
                    <td><?php echo htmlspecialchars($bus['from_location']) . ' &rarr; ' . htmlspecialchars($bus['to_location']); ?></td>
                    <td><?php echo date('M d, Y', strtotime($bus['departure_date'])) . ' ' . date('h:i A', strtotime($bus['departure_time'])); ?></td>
                    <td>₹<?php echo number_format($bus['price'], 2); ?></td>
                    <td><?php echo $bus['available_seats']; ?> / <?php echo $bus['total_seats']; ?></td>
                    <td>
                        <button onclick="editBus(<?php echo htmlspecialchars(json_encode($bus)); ?>)" class="admin-action-btn btn-edit">Edit</button>
                        <a href="buses.html?delete=<?php echo $bus['id']; ?>" class="admin-action-btn btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($buses)): ?>
                <tr><td colspan="6" class="text-center">No buses found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="busModal" class="form-modal">
    <div class="form-modal-content">
        <span class="close-modal" onclick="closeModal('busModal')">&times;</span>
        <h2 id="modalTitle" style="margin-bottom: 1.5rem;">Add Bus Route</h2>
        <form method="POST" action="">
            <input type="hidden" name="id" id="bus_id">
            
            <div class="grid-2">
                <div class="form-group">
                    <label>From Location</label>
                    <input type="text" name="from_location" id="bus_from" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>To Location</label>
                    <input type="text" name="to_location" id="bus_to" class="form-control" required>
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label>Departure Date</label>
                    <input type="date" name="departure_date" id="bus_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Departure Time</label>
                    <input type="time" name="departure_time" id="bus_time" class="form-control" required>
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label>Ticket Price (₹)</label>
                    <input type="number" step="0.01" name="price" id="bus_price" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Total Seats</label>
                    <input type="number" name="total_seats" id="bus_seats" class="form-control" required>
                </div>
            </div>
            <button type="submit" class="btn-primary" style="width: 100%;">Save Bus Route</button>
        </form>
    </div>
</div>

<script>
function editBus(bus) {
    document.getElementById('modalTitle').innerText = 'Edit Bus';
    document.getElementById('bus_id').value = bus.id;
    document.getElementById('bus_from').value = bus.from_location;
    document.getElementById('bus_to').value = bus.to_location;
    document.getElementById('bus_date').value = bus.departure_date;
    document.getElementById('bus_time').value = bus.departure_time;
    document.getElementById('bus_price').value = bus.price;
    document.getElementById('bus_seats').value = bus.total_seats;
    openModal('busModal');
}
</script>

<?php include 'includes/footer.php'; ?>

