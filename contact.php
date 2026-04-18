<?php
require_once 'config/database.php';

$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $message = sanitize($_POST['message']);
    
    if (!empty($name) && !empty($email) && !empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
        if ($stmt->execute([$name, $email, $message])) {
            $success = "Thank you for contacting us! We will get back to you shortly.";
            
            // Send Email Notification
            try {
                require_once 'libs/MailService.php';
                MailService::notifyAdmin($name, $email, $message);
            } catch (Exception $e) {
                // We don't block the user's success even if the email fails
                error_log("Email notification failed: " . $e->getMessage());
            }
        }
    }
}

include 'includes/header.php';
?>

<div style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://images.unsplash.com/photo-1528747045269-390fe24c1e22?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80') center/cover; margin: -2rem -2rem 2rem; padding: 4rem 2rem; text-align: center; color: white;">
    <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">Contact Us</h1>
    <p style="font-size: 1.1rem; max-width: 600px; margin: 0 auto;">We're here to help you plan your perfect trip.</p>
</div>

<div class="grid-2" style="max-width: 1000px; margin: 0 auto 3rem; gap: 3rem;">
    <div>
        <h2 style="margin-bottom: 1.5rem; color: var(--primary-dark);">Get in Touch</h2>
        <p style="color: #64748b; margin-bottom: 2rem; line-height: 1.8;">Have a question about our travel packages, custom tours, or need help with a booking? Our dedicated team is ready to assist you. Fill out the form, and we'll reply as soon as possible.</p>
        
        <div style="margin-bottom: 1.5rem; display: flex; align-items: start; gap: 1rem;">
            <div style="font-size: 1.5rem; color: var(--primary-color);">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
            </div>
            <div>
                <strong style="display: block; color: #1e293b;">Address</strong>
                <span style="color: #64748b;">123 Travel Agency Blvd, Globetrotter City<br>Travel State, 100100</span>
            </div>
        </div>
        
        <div style="margin-bottom: 1.5rem; display: flex; align-items: start; gap: 1rem;">
            <div style="font-size: 1.5rem; color: var(--primary-color);">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            </div>
            <div>
                <strong style="display: block; color: #1e293b;">Phone</strong>
                <span style="color: #64748b;"><a href="tel:9510243015" style="color: inherit; text-decoration: none;">9510243015</a><br>Mon-Fri, 9am - 6pm</span>
            </div>
        </div>
        
        <div style="display: flex; align-items: start; gap: 1rem;">
            <div style="font-size: 1.5rem; color: var(--primary-color);">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
            </div>
            <div>
                <strong style="display: block; color: #1e293b;">Email</strong>
                <span style="color: #64748b;"><a href="mailto:ksetcse@gmail.com" style="color: inherit; text-decoration: none;">ksetcse@gmail.com</a></span>
            </div>
        </div>
    </div>
    
    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <h3 style="margin-bottom: 1.5rem;">Send a Message</h3>
        
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php else: ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Your Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" class="form-control" rows="5" required placeholder="How can we help you?"></textarea>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%;">Send Message</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<div style="max-width: 1000px; margin: 3rem auto;">
    <h2 style="margin-bottom: 2rem; color: var(--primary-dark); text-align: center;">Recent Public Inquiries</h2>
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <?php
        $public_msgs = $pdo->query("SELECT name, message, response, responded_at, created_at FROM contact_messages ORDER BY created_at DESC LIMIT 5")->fetchAll();
        foreach($public_msgs as $pm):
        ?>
        <div style="background: white; padding: 1.5rem; border-radius: 8px; border: 1px solid var(--border-color);">
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <strong style="color: #1e293b;"><?php echo htmlspecialchars($pm['name']); ?></strong>
                <span style="color: #94a3b8; font-size: 0.8rem;"><?php echo date('M d, Y', strtotime($pm['created_at'])); ?></span>
            </div>
            <p style="color: #475569; margin: 0;"><?php echo nl2br(htmlspecialchars($pm['message'])); ?></p>
            
            <?php if($pm['response']): ?>
            <div style="margin-top: 1rem; padding: 1rem; background: #f8fafc; border-left: 3px solid var(--primary-color); border-radius: 4px;">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                    <span style="font-size: 1.1rem; color: var(--primary-color);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/></svg>
                    </span>
                    <strong style="color: var(--primary-dark); font-size: 0.9rem;">Team Travel Agency Reply</strong>
                    <span style="color: #94a3b8; font-size: 0.75rem; margin-left: auto;"><?php echo date('M d, Y', strtotime($pm['responded_at'])); ?></span>
                </div>
                <p style="color: #475569; font-size: 0.9rem; margin: 0; font-style: italic;">
                    "<?php echo htmlspecialchars($pm['response']); ?>"
                </p>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <?php if(empty($public_msgs)): ?>
            <p style="text-align: center; color: #94a3b8;">No public inquiries yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

