<?php
$page_title = "View Member";
include('config/config.php');

// Get member ID
$member_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$member_id) {
    header("Location: member_list.php");
    exit();
}

// Fetch member details
$result = $conn->query("SELECT * FROM members WHERE id = $member_id");

if ($result->num_rows == 0) {
    header("Location: member_list.php");
    exit();
}

$member = $result->fetch_assoc();

// Fetch member files
$files_result = $conn->query("SELECT * FROM member_files WHERE member_id = $member_id ORDER BY upload_date DESC");

// Fetch status history
$history_result = $conn->query("SELECT * FROM status_history WHERE member_id = $member_id ORDER BY changed_date DESC");

include('includes/header.php');
?>

<div class="page-header">
    <h1><i class="fas fa-user"></i> Member Details</h1>
    <p><?php echo $member['first_name'] . ' ' . $member['last_name']; ?></p>
</div>

<div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); margin-bottom: 2rem;">

    <!-- Member Information -->
    <div style="margin-bottom: 2rem;">
        <h2 style="color: #667eea; margin-bottom: 1rem;">Personal Information</h2>
        <div class="form-row" style="margin-bottom: 1rem;">
            <div>
                <strong>First Name:</strong>
                <p><?php echo $member['first_name']; ?></p>
            </div>
            <div>
                <strong>Last Name:</strong>
                <p><?php echo $member['last_name']; ?></p>
            </div>
        </div>

        <div class="form-row" style="margin-bottom: 1rem;">
            <div>
                <strong>Email:</strong>
                <p><a href="mailto:<?php echo $member['email']; ?>"><?php echo $member['email']; ?></a></p>
            </div>
            <div>
                <strong>Phone:</strong>
                <p><a href="tel:<?php echo $member['phone']; ?>"><?php echo $member['phone']; ?></a></p>
            </div>
        </div>

        <div class="form-row" style="margin-bottom: 1rem;">
            <div>
                <strong>Date of Birth:</strong>
                <p><?php echo date('M d, Y', strtotime($member['date_of_birth'])); ?></p>
            </div>
            <div>
                <strong>Age:</strong>
                <p><?php echo date_diff(date_create($member['date_of_birth']), date_create('now'))->y; ?> years</p>
            </div>
        </div>

        <div class="form-row" style="margin-bottom: 1rem;">
            <div>
                <strong>Address:</strong>
                <p><?php echo $member['address'] ?: 'N/A'; ?></p>
            </div>
            <div>
                <strong>City:</strong>
                <p><?php echo $member['city'] ?: 'N/A'; ?></p>
            </div>
        </div>

        <div class="form-row" style="margin-bottom: 1rem;">
            <div>
                <strong>State:</strong>
                <p><?php echo $member['state'] ?: 'N/A'; ?></p>
            </div>
            <div>
                <strong>Zipcode:</strong>
                <p><?php echo $member['zipcode'] ?: 'N/A'; ?></p>
            </div>
        </div>
    </div>

    <!-- Membership Information -->
    <div style="margin-bottom: 2rem; border-top: 1px solid #ddd; padding-top: 2rem;">
        <h2 style="color: #667eea; margin-bottom: 1rem;">Membership Information</h2>
        <div class="form-row" style="margin-bottom: 1rem;">
            <div>
                <strong>Membership Type:</strong>
                <p><?php echo $member['membership_type']; ?></p>
            </div>
            <div>
                <strong>Membership Fee:</strong>
                <p>₱<?php echo number_format($member['membership_fee'], 2); ?></p>
            </div>
        </div>

        <div class="form-row" style="margin-bottom: 1rem;">
            <div>
                <strong>Join Date:</strong>
                <p><?php echo date('M d, Y', strtotime($member['join_date'])); ?></p>
            </div>
            <div>
                <strong>Current Status:</strong>
                <p><span class="badge badge-<?php echo strtolower($member['status']); ?>">
                        <?php echo $member['status']; ?>
                    </span></p>
            </div>
        </div>
    </div>

    <!-- Member Files -->
    <div style="margin-bottom: 2rem; border-top: 1px solid #ddd; padding-top: 2rem;">
        <h2 style="color: #667eea; margin-bottom: 1rem;">Member Files</h2>
        <?php if ($files_result->num_rows > 0): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>File Name</th>
                            <th>File Type</th>
                            <th>File Size</th>
                            <th>Upload Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($file = $files_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $file['file_name']; ?></td>
                                <td><?php echo $file['file_type']; ?></td>
                                <td><?php echo round($file['file_size'] / 1024, 2) . ' KB'; ?></td>
                                <td><?php echo date('M d, Y h:i A', strtotime($file['upload_date'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No files uploaded for this member.</p>
        <?php endif; ?>
    </div>

    <!-- Status History -->
    <div style="border-top: 1px solid #ddd; padding-top: 2rem;">
        <h2 style="color: #667eea; margin-bottom: 1rem;">Status History</h2>
        <?php if ($history_result->num_rows > 0): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Old Status</th>
                            <th>New Status</th>
                            <th>Reason</th>
                            <th>Changed Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($history = $history_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $history['old_status'] ?: 'N/A'; ?></td>
                                <td><span class="badge badge-<?php echo strtolower($history['new_status']); ?>">
                                        <?php echo $history['new_status']; ?>
                                    </span></td>
                                <td><?php echo $history['change_reason']; ?></td>
                                <td><?php echo date('M d, Y h:i A', strtotime($history['changed_date'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No status history available.</p>
        <?php endif; ?>
    </div>

    <!-- Action Buttons -->
    <div style="margin-top: 2rem; border-top: 1px solid #ddd; padding-top: 2rem;">
        <div class="action-buttons">
            <a href="edit_member.php?id=<?php echo $member['id']; ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Member
            </a>
            <a href="delete_member.php?id=<?php echo $member['id']; ?>" class="btn btn-danger"
                onclick="return confirm('Are you sure you want to delete this member?');">
                <i class="fas fa-trash"></i> Delete Member
            </a>
            <a href="member_list.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

</div>

<?php include('includes/footer.php'); ?>