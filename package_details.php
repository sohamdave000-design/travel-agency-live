<?php
require_once 'config/database.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('packages.html');
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM packages WHERE id = ?");
$stmt->execute([$id]);
$package = $stmt->fetch();

if (!$package) {
    redirect('packages.html');
}

include 'includes/header.php';
?>

<div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 2rem;">
    <div style="height: 400px; overflow: hidden;">
        <img src="<?php echo htmlspecialchars($package['image']); ?>" alt="<?php echo htmlspecialchars($package['name']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
    </div>
    
    <div style="padding: 2.5rem; display: grid; grid-template-columns: 2fr 1fr; gap: 3rem;">
        <div>
            <h1 style="color: var(--primary-dark); margin-bottom: 0.5rem; font-size: 2.5rem;"><?php echo htmlspecialchars($package['name']); ?></h1>
            <p style="color: #64748b; font-size: 1.1rem; margin-bottom: 2rem;">
                  Destination: <strong><?php echo htmlspecialchars($package['destination']); ?></strong> | 
                ⏱️ Duration: <strong><?php echo $package['duration']; ?> Days</strong>
            </p>
            
            <h3 style="margin-bottom: 1rem;">About This Package</h3>
            <div style="color: #475569; line-height: 1.8; white-space: pre-wrap; margin-bottom: 2rem;"><?php echo htmlspecialchars($package['description']); ?></div>
        </div>
        
        <div style="background: #f8fafc; padding: 2rem; border-radius: 8px; border: 1px solid var(--border-color); height: fit-content; position: sticky; top: 100px;">
            <h3 style="margin-bottom: 1.5rem; text-align: center;">Booking Summary</h3>
            <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
                <span>Price per person</span>
                <strong style="color: var(--primary-color); font-size: 1.25rem;">₹<?php echo number_format($package['price'], 2); ?></strong>
            </div>
            
            <form action="payment.html" method="POST">
                <input type="hidden" name="booking_type" value="package">
                <input type="hidden" name="item_id" value="<?php echo $package['id']; ?>">
                <input type="hidden" name="price" value="<?php echo $package['price']; ?>">
                
                <div class="form-group">
                    <label>Travel Date</label>
                    <input type="date" name="start_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label>Number of Persons</label>
                    <input type="number" name="persons" class="form-control" value="1" min="1" required id="personsCount">
                </div>
                
                <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem; font-size: 1.25rem; font-weight: 700;">
                    <span>Total Amount:</span>
                    <span style="color: var(--primary-color);" id="totalAmount">₹<?php echo number_format($package['price'], 2); ?></span>
                </div>
                
                <?php if(isLoggedIn()): ?>
                    <button type="submit" class="btn-primary" style="width: 100%; font-size: 1.1rem; padding: 1rem;">Book Now</button>
                <?php else: ?>
                    <a href="login.html" class="btn-secondary" style="display: block; width: 100%; text-align: center; padding: 1rem;">Login to Book</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('personsCount').addEventListener('input', function() {
    let persons = parseInt(this.value) || 0;
    let price = <?php echo $package['price']; ?>;
    let total = (persons * price).toFixed(2);
    document.getElementById('totalAmount').innerText = '₹' + total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
});
</script>

<?php include 'includes/footer.php'; ?>

