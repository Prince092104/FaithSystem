-- Create Database
CREATE DATABASE IF NOT EXISTS club_membership;

USE club_membership;

-- Create Members Table
CREATE TABLE IF NOT EXISTS members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15) NOT NULL,
    date_of_birth DATE NOT NULL,
    join_date DATE NOT NULL,
    address VARCHAR(255),
    city VARCHAR(50),
    state VARCHAR(50),
    zipcode VARCHAR(10),
    status ENUM(
        'Active',
        'Inactive',
        'Suspended',
        'Expired'
    ) DEFAULT 'Active',
    membership_type ENUM(
        'Regular',
        'Premium',
        'Student',
        'Senior'
    ) DEFAULT 'Regular',
    membership_fee DECIMAL(10, 2) DEFAULT 0.00,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

-- Create Member Files Table (for storing documents/files)
CREATE TABLE IF NOT EXISTS member_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(50),
    file_size INT,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members (id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

-- Create Member Status History Table
CREATE TABLE IF NOT EXISTS status_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    old_status VARCHAR(50),
    new_status VARCHAR(50),
    change_reason VARCHAR(255),
    changed_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members (id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

-- Create Membership Types Table
CREATE TABLE IF NOT EXISTS membership_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_name VARCHAR(50) UNIQUE NOT NULL,
    description VARCHAR(255),
    annual_fee DECIMAL(10, 2),
    benefits TEXT,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

-- Insert Sample Membership Types
INSERT INTO
    membership_types (
        type_name,
        description,
        annual_fee,
        benefits
    )
VALUES (
        'Regular',
        'Standard membership for all members',
        50.00,
        'Access to all club events and facilities'
    ),
    (
        'Premium',
        'Premium membership with extra benefits',
        100.00,
        'Priority access to events, private lounge access'
    ),
    (
        'Student',
        'Discounted membership for students',
        25.00,
        'Access to student events and workshops'
    ),
    (
        'Senior',
        'Special membership for senior members',
        30.00,
        'Senior member events and discounts'
    );

-- Create Admin Users Table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

-- Create Indexes for better performance
CREATE INDEX idx_member_status ON members (status);

CREATE INDEX idx_member_email ON members (email);

CREATE INDEX idx_member_join_date ON members (join_date);