<?php
require_once '../config/database.php';

if (!isAdmin()) redirect('login.html');

$msg = '';

if (isset($_GET['delete'])) {
    $type = sanitize($_GET['type']);
    $id = (int)$_GET['delete'];
    
    if ($type == 'message') {
        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
        $stmt->execute([$id]);
        $msg = "Contact message deleted.";
    } elseif ($type == 'review') {
        $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
        $stmt->execute([$id]);
        $msg = "Review deleted.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $id = (int)$_POST['id'];
    $response = sanitize($_POST['response']);
    
    if ($action == 'reply_message') {
        $stmt = $pdo->prepare("UPDATE contact_messages SET response = ?, responded_at = NOW() WHERE id = ?");
        $stmt->execute([$response, $id]);
        $msg = "Reply sent successfully.";
    } elseif ($action == 'reply_review') {
        $stmt = $pdo->prepare("UPDATE reviews SET response = ?, responded_at = NOW() WHERE id = ?");
        $stmt->execute([$response, $id]);
        $msg = "Response posted successfully.";
    }
}

$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();
$reviews = $pdo->query("SELECT r.*, u.name as user_name FROM reviews r JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC")->fetchAll();

include 'includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h1>Messages & Reviews</h1>
</div>

<?php if($msg): ?>
<div class="alert alert-success"><?php echo $msg; ?></div>
<?php endif; ?>

<h2 style="margin-top: 2rem; margin-bottom: 1rem; color: var(--primary-dark);">Contact Messages</h2>
<div class="admin-table" style="margin-bottom: 3rem;">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($messages as $m): ?>
                <tr>
                    <td style="white-space: nowrap;"><?php echo date('M d, Y h:i A', strtotime($m['created_at'])); ?></td>
                    <td><strong><?php echo htmlspecialchars($m['name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($m['email']); ?></td>
                    <td style="max-width: 400px;"><?php echo nl2br(htmlspecialchars($m['message'])); ?></td>
                    <td>
                        <button onclick="openReplyModal('message', <?php echo $m['id']; ?>, <?php echo htmlspecialchars(json_encode($m['message'])); ?>)" class="admin-action-btn btn-edit">Reply</button>
                        <a href="messages.html?type=message&delete=<?php echo $m['id']; ?>" class="admin-action-btn btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                        <?php if($m['response']): ?>
                            <div style="margin-top: 0.5rem; font-size: 0.8rem; color: var(--secondary-dark);">Replied: <?php echo date('M d, Y', strtotime($m['responded_at'])); ?></div>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($messages)): ?>
                <tr><td colspan="5" class="text-center">No contact messages found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<h2 style="margin-bottom: 1rem; color: var(--primary-dark);">User Reviews</h2>
<div class="admin-table">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>User</th>
                    <th>Rating</th>
                    <th>Review</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($reviews as $r): ?>
                <tr>
                    <td style="white-space: nowrap;"><?php echo date('M d, Y', strtotime($r['created_at'])); ?></td>
                    <td><strong><?php echo htmlspecialchars($r['user_name']); ?></strong></td>
                    <td><span style="color: #f59e0b;"><?php echo str_repeat('â˜…', $r['rating']) . str_repeat('â˜†', 5-$r['rating']); ?></span></td>
                    <td style="max-width: 400px;"><?php echo nl2br(htmlspecialchars($r['comment'])); ?></td>
                    <td>
                        <button onclick="openReplyModal('review', <?php echo $r['id']; ?>, <?php echo htmlspecialchars(json_encode($r['comment'])); ?>)" class="admin-action-btn btn-edit">Respond</button>
                        <a href="messages.html?type=review&delete=<?php echo $r['id']; ?>" class="admin-action-btn btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                        <?php if($r['response']): ?>
                            <div style="margin-top: 0.5rem; font-size: 0.8rem; color: var(--secondary-dark);">Responded: <?php echo date('M d, Y', strtotime($r['responded_at'])); ?></div>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($reviews)): ?>
                <tr><td colspan="5" class="text-center">No reviews found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Reply Modal -->
<div id="replyModal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
    <div style="background:var(--card-bg); padding:2rem; border-radius:12px; width:90%; max-width:500px; box-shadow:0 10px 25px rgba(0,0,0,0.1);">
        <h3 id="modalTitle" style="margin-bottom:1rem; color:var(--primary-dark);">Reply to Message</h3>
        <div id="originalText" style="margin-bottom:1rem; padding:1rem; background:rgba(0,0,0,0.02); border-left:4px solid var(--primary-color); font-size:0.9rem; color:var(--text-muted);"></div>
        <form method="POST">
            <input type="hidden" name="action" id="modalAction">
            <input type="hidden" name="id" id="modalId">
            <div class="form-group">
                <label>Your Response</label>
                <textarea name="response" class="form-control" rows="5" required placeholder="Type your reply here..."></textarea>
            </div>
            <div style="display:flex; gap:1rem; margin-top:1.5rem;">
                <button type="submit" class="btn-primary" style="flex:1;">Send Reply</button>
                <button type="button" onclick="closeModal()" class="btn-secondary" style="background:#64748b; flex:1;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openReplyModal(type, id, text) {
    document.getElementById('modalAction').value = type === 'message' ? 'reply_message' : 'reply_review';
    document.getElementById('modalId').value = id;
    document.getElementById('modalTitle').textContent = type === 'message' ? 'Reply to Contact Message' : 'Respond to Review';
    document.getElementById('originalText').textContent = text;
    document.getElementById('replyModal').style.display = 'flex';
}
function closeModal() {
    document.getElementById('replyModal').style.display = 'none';
}
</script>

<?php include 'includes/footer.php'; ?>

