<?php

/**
 * System Setup Verification Script
 * Run this to verify your Club Membership Manager installation
 */

$page_title = "Setup Verification";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Club Membership Manager</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 2rem;
            min-height: 100vh;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        h1 {
            color: #667eea;
            text-align: center;
            margin-bottom: 2rem;
        }

        .check-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            background: #f5f5f5;
        }

        .check-icon {
            font-size: 1.5rem;
            margin-right: 1rem;
            width: 30px;
            text-align: center;
        }

        .check-content {
            flex: 1;
        }

        .check-content h3 {
            margin: 0 0 0.5rem 0;
            color: #333;
        }

        .check-content p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }

        .success {
            background: #d4edda;
            border-left: 4px solid #28a745;
        }

        .success .check-icon {
            color: #28a745;
        }

        .error {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
        }

        .error .check-icon {
            color: #dc3545;
        }

        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
        }

        .warning .check-icon {
            color: #ffc107;
        }

        .summary {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            padding: 1.5rem;
            border-radius: 5px;
            margin-top: 2rem;
        }

        .summary h2 {
            color: #667eea;
            margin-top: 0;
        }

        .action-buttons {
            text-align: center;
            margin-top: 2rem;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            transition: 0.3s;
        }

        .btn:hover {
            background: #5568d3;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🔧 System Setup Verification</h1>

        <?php
        $checks = array(
            'passed' => 0,
            'failed' => 0,
            'warnings' => 0
        );

        // Check PHP Version
        echo '<div class="check-item ';
        if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
            echo 'success';
            $checks['passed']++;
        } else {
            echo 'error';
            $checks['failed']++;
        }
        echo '">';
        echo version_compare(PHP_VERSION, '7.4.0', '>=') ? '<div class="check-icon">✓</div>' : '<div class="check-icon">✗</div>';
        echo '<div class="check-content">';
        echo '<h3>PHP Version</h3>';
        echo '<p>' . PHP_VERSION . ' (Required: 7.4+)</p>';
        echo '</div></div>';

        // Check MySQLi Extension
        echo '<div class="check-item ';
        if (extension_loaded('mysqli')) {
            echo 'success';
            $checks['passed']++;
        } else {
            echo 'error';
            $checks['failed']++;
        }
        echo '">';
        echo extension_loaded('mysqli') ? '<div class="check-icon">✓</div>' : '<div class="check-icon">✗</div>';
        echo '<div class="check-content">';
        echo '<h3>MySQLi Extension</h3>';
        echo '<p>' . (extension_loaded('mysqli') ? 'Installed' : 'Not installed') . '</p>';
        echo '</div></div>';

        // Check Database Connection
        echo '<div class="check-item ';
        $db_ok = false;
        try {
            $conn = new mysqli('localhost', 'root', '');
            if ($conn->connect_error) {
                throw new Exception($conn->connect_error);
            }
            $db_ok = true;
            echo 'success';
            $checks['passed']++;
            $conn->close();
        } catch (Exception $e) {
            echo 'error';
            $checks['failed']++;
        }
        echo '">';
        echo $db_ok ? '<div class="check-icon">✓</div>' : '<div class="check-icon">✗</div>';
        echo '<div class="check-content">';
        echo '<h3>Database Connection</h3>';
        echo '<p>' . ($db_ok ? 'Connected to MySQL' : 'Failed to connect to MySQL') . '</p>';
        echo '</div></div>';

        // Check Database Exists
        echo '<div class="check-item ';
        $database_exists = false;
        if ($db_ok) {
            try {
                $conn = new mysqli('localhost', 'root', '', 'club_membership');
                if (!$conn->connect_error) {
                    $database_exists = true;
                    echo 'success';
                    $checks['passed']++;
                    $conn->close();
                } else {
                    echo 'error';
                    $checks['failed']++;
                }
            } catch (Exception $e) {
                echo 'warning';
                $checks['warnings']++;
            }
        } else {
            echo 'error';
            $checks['failed']++;
        }
        echo '">';
        echo $database_exists ? '<div class="check-icon">✓</div>' : '<div class="check-icon">!</div>';
        echo '<div class="check-content">';
        echo '<h3>Database club_membership</h3>';
        echo '<p>' . ($database_exists ? 'Database exists' : 'Database not found - Run database.sql') . '</p>';
        echo '</div></div>';

        // Check config.php
        echo '<div class="check-item ';
        if (file_exists('config/config.php')) {
            echo 'success';
            $checks['passed']++;
        } else {
            echo 'error';
            $checks['failed']++;
        }
        echo '">';
        echo file_exists('config/config.php') ? '<div class="check-icon">✓</div>' : '<div class="check-icon">✗</div>';
        echo '<div class="check-content">';
        echo '<h3>Configuration File</h3>';
        echo '<p>config/config.php - ' . (file_exists('config/config.php') ? 'Found' : 'Missing') . '</p>';
        echo '</div></div>';

        // Check CSS file
        echo '<div class="check-item ';
        if (file_exists('css/style.css')) {
            echo 'success';
            $checks['passed']++;
        } else {
            echo 'warning';
            $checks['warnings']++;
        }
        echo '">';
        echo file_exists('css/style.css') ? '<div class="check-icon">✓</div>' : '<div class="check-icon">!</div>';
        echo '<div class="check-content">';
        echo '<h3>Stylesheet</h3>';
        echo '<p>css/style.css - ' . (file_exists('css/style.css') ? 'Found' : 'Missing') . '</p>';
        echo '</div></div>';

        // Check JavaScript file
        echo '<div class="check-item ';
        if (file_exists('js/script.js')) {
            echo 'success';
            $checks['passed']++;
        } else {
            echo 'warning';
            $checks['warnings']++;
        }
        echo '">';
        echo file_exists('js/script.js') ? '<div class="check-icon">✓</div>' : '<div class="check-icon">!</div>';
        echo '<div class="check-content">';
        echo '<h3>JavaScript File</h3>';
        echo '<p>js/script.js - ' . (file_exists('js/script.js') ? 'Found' : 'Missing') . '</p>';
        echo '</div></div>';

        // Check Tables
        if ($database_exists) {
            try {
                $conn = new mysqli('localhost', 'root', '', 'club_membership');
                $result = $conn->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = 'club_membership'");
                $table_count = $result->fetch_assoc()['count'];

                echo '<div class="check-item ';
                if ($table_count >= 4) {
                    echo 'success';
                    $checks['passed']++;
                } else {
                    echo 'warning';
                    $checks['warnings']++;
                }
                echo '">';
                echo ($table_count >= 4) ? '<div class="check-icon">✓</div>' : '<div class="check-icon">!</div>';
                echo '<div class="check-content">';
                echo '<h3>Database Tables</h3>';
                echo '<p>' . $table_count . ' tables found (Expected: 5)</p>';
                echo '</div></div>';

                $conn->close();
            } catch (Exception $e) {
                echo '<div class="check-item error">';
                echo '<div class="check-icon">✗</div>';
                echo '<div class="check-content">';
                echo '<h3>Database Tables</h3>';
                echo '<p>Error checking tables</p>';
                echo '</div></div>';
                $checks['failed']++;
            }
        }
        ?>

        <div class="summary">
            <h2>Summary</h2>
            <p><strong>Passed:</strong> <span style="color: #28a745;"><?php echo $checks['passed']; ?> checks</span></p>
            <p><strong>Failed:</strong> <span style="color: #dc3545;"><?php echo $checks['failed']; ?> checks</span></p>
            <p><strong>Warnings:</strong> <span style="color: #ffc107;"><?php echo $checks['warnings']; ?> warnings</span></p>

            <?php if ($checks['failed'] == 0 && $checks['warnings'] == 0): ?>
                <p style="color: #28a745; font-size: 1.2rem; margin-top: 1rem;">
                    <strong>✓ System is ready to use!</strong>
                </p>
            <?php elseif ($checks['failed'] > 0): ?>
                <p style="color: #dc3545; font-size: 1.2rem; margin-top: 1rem;">
                    <strong>✗ Please fix the errors above before using the system</strong>
                </p>
                <ul style="margin-top: 1rem; text-align: left;">
                    <li>Import database.sql into MySQL</li>
                    <li>Verify database connection settings in config/config.php</li>
                    <li>Ensure all files are uploaded to the correct directory</li>
                </ul>
            <?php else: ?>
                <p style="color: #ffc107; font-size: 1.2rem; margin-top: 1rem;">
                    <strong>⚠ System works but check warnings above</strong>
                </p>
            <?php endif; ?>
        </div>

        <div class="action-buttons">
            <?php if ($checks['failed'] == 0 && $checks['warnings'] == 0): ?>
                <a href="index.php" class="btn">Go to Dashboard →</a>
            <?php else: ?>
                <button class="btn" onclick="location.reload()">Refresh Checks</button>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>