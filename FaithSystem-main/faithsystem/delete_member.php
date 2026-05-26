<?php
include('config/config.php');

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

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['confirm_delete']) && $_POST['confirm_delete'] == 'yes') {
        // Delete member (cascading will handle files and history)
        $sql = "DELETE FROM members WHERE id = $member_id";

        if ($conn->query($sql) === TRUE) {
            header("Location: member_list.php?message=Member deleted successfully");
            exit();
        } else {
            $error = "Error deleting member: " . $conn->error;
        }
    } else {
        header("Location: view_member.php?id=$member_id");
        exit();
    }
}

$page_title = "Delete Member";
include('includes/header.php');
?>

<div class="page-header">
    <h1><i class="fas fa-trash"></i> Delete Member</h1>
    <p>Confirm Member Deletion</p>
</div>

<div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); max-width: 600px; margin: 0 auto;">

    <div class="alert alert-danger">
        <strong>Warning!</strong> This action cannot be undone.
    </div>

    <div style="margin-bottom: 2rem;">
        <h3>Member Information:</h3>
        <p><strong>Name:</strong> <?php echo $member['first_name'] . ' ' . $member['last_name']; ?></p>
        <p><strong>Email:</strong> <?php echo $member['email']; ?></p>
        <p><strong>Phone:</strong> <?php echo $member['phone']; ?></p>
        <p><strong>Join Date:</strong> <?php echo date('M d, Y', strtotime($member['join_date'])); ?></p>
        <p><strong>Status:</strong> <span class="badge badge-<?php echo strtolower($member['status']); ?>">
                <?php echo $member['status']; ?>
            </span></p>
    </div>

    <form method="POST" action="">
        <p style="margin-bottom: 2rem;">
            Are you absolutely sure you want to delete this member? This will also delete all associated files and status history.
        </p>

        <div style="display: flex; gap: 1rem;">
            <button type="submit" name="confirm_delete" value="yes" class="btn btn-danger">
                <i class="fas fa-check"></i> Yes, Delete Member
            </button>
            <button type="submit" name="confirm_delete" value="no" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </button>
        </div>
    </form>

</div>

<?php include('includes/footer.php'); ?>