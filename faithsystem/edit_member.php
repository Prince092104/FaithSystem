<?php
$page_title = "Edit Member";
include('config/config.php');

$member_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$message_type = '';

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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $date_of_birth = $conn->real_escape_string($_POST['date_of_birth']);
    $address = $conn->real_escape_string($_POST['address']);
    $city = $conn->real_escape_string($_POST['city']);
    $state = $conn->real_escape_string($_POST['state']);
    $zipcode = $conn->real_escape_string($_POST['zipcode']);
    $status = $conn->real_escape_string($_POST['status']);
    $membership_type = $conn->real_escape_string($_POST['membership_type']);

    // Check if status changed
    $status_changed = $status != $member['status'];

    // Update member
    $sql = "UPDATE members SET 
            first_name = '$first_name',
            last_name = '$last_name',
            phone = '$phone',
            date_of_birth = '$date_of_birth',
            address = '$address',
            city = '$city',
            state = '$state',
            zipcode = '$zipcode',
            status = '$status',
            membership_type = '$membership_type'
            WHERE id = $member_id";

    if ($conn->query($sql) === TRUE) {
        // Record status history if status changed
        if ($status_changed) {
            $reason = $_POST['status_reason'] ?? 'Status updated';
            $reason = $conn->real_escape_string($reason);
            $conn->query("INSERT INTO status_history (member_id, old_status, new_status, change_reason) 
                         VALUES ($member_id, '{$member['status']}', '$status', '$reason')");
        }

        $message = "Member updated successfully!";
        $message_type = "success";

        // Refresh member data
        $result = $conn->query("SELECT * FROM members WHERE id = $member_id");
        $member = $result->fetch_assoc();
    } else {
        $message = "Error: " . $conn->error;
        $message_type = "danger";
    }
}

include('includes/header.php');
?>

<div class="page-header">
    <h1><i class="fas fa-user-edit"></i> Edit Member</h1>
    <p><?php echo $member['first_name'] . ' ' . $member['last_name']; ?></p>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $message_type; ?>">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<div class="form-container">
    <form method="POST" action="">
        <div class="form-row">
            <div class="form-group">
                <label for="first_name">First Name *</label>
                <input type="text" id="first_name" name="first_name" required
                    value="<?php echo $member['first_name']; ?>">
            </div>
            <div class="form-group">
                <label for="last_name">Last Name *</label>
                <input type="text" id="last_name" name="last_name" required
                    value="<?php echo $member['last_name']; ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="email">Email (Read-Only)</label>
                <input type="email" id="email" name="email" readonly
                    value="<?php echo $member['email']; ?>">
                <small style="color: #666;">Email cannot be changed.</small>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number *</label>
                <input type="tel" id="phone" name="phone" required
                    value="<?php echo $member['phone']; ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="date_of_birth">Date of Birth *</label>
                <input type="date" id="date_of_birth" name="date_of_birth" required
                    value="<?php echo $member['date_of_birth']; ?>">
            </div>
            <div class="form-group">
                <label for="join_date">Join Date (Read-Only)</label>
                <input type="date" id="join_date" name="join_date" readonly
                    value="<?php echo $member['join_date']; ?>">
            </div>
        </div>

        <div class="form-group form-row full">
            <label for="address">Address</label>
            <input type="text" id="address" name="address"
                value="<?php echo $member['address']; ?>">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city"
                    value="<?php echo $member['city']; ?>">
            </div>
            <div class="form-group">
                <label for="state">State</label>
                <input type="text" id="state" name="state"
                    value="<?php echo $member['state']; ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="zipcode">Zipcode</label>
            <input type="text" id="zipcode" name="zipcode"
                value="<?php echo $member['zipcode']; ?>">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="membership_type">Membership Type *</label>
                <select id="membership_type" name="membership_type" required>
                    <option value="Regular" <?php echo $member['membership_type'] == 'Regular' ? 'selected' : ''; ?>>Regular</option>
                    <option value="Premium" <?php echo $member['membership_type'] == 'Premium' ? 'selected' : ''; ?>>Premium</option>
                    <option value="Student" <?php echo $member['membership_type'] == 'Student' ? 'selected' : ''; ?>>Student</option>
                    <option value="Senior" <?php echo $member['membership_type'] == 'Senior' ? 'selected' : ''; ?>>Senior</option>
                </select>
            </div>
            <div class="form-group">
                <label for="status">Member Status *</label>
                <select id="status" name="status" required>
                    <option value="Active" <?php echo $member['status'] == 'Active' ? 'selected' : ''; ?>>Active</option>
                    <option value="Inactive" <?php echo $member['status'] == 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                    <option value="Suspended" <?php echo $member['status'] == 'Suspended' ? 'selected' : ''; ?>>Suspended</option>
                    <option value="Expired" <?php echo $member['status'] == 'Expired' ? 'selected' : ''; ?>>Expired</option>
                </select>
            </div>
        </div>

        <div class="form-group form-row full">
            <label for="status_reason">Status Change Reason (if applicable)</label>
            <textarea id="status_reason" name="status_reason" rows="3" placeholder="Enter reason for status change if applicable"></textarea>
        </div>

        <div style="display: flex; gap: 1rem;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Member
            </button>
            <a href="view_member.php?id=<?php echo $member_id; ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </form>
</div>

<?php include('includes/footer.php'); ?>