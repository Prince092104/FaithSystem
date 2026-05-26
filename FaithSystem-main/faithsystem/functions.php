<?php

/**
 * Common Functions for Club Membership Manager
 * This file contains reusable functions for the system
 */

// Get all members
function getAllMembers($conn)
{
    return $conn->query("SELECT * FROM members ORDER BY date_created DESC");
}

// Get members by status
function getMembersByStatus($conn, $status)
{
    $status = $conn->real_escape_string($status);
    return $conn->query("SELECT * FROM members WHERE status = '$status' ORDER BY date_created DESC");
}

// Get members by membership type
function getMembersByType($conn, $type)
{
    $type = $conn->real_escape_string($type);
    return $conn->query("SELECT * FROM members WHERE membership_type = '$type' ORDER BY date_created DESC");
}

// Get member by ID
function getMemberById($conn, $id)
{
    $id = (int)$id;
    $result = $conn->query("SELECT * FROM members WHERE id = $id");
    return $result->fetch_assoc();
}

// Get member by email
function getMemberByEmail($conn, $email)
{
    $email = $conn->real_escape_string($email);
    $result = $conn->query("SELECT * FROM members WHERE email = '$email'");
    return $result->fetch_assoc();
}

// Count total members
function countMembers($conn)
{
    $result = $conn->query("SELECT COUNT(*) as count FROM members");
    return $result->fetch_assoc()['count'];
}

// Count active members
function countActiveMembers($conn)
{
    $result = $conn->query("SELECT COUNT(*) as count FROM members WHERE status = 'Active'");
    return $result->fetch_assoc()['count'];
}

// Get total revenue
function getTotalRevenue($conn)
{
    $result = $conn->query("SELECT SUM(membership_fee) as total FROM members WHERE status = 'Active'");
    return $result->fetch_assoc()['total'] ?: 0;
}

// Get members by date range
function getMembersByDateRange($conn, $start_date, $end_date)
{
    $start_date = $conn->real_escape_string($start_date);
    $end_date = $conn->real_escape_string($end_date);
    return $conn->query("SELECT * FROM members WHERE join_date BETWEEN '$start_date' AND '$end_date' ORDER BY join_date DESC");
}

// Update member status
function updateMemberStatus($conn, $member_id, $new_status, $reason = '')
{
    $member_id = (int)$member_id;
    $new_status = $conn->real_escape_string($new_status);
    $reason = $conn->real_escape_string($reason);

    $member = getMemberById($conn, $member_id);
    if (!$member) return false;

    // Update status
    $sql = "UPDATE members SET status = '$new_status' WHERE id = $member_id";
    if (!$conn->query($sql)) return false;

    // Record status change
    $old_status = $member['status'];
    $sql = "INSERT INTO status_history (member_id, old_status, new_status, change_reason) 
            VALUES ($member_id, '$old_status', '$new_status', '$reason')";

    return $conn->query($sql);
}

// Get status history for member
function getStatusHistory($conn, $member_id)
{
    $member_id = (int)$member_id;
    return $conn->query("SELECT * FROM status_history WHERE member_id = $member_id ORDER BY changed_date DESC");
}

// Get membership fee
function getMembershipFee($conn, $membership_type)
{
    $membership_type = $conn->real_escape_string($membership_type);
    $result = $conn->query("SELECT annual_fee FROM membership_types WHERE type_name = '$membership_type'");
    $row = $result->fetch_assoc();
    return $row ? $row['annual_fee'] : 0;
}

// Get all membership types
function getAllMembershipTypes($conn)
{
    return $conn->query("SELECT * FROM membership_types ORDER BY annual_fee ASC");
}

// Get member files
function getMemberFiles($conn, $member_id)
{
    $member_id = (int)$member_id;
    return $conn->query("SELECT * FROM member_files WHERE member_id = $member_id ORDER BY upload_date DESC");
}

// Add member file
function addMemberFile($conn, $member_id, $file_name, $file_path, $file_type, $file_size)
{
    $member_id = (int)$member_id;
    $file_name = $conn->real_escape_string($file_name);
    $file_path = $conn->real_escape_string($file_path);
    $file_type = $conn->real_escape_string($file_type);
    $file_size = (int)$file_size;

    $sql = "INSERT INTO member_files (member_id, file_name, file_path, file_type, file_size) 
            VALUES ($member_id, '$file_name', '$file_path', '$file_type', $file_size)";

    return $conn->query($sql);
}

// Get statistics
function getStatistics($conn)
{
    $stats = array();

    $stats['total_members'] = countMembers($conn);
    $stats['active_members'] = countActiveMembers($conn);
    $stats['inactive_members'] = $conn->query("SELECT COUNT(*) as count FROM members WHERE status = 'Inactive'")->fetch_assoc()['count'];
    $stats['suspended_members'] = $conn->query("SELECT COUNT(*) as count FROM members WHERE status = 'Suspended'")->fetch_assoc()['count'];
    $stats['expired_members'] = $conn->query("SELECT COUNT(*) as count FROM members WHERE status = 'Expired'")->fetch_assoc()['count'];
    $stats['total_revenue'] = getTotalRevenue($conn);

    return $stats;
}

// Search members
function searchMembers($conn, $search_term)
{
    $search_term = $conn->real_escape_string($search_term);
    return $conn->query("SELECT * FROM members WHERE 
        first_name LIKE '%$search_term%' OR 
        last_name LIKE '%$search_term%' OR 
        email LIKE '%$search_term%' OR 
        phone LIKE '%$search_term%' OR 
        address LIKE '%$search_term%' 
        ORDER BY date_created DESC");
}

// Get members by membership fee range
function getMembersByFeeRange($conn, $min_fee, $max_fee)
{
    $min_fee = (float)$min_fee;
    $max_fee = (float)$max_fee;
    return $conn->query("SELECT * FROM members WHERE membership_fee BETWEEN $min_fee AND $max_fee ORDER BY membership_fee DESC");
}

// Export members to array (for CSV/Excel)
function exportMembersToArray($conn, $filters = array())
{
    $where = "1=1";

    if (isset($filters['status'])) {
        $status = $conn->real_escape_string($filters['status']);
        $where .= " AND status = '$status'";
    }

    if (isset($filters['membership_type'])) {
        $type = $conn->real_escape_string($filters['membership_type']);
        $where .= " AND membership_type = '$type'";
    }

    $result = $conn->query("SELECT * FROM members WHERE $where ORDER BY date_created DESC");
    $members = array();

    while ($row = $result->fetch_assoc()) {
        $members[] = $row;
    }

    return $members;
}

// Calculate age
function calculateAge($birth_date)
{
    $birth_date = new DateTime($birth_date);
    $today = new DateTime();
    $age = $today->diff($birth_date);
    return $age->y;
}

// Format member data for display
function formatMemberData($member)
{
    $member['age'] = calculateAge($member['date_of_birth']);
    $member['join_date_formatted'] = date('M d, Y', strtotime($member['join_date']));
    $member['membership_fee_formatted'] = '₱' . number_format($member['membership_fee'], 2);
    return $member;
}

// Validate member email
function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Validate member phone
function isValidPhone($phone)
{
    $phone = preg_replace('/[^0-9]/', '', $phone);
    return strlen($phone) >= 10;
}

// Validate date format
function isValidDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}
