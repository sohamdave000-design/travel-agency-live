<?php
require_once 'config/database.php';
include 'includes/header.php';

$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isLoggedIn()) {
    $rating = (int)$_POST['rating'];
    $comment = sanitize($_POST['comment']);
    
    if ($rating >= 1 && $rating <= 5) {
        $stmt = $pdo->prepare("INSERT INTO reviews (user_id, rating, comment) VALUES (?, ?, ?)");
        if ($stmt->execute([$_SESSION['user_id'], $rating, $comment])) {
            $success = "Thank you for your feedback!";
        }
    }
}

$reviews = $pdo->query("SELECT r.*, u.name FROM reviews r JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC LIMIT 20")->fetchAll();
$avg_rating = $pdo->query("SELECT COALESCE(AVG(rating),0) FROM reviews")->fetchColumn();
$total_reviews = count($reviews);

// Star breakdown
$star_counts = [5=>0,4=>0,3=>0,2=>0,1=>0];
foreach($reviews as $r) { if(isset($star_counts[$r['rating']])) $star_counts[$r['rating']]++; }
?>

<div style="text-align: center; margin-bottom: 3rem;">
    <h1 style="font-size: 2.5rem; color: var(--primary-dark); margin-bottom: 1rem;" data-lang="reviews_title">Traveler's Reviews</h1>
    <p style="color: #64748b; font-size: 1.1rem;">See what our customers are saying about their experiences.</p>
</div>

<div style="max-width: 800px; margin: 0 auto; margin-bottom: 3rem;">
    
    <!-- Rating summary with star breakdown -->
    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 2rem; display: flex; flex-wrap: wrap; gap: 2rem;">
        <div style="text-align: center; min-width: 120px;">
            <div style="font-size: 4rem; font-weight: 700; color: #f59e0b; line-height: 1;"><?php echo number_format($avg_rating, 1); ?></div>
            <div style="color: #f59e0b; font-size: 1.5rem; margin: 0.25rem 0;"><?php echo str_repeat('★', round($avg_rating)) . str_repeat('☆', 5-round($avg_rating)); ?></div>
            <div style="color: #64748b; font-size: 0.875rem;"><?php echo $total_reviews; ?> reviews</div>
        </div>
        
        <!-- Star Breakdown Bars -->
        <div style="flex: 1; min-width: 200px;">
            <?php for($s=5; $s>=1; $s--): ?>
            <?php $pct = $total_reviews > 0 ? round(($star_counts[$s]/$total_reviews)*100) : 0; ?>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                <span style="font-size: 0.8rem; width: 14px; color: #64748b;"><?php echo $s; ?>★</span>
                <div style="flex: 1; background: #e2e8f0; border-radius: 4px; height: 10px; overflow: hidden;">
                    <div style="width: <?php echo $pct; ?>%; height: 100%; background: #f59e0b; border-radius: 4px; transition: width 0.5s;"></div>
                </div>
                <span style="font-size: 0.75rem; color: #94a3b8; width: 35px; text-align: right;"><?php echo $star_counts[$s]; ?></span>
            </div>
            <?php endfor; ?>
        </div>
        
        <!-- Submit Form -->
        <div style="flex: 1; min-width: 250px; border-left: 1px solid var(--border-color); padding-left: 2rem;">
            <?php if(isLoggedIn()): ?>
                <h3 style="margin-bottom: 1rem;">Share your experience</h3>
                <?php if($success): ?>
                    <div class="alert alert-success" style="padding: 0.5rem;"><?php echo $success; ?></div>
                <?php else: ?>
                    <form method="POST" action="" style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <div>
                            <label style="font-size: 0.875rem; font-weight: 500; display: block; margin-bottom: 0.25rem;">Your Rating</label>
                            <div id="star-rating" style="display: flex; gap: 4px; cursor: pointer;">
                                <?php for($i=1; $i<=5; $i++): ?>
                                <span class="rate-star" data-val="<?php echo $i; ?>" onclick="setRating(<?php echo $i; ?>)" style="font-size: 2rem; color: #d1d5db; transition: color 0.2s;">★</span>
                                <?php endfor; ?>
                            </div>
                            <input type="hidden" name="rating" id="rating-input" required>
                        </div>
                        <textarea name="comment" class="form-control" rows="3" placeholder="Write your review..." required></textarea>
                        <button type="submit" class="btn-primary">Submit Review</button>
                    </form>
                <?php endif; ?>
            <?php else: ?>
                <h3 style="margin-bottom: 0.5rem;">Have you traveled with us?</h3>
                <p style="color: #64748b; margin-bottom: 1rem;">Log in to share your experience.</p>
                <a href="login.html" class="btn-secondary">Login to Write Review</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Review items -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <?php foreach($reviews as $review): ?>
        <div style="background: white; padding: 1.5rem; border-radius: 8px; border: 1px solid var(--border-color);">
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <div>
                    <strong style="font-size: 1.1rem; color: #1e293b;"><?php echo htmlspecialchars($review['name']); ?></strong>
                    <span style="color: #f59e0b; font-size: 1.1rem; margin-left: 0.5rem;">
                        <?php echo str_repeat('★', $review['rating']) . str_repeat('☆', 5-$review['rating']); ?>
                    </span>
                </div>
                <span style="color: #94a3b8; font-size: 0.8rem;"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></span>
            </div>
            <p style="color: #475569; line-height: 1.6; margin: 0;"><?php echo htmlspecialchars($review['comment']); ?></p>
            
            <?php if($review['response']): ?>
            <div style="margin-top: 1rem; padding: 1rem; background: #f8fafc; border-left: 3px solid var(--secondary-color); border-radius: 4px;">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                    <span style="font-size: 1.2rem;"></span>
                    <strong style="color: var(--secondary-dark); font-size: 0.9rem;">Admin Response</strong>
                    <span style="color: #94a3b8; font-size: 0.75rem; margin-left: auto;"><?php echo date('M d, Y', strtotime($review['responded_at'])); ?></span>
                </div>
                <p style="color: #475569; font-size: 0.9rem; margin: 0; font-style: italic;">
                    "<?php echo htmlspecialchars($review['response']); ?>"
                </p>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        
        <?php if(empty($reviews)): ?>
        <div style="text-align: center; padding: 3rem; background: var(--card-bg); border-radius: 8px;">
            <h3>No reviews yet.</h3>
            <p style="color: #64748b;">Be the first to leave a review!</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function setRating(val) {
    document.getElementById('rating-input').value = val;
    document.querySelectorAll('.rate-star').forEach(star => {
        star.style.color = star.dataset.val <= val ? '#f59e0b' : '#d1d5db';
    });
}
</script>

<?php include 'includes/footer.php'; ?>

