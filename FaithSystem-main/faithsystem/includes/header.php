<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - Club Membership Manager' : 'Club Membership Manager'; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <nav class="navbar">
        <div class="navbar-container">
            <div class="navbar-brand">
                <i class="fas fa-users"></i> Club Membership Manager
            </div>
            <ul class="navbar-menu">
                <li><a href="index.php" class="nav-link">Dashboard</a></li>
                <li><a href="member_list.php" class="nav-link">Members</a></li>
                <li><a href="register_member.php" class="nav-link">Register Member</a></li>
                <li><a href="member_reports.php" class="nav-link">Reports</a></li>
                <li><a href="settings.php" class="nav-link">Settings</a></li>
            </ul>
        </div>
    </nav>
    <div class="container">