<?php
$page_title = "Member Reports";
include('config/config.php');
include('includes/header.php');

$report_type = isset($_GET['type']) ? $_GET['type'] : 'summary';
$export_format = isset($_GET['export']) ? $_GET['export'] : '';

// Generate CSV export
if ($export_format == 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="members_report.csv"');

    $output = fopen('php://output', 'w');

    if ($report_type == 'summary') {
        fputcsv($output, array('Total Members', 'Active Members', 'Inactive Members', 'Suspended Members', 'Expired Members'));

        $total = $conn->query("SELECT COUNT(*) as count FROM members")->fetch_assoc()['count'];
        $active = $conn->query("SELECT COUNT(*) as count FROM members WHERE status = 'Active'")->fetch_assoc()['count'];
        $inactive = $conn->query("SELECT COUNT(*) as count FROM members WHERE status = 'Inactive'")->fetch_assoc()['count'];
        $suspended = $conn->query("SELECT COUNT(*) as count FROM members WHERE status = 'Suspended'")->fetch_assoc()['count'];
        $expired = $conn->query("SELECT COUNT(*) as count FROM members WHERE status = 'Expired'")->fetch_assoc()['count'];

        fputcsv($output, array($total, $active, $inactive, $suspended, $expired));
    } else {
        fputcsv($output, array('ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Join Date', 'Membership Type', 'Status', 'Fee'));

        $result = $conn->query("SELECT * FROM members ORDER BY date_created DESC");
        while ($member = $result->fetch_assoc()) {
            fputcsv($output, array(
                $member['id'],
                $member['first_name'],
                $member['last_name'],
                $member['email'],
                $member['phone'],
                $member['join_date'],
                $member['membership_type'],
                $member['status'],
                $member['membership_fee']
            ));
        }
    }

    fclose($output);
    exit();
}

// Generate PDF export
if ($export_format == 'pdf') {
    ob_start();
?>
    <html>

    <head>
        <title>Members Report</title>
        <style>
            body {
                font-family: Arial, sans-serif;
            }

            h1 {
                text-align: center;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            th,
            td {
                border: 1px solid #000;
                padding: 8px;
                text-align: left;
            }

            th {
                background-color: #667eea;
                color: white;
            }

            .summary {
                margin-bottom: 20px;
            }
        </style>
    </head>

    <body>
        <h1>Club Membership Manager - Members Report</h1>
        <p style="text-align: center;">Generated on <?php echo date('M d, Y h:i A'); ?></p>

        <?php if ($report_type == 'summary'): ?>
            <div class="summary">
                <h2>Summary Statistics</h2>
                <table>
                    <tr>
                        <td><strong>Total Members</strong></td>
                        <td><?php echo $conn->query("SELECT COUNT(*) as count FROM members")->fetch_assoc()['count']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Active Members</strong></td>
                        <td><?php echo $conn->query("SELECT COUNT(*) as count FROM members WHERE status = 'Active'")->fetch_assoc()['count']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Inactive Members</strong></td>
                        <td><?php echo $conn->query("SELECT COUNT(*) as count FROM members WHERE status = 'Inactive'")->fetch_assoc()['count']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Suspended Members</strong></td>
                        <td><?php echo $conn->query("SELECT COUNT(*) as count FROM members WHERE status = 'Suspended'")->fetch_assoc()['count']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Expired Members</strong></td>
                        <td><?php echo $conn->query("SELECT COUNT(*) as count FROM members WHERE status = 'Expired'")->fetch_assoc()['count']; ?></td>
                    </tr>
                </table>
            </div>

            <h2>Members by Membership Type</h2>
            <table>
                <thead>
                    <tr>
                        <th>Membership Type</th>
                        <th>Count</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $types = $conn->query("SELECT membership_type, COUNT(*) as count FROM members GROUP BY membership_type");
                    while ($type = $types->fetch_assoc()) {
                        echo "<tr><td>" . $type['membership_type'] . "</td><td>" . $type['count'] . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        <?php else: ?>
            <h2>Detailed Member List</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Join Date</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Fee</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM members ORDER BY date_created DESC");
                    while ($member = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $member['id'] . "</td>";
                        echo "<td>" . $member['first_name'] . " " . $member['last_name'] . "</td>";
                        echo "<td>" . $member['email'] . "</td>";
                        echo "<td>" . $member['phone'] . "</td>";
                        echo "<td>" . date('M d, Y', strtotime($member['join_date'])) . "</td>";
                        echo "<td>" . $member['membership_type'] . "</td>";
                        echo "<td>" . $member['status'] . "</td>";
                        echo "<td>$" . number_format($member['membership_fee'], 2) . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        <?php endif; ?>
    </body>

    </html>
<?php
    exit();
}

// Get statistics
$total_members = $conn->query("SELECT COUNT(*) as count FROM members")->fetch_assoc()['count'];
$active_members = $conn->query("SELECT COUNT(*) as count FROM members WHERE status = 'Active'")->fetch_assoc()['count'];
$inactive_members = $conn->query("SELECT COUNT(*) as count FROM members WHERE status = 'Inactive'")->fetch_assoc()['count'];
$suspended_members = $conn->query("SELECT COUNT(*) as count FROM members WHERE status = 'Suspended'")->fetch_assoc()['count'];
$expired_members = $conn->query("SELECT COUNT(*) as count FROM members WHERE status = 'Expired'")->fetch_assoc()['count'];
$total_revenue = $conn->query("SELECT SUM(membership_fee) as total FROM members WHERE status = 'Active'")->fetch_assoc()['total'];

// Get membership type breakdown
$membership_types = $conn->query("SELECT membership_type, COUNT(*) as count FROM members GROUP BY membership_type");

// Get members by status
$status_breakdown = $conn->query("SELECT status, COUNT(*) as count FROM members GROUP BY status");

?>

<div class="page-header">
    <h1><i class="fas fa-chart-bar"></i> Member Reports</h1>
    <p>Generate and view member statistics and reports</p>
</div>

<!-- Report Type Selection -->
<div class="search-filter">
    <a href="member_reports.php?type=summary" class="btn <?php echo $report_type == 'summary' ? 'btn-primary' : 'btn-secondary'; ?>">
        <i class="fas fa-chart-pie"></i> Summary Report
    </a>
    <a href="member_reports.php?type=detailed" class="btn <?php echo $report_type == 'detailed' ? 'btn-primary' : 'btn-secondary'; ?>">
        <i class="fas fa-list"></i> Detailed List
    </a>
</div>

<?php if ($report_type == 'summary'): ?>
    <!-- Summary Report -->
    <div class="dashboard-grid" style="margin-bottom: 2rem;">
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
            <i class="fas fa-ban"></i>
            <h3>Suspended Members</h3>
            <div class="stat"><?php echo $suspended_members; ?></div>
        </div>

        <div class="dashboard-card">
            <i class="fas fa-calendar-times"></i>
            <h3>Expired Members</h3>
            <div class="stat"><?php echo $expired_members; ?></div>
        </div>

        <div class="dashboard-card">
            <i class="fas fa-dollar-sign"></i>
            <h3>Total Revenue</h3>
            <div class="stat">$<?php echo number_format($total_revenue, 2); ?></div>
        </div>
    </div>

    <!-- Membership Type Breakdown -->
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); margin-bottom: 2rem;">
        <h2 style="color: #667eea; margin-bottom: 1.5rem;">Members by Membership Type</h2>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Membership Type</th>
                        <th>Count</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $membership_types = $conn->query("SELECT membership_type, COUNT(*) as count FROM members GROUP BY membership_type");
                    while ($type = $membership_types->fetch_assoc()):
                        $percentage = ($total_members > 0) ? ($type['count'] / $total_members * 100) : 0;
                    ?>
                        <tr>
                            <td><?php echo $type['membership_type']; ?></td>
                            <td><?php echo $type['count']; ?></td>
                            <td><?php echo number_format($percentage, 2); ?>%</td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Status Breakdown -->
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
        <h2 style="color: #667eea; margin-bottom: 1.5rem;">Members by Status</h2>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Count</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $status_breakdown = $conn->query("SELECT status, COUNT(*) as count FROM members GROUP BY status");
                    while ($status = $status_breakdown->fetch_assoc()):
                        $percentage = ($total_members > 0) ? ($status['count'] / $total_members * 100) : 0;
                    ?>
                        <tr>
                            <td><span class="badge badge-<?php echo strtolower($status['status']); ?>">
                                    <?php echo $status['status']; ?>
                                </span></td>
                            <td><?php echo $status['count']; ?></td>
                            <td><?php echo number_format($percentage, 2); ?>%</td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php else: ?>
    <!-- Detailed Member List Report -->
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
        <h2 style="color: #667eea; margin-bottom: 1.5rem;">All Members</h2>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Join Date</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Fee</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM members ORDER BY date_created DESC");
                    if ($result->num_rows > 0) {
                        while ($member = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $member['id'] . "</td>";
                            echo "<td>" . $member['first_name'] . " " . $member['last_name'] . "</td>";
                            echo "<td>" . $member['email'] . "</td>";
                            echo "<td>" . $member['phone'] . "</td>";
                            echo "<td>" . date('M d, Y', strtotime($member['join_date'])) . "</td>";
                            echo "<td>" . $member['membership_type'] . "</td>";
                            echo "<td><span class='badge badge-" . strtolower($member['status']) . "'>" . $member['status'] . "</span></td>";
                            echo "<td>$" . number_format($member['membership_fee'], 2) . "</td>";
                            echo "</tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

<?php endif; ?>

<!-- Export Options -->
<div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); margin-top: 2rem;">
    <h3 style="color: #667eea; margin-bottom: 1rem;">Export Report</h3>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="member_reports.php?type=<?php echo $report_type; ?>&export=csv" class="btn btn-success">
            <i class="fas fa-file-csv"></i> Export to CSV
        </a>
        <a href="member_reports.php?type=<?php echo $report_type; ?>&export=pdf" class="btn btn-danger" target="_blank">
            <i class="fas fa-file-pdf"></i> Export to PDF
        </a>
        <button onclick="window.print()" class="btn btn-info">
            <i class="fas fa-print"></i> Print Report
        </button>
    </div>
</div>

<?php include('includes/footer.php'); ?>