<?php
$page_title = "Register Member";
include('config/config.php');

$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $date_of_birth = $conn->real_escape_string($_POST['date_of_birth']);
    $join_date = $conn->real_escape_string($_POST['join_date']);
    $address = $conn->real_escape_string($_POST['address']);
    $city = $conn->real_escape_string($_POST['city']);
    $state = $conn->real_escape_string($_POST['state']);
    $zipcode = $conn->real_escape_string($_POST['zipcode']);
    $membership_type = $conn->real_escape_string($_POST['membership_type']);

    // Check if email already exists
    $email_check = $conn->query("SELECT * FROM members WHERE email = '$email'");

    if ($email_check->num_rows > 0) {
        $message = "Email already registered!";
        $message_type = "danger";
    } else {
        // Get membership fee
        $fee_result = $conn->query("SELECT annual_fee FROM membership_types WHERE type_name = '$membership_type'");
        $fee_row = $fee_result->fetch_assoc();
        $membership_fee = $fee_row['annual_fee'];

        // Insert member
        $sql = "INSERT INTO members (first_name, last_name, email, phone, date_of_birth, join_date, 
                address, city, state, zipcode, status, membership_type, membership_fee) 
                VALUES ('$first_name', '$last_name', '$email', '$phone', '$date_of_birth', '$join_date', 
                '$address', '$city', '$state', '$zipcode', 'Active', '$membership_type', '$membership_fee')";

        if ($conn->query($sql) === TRUE) {
            $member_id = $conn->insert_id;

            // Record status history
            $conn->query("INSERT INTO status_history (member_id, old_status, new_status, change_reason) 
                         VALUES ($member_id, NULL, 'Active', 'New member registration')");

            $message = "Member registered successfully!";
            $message_type = "success";

            // Clear form
            $_POST = array();
        } else {
            $message = "Error: " . $conn->error;
            $message_type = "danger";
        }
    }
}

include('includes/header.php');
?>

<div class="page-header">
    <h1><i class="fas fa-user-plus"></i> Register New Member</h1>
    <p>Add a new member to the club</p>
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
                    value="<?php echo isset($_POST['first_name']) ? $_POST['first_name'] : ''; ?>">
            </div>
            <div class="form-group">
                <label for="last_name">Last Name *</label>
                <input type="text" id="last_name" name="last_name" required
                    value="<?php echo isset($_POST['last_name']) ? $_POST['last_name'] : ''; ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required
                    value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
            </div>
            <div class="form-group">
                <label for="phone">Phone Number *</label>
                <input type="tel" id="phone" name="phone" required
                    value="<?php echo isset($_POST['phone']) ? $_POST['phone'] : ''; ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="date_of_birth">Date of Birth *</label>
                <input type="date" id="date_of_birth" name="date_of_birth" required
                    value="<?php echo isset($_POST['date_of_birth']) ? $_POST['date_of_birth'] : ''; ?>">
            </div>
            <div class="form-group">
                <label for="join_date">Join Date *</label>
                <input type="date" id="join_date" name="join_date" required
                    value="<?php echo isset($_POST['join_date']) ? $_POST['join_date'] : date('Y-m-d'); ?>">
            </div>
        </div>

        <div class="form-group form-row full">
            <label for="address">Address</label>
            <input type="text" id="address" name="address"
                value="<?php echo isset($_POST['address']) ? $_POST['address'] : ''; ?>">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city"
                    value="<?php echo isset($_POST['city']) ? $_POST['city'] : ''; ?>">
            </div>
            <div class="form-group">
                <label for="state">State</label>
                <input type="text" id="state" name="state"
                    value="<?php echo isset($_POST['state']) ? $_POST['state'] : ''; ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="zipcode">Zipcode</label>
            <input type="text" id="zipcode" name="zipcode"
                value="<?php echo isset($_POST['zipcode']) ? $_POST['zipcode'] : ''; ?>">
        </div>

        <div class="form-group">
            <label for="membership_type">Membership Type *</label>
            <select id="membership_type" name="membership_type" required>
                <option value="">-- Select Membership Type --</option>
                <option value="Regular" <?php echo (isset($_POST['membership_type']) && $_POST['membership_type'] == 'Regular') ? 'selected' : ''; ?>>Regular</option>
                <option value="Premium" <?php echo (isset($_POST['membership_type']) && $_POST['membership_type'] == 'Premium') ? 'selected' : ''; ?>>Premium</option>
                <option value="Student" <?php echo (isset($_POST['membership_type']) && $_POST['membership_type'] == 'Student') ? 'selected' : ''; ?>>Student</option>
                <option value="Senior" <?php echo (isset($_POST['membership_type']) && $_POST['membership_type'] == 'Senior') ? 'selected' : ''; ?>>Senior</option>
            </select>
        </div>

        <div style="display: flex; gap: 1rem;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Register Member
            </button>
            <a href="member_list.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </form>
</div>

<?php include('includes/footer.php'); ?>