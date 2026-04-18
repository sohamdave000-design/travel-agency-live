<?php
require_once 'config/database.php';
include 'includes/header.php';

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$whereClause = '';
$params = [];

if ($search) {
    $whereClause = "WHERE name LIKE ? OR destination LIKE ?";
    $params = ["%$search%", "%$search%"];
}

$stmt = $pdo->prepare("SELECT * FROM packages $whereClause ORDER BY id DESC");
$stmt->execute($params);
$packages = $stmt->fetchAll();

// Get user wishlist items
$user_wishlist = [];
if (isLoggedIn()) {
    $wl = $pdo->prepare("SELECT package_id FROM wishlist WHERE user_id = ?");
    $wl->execute([$_SESSION['user_id']]);
    $user_wishlist = $wl->fetchAll(PDO::FETCH_COLUMN);
}
?>

<div style="background: linear-gradient(to right, #2563eb, #10b981); margin: -2rem -2rem 2rem; padding: 4rem 2rem; text-align: center; color: white;">
    <h1 style="font-size: 2.5rem; margin-bottom: 1rem;" data-lang="explore_packages">Explore Our Tour Packages</h1>
    <p style="font-size: 1.1rem; max-width: 600px; margin: 0 auto;" data-lang="discover_text">Discover the world's most amazing destinations with our curated travel packages.</p>
</div>

<div class="filters-section">
    <form method="GET" action="" class="filters-grid">
        <div class="form-group" style="margin-bottom: 0;">
            <label data-lang="search_dest">Search Destination</label>
            <input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($search); ?>" placeholder="e.g., Goa, Manali...">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <button type="submit" class="btn-primary" style="height: 42px;" data-lang="search_btn">Search Packages</button>
            <?php if($search): ?>
                <a href="packages.html" class="btn-secondary" style="height: 42px; line-height: 26px; margin-left: 0.5rem;">Clear</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card-grid">
    <?php foreach($packages as $pkg): ?>
    <div class="card">
        <div style="position: relative;">
            <img src="<?php echo htmlspecialchars($pkg['image']); ?>" alt="<?php echo htmlspecialchars($pkg['name']); ?>" class="card-img">
            <?php if(isLoggedIn()): ?>
            <button onclick="toggleWishlist(<?php echo $pkg['id']; ?>, this)" 
                    style="position: absolute; top: 0.75rem; right: 0.75rem; background: rgba(255,255,255,0.9); border: none; width: 36px; height: 36px; border-radius: 50%; font-size: 1.25rem; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.2); transition: transform 0.2s;"
                    class="wishlist-btn <?php echo in_array($pkg['id'], $user_wishlist) ? 'wishlisted' : ''; ?>">
                <?php echo in_array($pkg['id'], $user_wishlist) ? '❤️' : '🤍'; ?>
            </button>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <h3 class="card-title"><?php echo htmlspecialchars($pkg['name']); ?></h3>
            <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 0.5rem;">  <?php echo htmlspecialchars($pkg['destination']); ?> | ⏱️ <?php echo $pkg['duration']; ?> Days</p>
            <p class="card-text"><?php echo substr(htmlspecialchars($pkg['description']), 0, 100) . '...'; ?></p>
            
            <?php if($pkg['latitude'] && $pkg['longitude']): ?>
            <div style="margin-bottom: 1rem; border-radius: 6px; overflow: hidden; height: 120px;">
                <iframe src="https://maps.google.com/maps?q=<?php echo $pkg['latitude']; ?>,<?php echo $pkg['longitude']; ?>&z=10&output=embed" 
                        width="100%" height="120" style="border:0;" loading="lazy"></iframe>
            </div>
            <?php endif; ?>
            
            <div style="margin-top: auto; display: flex; justify-content: space-between; align-items: center;">
                <div class="card-price">₹<?php echo number_format($pkg['price'], 2); ?></div>
                <a href="package_details.html?id=<?php echo $pkg['id']; ?>" class="btn-primary">View Details</a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    
    <?php if(empty($packages)): ?>
    <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem; background: var(--card-bg); border-radius: 8px;">
        <h3>No packages found</h3>
        <p style="color: #64748b; margin-top: 0.5rem;">Try adjusting your search criteria.</p>
    </div>
    <?php endif; ?>
</div>

<script>
function toggleWishlist(packageId, btn) {
    const isWished = btn.classList.contains('wishlisted');
    const action = isWished ? 'remove' : 'add';
    
    fetch('wishlist_action.html', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=' + action + '&package_id=' + packageId
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            btn.classList.toggle('wishlisted');
            btn.innerHTML = data.action === 'added' ? '❤️' : '🤍';
            btn.style.transform = 'scale(1.3)';
            setTimeout(() => btn.style.transform = 'scale(1)', 200);
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?>

