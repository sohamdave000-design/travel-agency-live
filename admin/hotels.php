<?php
require_once '../config/database.php';

if (!isAdmin()) redirect('login.html');

$msg = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM hotels WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    redirect('hotels.html?msg=deleted');
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $location = sanitize($_POST['location']);
    $price_per_night = $_POST['price_per_night'];
    $rating = $_POST['rating'];
    $description = sanitize($_POST['description']);
    $image = sanitize($_POST['image']);
    $amenities = isset($_POST['amenities']) ? sanitize($_POST['amenities']) : '';
    
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $stmt = $pdo->prepare("UPDATE hotels SET name=?, location=?, price_per_night=?, rating=?, description=?, image=?, amenities=? WHERE id=?");
        $stmt->execute([$name, $location, $price_per_night, $rating, $description, $image, $amenities, $_POST['id']]);
        $msg = "Hotel updated successfully.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO hotels (name, location, price_per_night, rating, description, image, amenities) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $location, $price_per_night, $rating, $description, $image, $amenities]);
        $msg = "Hotel added successfully.";
    }
}

if(isset($_GET['msg']) && $_GET['msg'] == 'deleted') $msg = "Hotel deleted successfully.";

$hotels = $pdo->query("SELECT * FROM hotels ORDER BY id DESC")->fetchAll();

include 'includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h1>Manage Hotels</h1>
    <button onclick="openModal('hotelModal')" class="btn-primary">+ Add New Hotel</button>
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
                    <th>Location</th>
                    <th>Price/Night</th>
                    <th>Rating</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($hotels as $hotel): ?>
                <tr>
                    <td><?php echo $hotel['id']; ?></td>
                    <td><img src="<?php echo htmlspecialchars(get_image_url($hotel['image'])); ?>" width="50" height="50" style="object-fit:cover; border-radius:4px;"></td>
                    <td><?php echo htmlspecialchars($hotel['name']); ?></td>
                    <td><?php echo htmlspecialchars($hotel['location']); ?></td>
                    <td>₹<?php echo number_format($hotel['price_per_night'], 2); ?></td>
                    <td><?php echo $hotel['rating']; ?> / 5</td>
                    <td>
                        <button onclick="editHotel(<?php echo htmlspecialchars(json_encode($hotel)); ?>)" class="admin-action-btn btn-edit">Edit</button>
                        <a href="hotels.html?delete=<?php echo $hotel['id']; ?>" class="admin-action-btn btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($hotels)): ?>
                <tr><td colspan="7" class="text-center">No hotels found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="hotelModal" class="form-modal">
    <div class="form-modal-content">
        <span class="close-modal" onclick="closeModal('hotelModal')">&times;</span>
        <h2 id="modalTitle" style="margin-bottom: 1.5rem;">Add Hotel</h2>
        <form method="POST" action="">
            <input type="hidden" name="id" id="hotel_id">
            
            <div class="form-group">
                <label>Hotel Name</label>
                <input type="text" name="name" id="hotel_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Location</label>
                <input type="text" name="location" id="hotel_location" class="form-control" required>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label>Price per Night ($)</label>
                    <input type="number" step="0.01" name="price_per_night" id="hotel_price" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Rating (1-5)</label>
                    <input type="number" step="0.1" max="5" min="1" name="rating" id="hotel_rating" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label>Image URL</label>
                <input type="text" name="image" id="hotel_image" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Amenities</label>
                <input type="text" name="amenities" id="hotel_amenities" class="form-control" placeholder="e.g. Free Wi-Fi, Pool, AC">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" id="hotel_description" class="form-control" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn-primary" style="width: 100%;">Save Hotel</button>
        </form>
    </div>
</div>

<script>
function editHotel(hotel) {
    document.getElementById('modalTitle').innerText = 'Edit Hotel';
    document.getElementById('hotel_id').value = hotel.id;
    document.getElementById('hotel_name').value = hotel.name;
    document.getElementById('hotel_location').value = hotel.location;
    document.getElementById('hotel_price').value = hotel.price_per_night;
    document.getElementById('hotel_rating').value = hotel.rating;
    document.getElementById('hotel_image').value = hotel.image;
    document.getElementById('hotel_description').value = hotel.description;
    document.getElementById('hotel_amenities').value = hotel.amenities ? hotel.amenities : '';
    openModal('hotelModal');
}
</script>

<?php include 'includes/footer.php'; ?>

