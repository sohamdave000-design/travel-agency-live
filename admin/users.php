<?php
require_once '../config/database.php';

// Handle user deletion
if (isset($_GET['delete'])) {
    if(!isAdmin()) redirect('login.html'); // extra check
    $delete_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$delete_id]);
    redirect('users.html?msg=deleted');
}

include 'includes/header.php';

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h1>User Management</h1>
</div>

<?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
<div class="alert alert-success">User deleted successfully.</div>
<?php endif; ?>

<div class="admin-table">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Joined Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone']); ?></td>
                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                    <td>
                        <a href="users.html?delete=<?php echo $user['id']; ?>" class="admin-action-btn btn-delete" onclick="return confirm('Are you sure you want to delete this user? All their bookings will also be deleted.')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($users)): ?>
                <tr><td colspan="6" class="text-center">No users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

