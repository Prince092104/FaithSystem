<?php
$page_title = "Member List";
include('config/config.php');
include('includes/header.php');

// Search and filter parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$membership_filter = isset($_GET['membership_type']) ? $_GET['membership_type'] : '';

// Build query
$where = "1=1";
if ($search) {
    $search = $conn->real_escape_string($search);
    $where .= " AND (first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR email LIKE '%$search%' OR phone LIKE '%$search%')";
}
if ($status_filter) {
    $status_filter = $conn->real_escape_string($status_filter);
    $where .= " AND status = '$status_filter'";
}
if ($membership_filter) {
    $membership_filter = $conn->real_escape_string($membership_filter);
    $where .= " AND membership_type = '$membership_filter'";
}

// Get total count
$count_result = $conn->query("SELECT COUNT(*) as count FROM members WHERE $where");
$total_members = $count_result->fetch_assoc()['count'];

// Pagination
$per_page = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $per_page;
$total_pages = ceil($total_members / $per_page);

// Fetch members
$sql = "SELECT * FROM members WHERE $where ORDER BY date_created DESC LIMIT $offset, $per_page";
$result = $conn->query($sql);

?>

<div class="page-header">
    <h1><i class="fas fa-list"></i> Member List</h1>
    <p>Total Members: <strong><?php echo $total_members; ?></strong></p>
</div>

<!-- Search and Filter -->
<div class="search-filter">
    <form method="GET" action="" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end; width: 100%;">
        <div style="flex: 1; min-width: 200px;">
            <label for="search" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Search</label>
            <input type="text" id="search" name="search" placeholder="Name, Email, or Phone"
                value="<?php echo htmlspecialchars($search); ?>">
        </div>

        <div style="flex: 1; min-width: 150px;">
            <label for="status" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Status</label>
            <select id="status" name="status">
                <option value="">All Status</option>
                <option value="Active" <?php echo (isset($_GET['status']) && $_GET['status'] === 'Active') ? 'selected' : ''; ?>>Active</option>
                <option value="Inactive" <?php echo (isset($_GET['status']) && $_GET['status'] === 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                <option value="Suspended" <?php echo (isset($_GET['status']) && $_GET['status'] === 'Suspended') ? 'selected' : ''; ?>>Suspended</option>
                <option value="Expired" <?php echo (isset($_GET['status']) && $_GET['status'] === 'Expired') ? 'selected' : ''; ?>>Expired</option>
            </select>
        </div>

        <div style="flex: 1; min-width: 150px;">
            <label for="membership_type" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Membership Type</label>
            <select id="membership_type" name="membership_type">
                <option value="">All Types</option>
                <option value="Regular" <?php echo (isset($_GET['membership_type']) && $_GET['membership_type'] === 'Regular') ? 'selected' : ''; ?>>Regular</option>
                <option value="Premium" <?php echo (isset($_GET['membership_type']) && $_GET['membership_type'] === 'Premium') ? 'selected' : ''; ?>>Premium</option>
                <option value="Student" <?php echo (isset($_GET['membership_type']) && $_GET['membership_type'] === 'Student') ? 'selected' : ''; ?>>Student</option>
                <option value="Senior" <?php echo (isset($_GET['membership_type']) && $_GET['membership_type'] === 'Senior') ? 'selected' : ''; ?>>Senior</option>
            </select>
        </div>

        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Filter
            </button>
            <a href="member_list.php" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Reset
            </a>
        </div>
    </form>
</div>

<!-- Members Table -->
<?php if ($result->num_rows > 0): ?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Join Date</th>
                    <th>Membership Type</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($member = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $member['id']; ?></td>
                        <td><?php echo $member['first_name'] . ' ' . $member['last_name']; ?></td>
                        <td><?php echo $member['email']; ?></td>
                        <td><?php echo $member['phone']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($member['join_date'])); ?></td>
                        <td><?php echo $member['membership_type']; ?></td>
                        <td>
                            <span class="badge badge-<?php echo strtolower($member['status']); ?>">
                                <?php echo $member['status']; ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="view_member.php?id=<?php echo $member['id']; ?>" class="btn btn-info action-btn">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit_member.php?id=<?php echo $member['id']; ?>" class="btn btn-warning action-btn">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="delete_member.php?id=<?php echo $member['id']; ?>" class="btn btn-danger action-btn"
                                    onclick="return confirm('Are you sure you want to delete this member?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div style="text-align: center; margin-top: 2rem; padding: 1.5rem; background: white; border-radius: 10px;">
            <?php
            // Build query string with proper URL encoding
            $params = array();
            if (!empty($search)) {
                $params[] = 'search=' . urlencode($search);
            }
            if (!empty($status_filter)) {
                $params[] = 'status=' . urlencode($status_filter);
            }
            if (!empty($membership_filter)) {
                $params[] = 'membership_type=' . urlencode($membership_filter);
            }
            $query_string = implode('&', $params);
            if (!empty($query_string)) {
                $query_string = '&' . $query_string;
            }

            if ($page > 1) {
                echo '<a href="member_list.php?page=1' . $query_string . '" class="btn btn-secondary">First</a> ';
                echo '<a href="member_list.php?page=' . ($page - 1) . $query_string . '" class="btn btn-secondary">Previous</a> ';
            }

            echo "Page $page of $total_pages ";

            if ($page < $total_pages) {
                echo '<a href="member_list.php?page=' . ($page + 1) . $query_string . '" class="btn btn-secondary">Next</a> ';
                echo '<a href="member_list.php?page=' . $total_pages . $query_string . '" class="btn btn-secondary">Last</a>';
            }
            ?>
        </div>
    <?php endif; ?>
<?php else: ?>
    <div style="background: white; padding: 2rem; border-radius: 10px; text-align: center;">
        <p>No members found. <a href="register_member.php">Add a new member</a></p>
    </div>
<?php endif; ?>

<?php include('includes/footer.php'); ?>