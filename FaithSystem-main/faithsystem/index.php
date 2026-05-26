<?php
$page_title = "Dashboard";
include('config/config.php');
include('includes/header.php');

// Get statistics
$total_members = $conn->query("SELECT COUNT(*) as count FROM members")->fetch_assoc()['count'];
$active_members = $conn->query("SELECT COUNT(*) as count FROM members WHERE status = 'Active'")->fetch_assoc()['count'];
$inactive_members = $conn->query("SELECT COUNT(*) as count FROM members WHERE status = 'Inactive'")->fetch_assoc()['count'];
$premium_members = $conn->query("SELECT COUNT(*) as count FROM members WHERE membership_type = 'Premium'")->fetch_assoc()['count'];

// Get recent members
$recent_members = $conn->query("SELECT * FROM members ORDER BY date_created DESC LIMIT 5");

?>

<div class="page-header">
    <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
    <p>Welcome to Club Membership Manager System</p>
</div>

<!-- Dashboard Statistics -->
<div class="dashboard-grid">
    <div class="dashboard-card">
        <i class="fas fa-users"></i>
        <h3>Total Members</h3>
        <div class="stat"><?php echo $total_members; ?></div>
    </div>

    <div class="dashboard-card">
        <i class="fas fa-check-circle"></i>
        <h3>Active Members</h3>
        <div class="stat"><?php echo $active_members; ?></div>
    </div>

    <div class="dashboard-card">
        <i class="fas fa-pause-circle"></i>
        <h3>Inactive Members</h3>
        <div class="stat"><?php echo $inactive_members; ?></div>
    </div>

    <div class="dashboard-card">
        <i class="fas fa-crown"></i>
        <h3>Premium Members</h3>
        <div class="stat"><?php echo $premium_members; ?></div>
    </div>
</div>

<!-- Recent Members -->
<div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
    <h2 style="margin-bottom: 1.5rem; color: #667eea;">
        <i class="fas fa-clock"></i> Recently Added Members
    </h2>

    <?php if ($recent_members->num_rows > 0): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Join Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($member = $recent_members->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $member['id']; ?></td>
                            <td><?php echo $member['first_name'] . ' ' . $member['last_name']; ?></td>
                            <td><?php echo $member['email']; ?></td>
                            <td><?php echo $member['phone']; ?></td>
                            <td><?php echo date('M d, Y', strtotime($member['join_date'])); ?></td>
                            <td>
                                <span class="badge badge-<?php echo strtolower($member['status']); ?>">
                                    <?php echo $member['status']; ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="view_member.php?id=<?php echo $member['id']; ?>" class="btn btn-info action-btn">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="edit_member.php?id=<?php echo $member['id']; ?>" class="btn btn-warning action-btn">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No members registered yet. <a href="register_member.php">Add a new member</a></p>
    <?php endif; ?>
</div>

<?php include('includes/footer.php'); ?>