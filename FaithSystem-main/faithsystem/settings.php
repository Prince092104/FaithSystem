<?php
$page_title = "Settings";
include('config/config.php');
include('includes/header.php');

$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // This is a placeholder for settings management
    // You can add more settings functionality here
    $message = "Settings updated successfully!";
    $message_type = "success";
}

// Get membership types
$membership_types = $conn->query("SELECT * FROM membership_types");

?>

<div class="page-header">
    <h1><i class="fas fa-cog"></i> System Settings</h1>
    <p>Manage club and membership settings</p>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $message_type; ?>">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<!-- Membership Types Management -->
<div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); margin-bottom: 2rem;">
    <h2 style="color: #667eea; margin-bottom: 1.5rem;"><i class="fas fa-crown"></i> Membership Types</h2>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Type Name</th>
                    <th>Description</th>
                    <th>Annual Fee</th>
                    <th>Benefits</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($type = $membership_types->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $type['type_name']; ?></td>
                        <td><?php echo $type['description']; ?></td>
                        <td>₱<?php echo number_format($type['annual_fee'], 2); ?></td>
                        <td><?php echo $type['benefits']; ?></td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-warning action-btn" onclick="editMembershipType(<?php echo $type['id']; ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Database Statistics -->
<div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
    <h2 style="color: #667eea; margin-bottom: 1.5rem;"><i class="fas fa-database"></i> Database Statistics</h2>

    <div class="dashboard-grid">
        <div class="dashboard-card">
            <i class="fas fa-users"></i>
            <h3>Total Members</h3>
            <div class="stat"><?php echo $conn->query("SELECT COUNT(*) as count FROM members")->fetch_assoc()['count']; ?></div>
        </div>

        <div class="dashboard-card">
            <i class="fas fa-file"></i>
            <h3>Total Files</h3>
            <div class="stat"><?php echo $conn->query("SELECT COUNT(*) as count FROM member_files")->fetch_assoc()['count']; ?></div>
        </div>

        <div class="dashboard-card">
            <i class="fas fa-history"></i>
            <h3>Status History Records</h3>
            <div class="stat"><?php echo $conn->query("SELECT COUNT(*) as count FROM status_history")->fetch_assoc()['count']; ?></div>
        </div>
    </div>
</div>

<script>
    function editMembershipType(id) {
        alert('Edit functionality can be implemented here. Membership Type ID: ' + id);
    }
</script>

<?php include('includes/footer.php'); ?>