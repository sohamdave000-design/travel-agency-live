<?php
require_once '../config/database.php';

if (!isAdmin()) redirect('login.html');

$msg = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM packages WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    redirect('packages.html?msg=deleted');
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $destination = sanitize($_POST['destination']);
    $price = $_POST['price'];
    $duration = $_POST['duration'];
    $description = sanitize($_POST['description']);
    $image = sanitize($_POST['image']);
    $latitude = !empty($_POST['latitude']) ? $_POST['latitude'] : null;
    $longitude = !empty($_POST['longitude']) ? $_POST['longitude'] : null;
    
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Edit
        $stmt = $pdo->prepare("UPDATE packages SET name=?, destination=?, price=?, duration=?, description=?, image=?, latitude=?, longitude=? WHERE id=?");
        $stmt->execute([$name, $destination, $price, $duration, $description, $image, $latitude, $longitude, $_POST['id']]);
        $msg = "Package updated successfully.";
    } else {
        // Add
        $stmt = $pdo->prepare("INSERT INTO packages (name, destination, price, duration, description, image, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $destination, $price, $duration, $description, $image, $latitude, $longitude]);
        $msg = "Package added successfully.";
    }
}

if(isset($_GET['msg']) && $_GET['msg'] == 'deleted') $msg = "Package deleted successfully.";

$packages = $pdo->query("SELECT * FROM packages ORDER BY id DESC")->fetchAll();

include 'includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h1>Manage Packages</h1>
    <button onclick="openModal('packageModal')" class="btn-primary">+ Add New Package</button>
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
                    <th>Destination</th>
                    <th>Price</th>
                    <th>Duration</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($packages as $pkg): ?>
                <tr>
                    <td><?php echo $pkg['id']; ?></td>
                    <td><img src="<?php echo htmlspecialchars(get_image_url($pkg['image'])); ?>" width="50" height="50" style="object-fit:cover; border-radius:4px;"></td>
                    <td><?php echo htmlspecialchars($pkg['name']); ?></td>
                    <td><?php echo htmlspecialchars($pkg['destination']); ?></td>
                    <td>₹<?php echo number_format($pkg['price'], 2); ?></td>
                    <td><?php echo $pkg['duration']; ?> Days</td>
                    <td>
                        <button onclick="editPackage(<?php echo htmlspecialchars(json_encode($pkg)); ?>)" class="admin-action-btn btn-edit">Edit</button>
                        <a href="packages.html?delete=<?php echo $pkg['id']; ?>" class="admin-action-btn btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($packages)): ?>
                <tr><td colspan="7" class="text-center">No packages found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="packageModal" class="form-modal">
    <div class="form-modal-content">
        <span class="close-modal" onclick="closeModal('packageModal')">&times;</span>
        <h2 id="modalTitle" style="margin-bottom: 1.5rem;">Add Package</h2>
        <form method="POST" action="">
            <input type="hidden" name="id" id="pkg_id">
            
            <div class="form-group">
                <label>Package Name</label>
                <input type="text" name="name" id="pkg_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Destination</label>
                <input type="text" name="destination" id="pkg_destination" class="form-control" required>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label>Price ($)</label>
                    <input type="number" step="0.01" name="price" id="pkg_price" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Duration (Days)</label>
                    <input type="number" name="duration" id="pkg_duration" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label>Image URL</label>
                <input type="text" name="image" id="pkg_image" class="form-control" placeholder="https://..." required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" id="pkg_description" class="form-control" rows="4" required></textarea>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label>Latitude</label>
                    <input type="number" step="0.0001" name="latitude" id="pkg_latitude" class="form-control" placeholder="e.g. 21.1243">
                </div>
                <div class="form-group">
                    <label>Longitude</label>
                    <input type="number" step="0.0001" name="longitude" id="pkg_longitude" class="form-control" placeholder="e.g. 70.8242">
                </div>
            </div>
            <button type="submit" class="btn-primary" style="width: 100%;">Save Package</button>
        </form>
    </div>
</div>

<script>
function editPackage(pkg) {
    document.getElementById('modalTitle').innerText = 'Edit Package';
    document.getElementById('pkg_id').value = pkg.id;
    document.getElementById('pkg_name').value = pkg.name;
    document.getElementById('pkg_destination').value = pkg.destination;
    document.getElementById('pkg_price').value = pkg.price;
    document.getElementById('pkg_duration').value = pkg.duration;
    document.getElementById('pkg_image').value = pkg.image;
    document.getElementById('pkg_description').value = pkg.description;
    document.getElementById('pkg_latitude').value = pkg.latitude || '';
    document.getElementById('pkg_longitude').value = pkg.longitude || '';
    openModal('packageModal');
}
</script>

<?php include 'includes/footer.php'; ?>

