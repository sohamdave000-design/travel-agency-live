<?php
if (!isAdmin()) {
    redirect('login.html');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Travel Agency</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-layout { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: #1e293b; color: white; padding: 2rem 0; flex-shrink: 0; }
        .sidebar-header { padding: 0 1.5rem 2rem; border-bottom: 1px solid #334155; margin-bottom: 1rem; }
        .sidebar-header h2 { color: #f8fafc; font-size: 1.25rem; }
        .sidebar-nav { list-style: none; }
        .sidebar-nav li a { display: block; padding: 1rem 1.5rem; color: #cbd5e1; text-decoration: none; transition: 0.3s; }
        .sidebar-nav li a:hover, .sidebar-nav li a.active { background: #334155; color: white; border-left: 4px solid var(--primary-color); }
        .admin-content { flex: 1; background: #f1f5f9; padding: 2rem; overflow-y: auto; }
        .admin-topbar { display: flex; justify-content: space-between; align-items: center; background: white; padding: 1rem 2rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        
        .stat-card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; flex-direction: column; }
        .stat-card-title { color: #64748b; font-size: 0.875rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.5rem; }
        .stat-card-value { color: #0f172a; font-size: 2rem; font-weight: 700; }
        
        .admin-table { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .admin-action-btn { padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem; text-decoration: none; display: inline-block; }
        .btn-edit { background: #e0f2fe; color: #0284c7; }
        .btn-delete { background: #fee2e2; color: #dc2626; }
        
        .form-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; }
        .form-modal-content { background: white; padding: 2rem; border-radius: 8px; width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto; }
        .close-modal { float: right; cursor: pointer; font-size: 1.5rem; line-height: 1; }
    </style>
</head>
<body>
    <div class="admin-layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>&#9992;&#65039; Admin Portal</h2>
                <a href="../index.html" style="display:inline-block; margin-top:0.75rem; font-size:0.8rem; color:#94a3b8; text-decoration:none; background:#334155; padding:0.35rem 0.9rem; border-radius:6px; transition:background 0.2s;" onmouseover="this.style.background='#475569'" onmouseout="this.style.background='#334155'">&#127968; View Live Site</a>
            </div>
            <ul class="sidebar-nav">
                <li><a href="index.html">&#128202; Dashboard</a></li>
                <li><a href="bookings.html">&#128203; Bookings</a></li>
                <li><a href="packages.html">&#127757; Packages</a></li>
                <li><a href="hotels.html">&#127970; Hotels</a></li>
                <li><a href="buses.html">&#128652; Buses</a></li>
                <li><a href="rentals.html">&#128663; Rentals</a></li>
                <li><a href="users.html">&#128101; Users</a></li>
                <li><a href="messages.html">&#128172; Messages &amp; Reviews</a></li>
            </ul>
        </aside>
        
        <div class="admin-content">
            <div class="admin-topbar">
                <h2>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></h2>
                <a href="logout.html" class="btn-danger">Logout</a>
            </div>

