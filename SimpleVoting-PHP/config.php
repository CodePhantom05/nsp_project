<?php
/**
 * Database Connection Configuration
 * Secure connection script for the eVoting system
 * Includes auto-creation of database if it doesn't exist
 */

// Database credentials
$hostname = "localhost";
$username = "root";
$password = "";
$database = "db_evoting";

// Error reporting for development (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // First connect without specifying database
    $conn = new mysqli($hostname, $username, $password);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Check if database exists
    $result = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$database'");
    
    if ($result->num_rows == 0) {
        // Database doesn't exist, create it
        $sql = "CREATE DATABASE $database";
        if ($conn->query($sql) === TRUE) {
            echo "Database created successfully.<br>";
            
            // Connect to the newly created database
            $conn->select_db($database);
            
            // Create tables
            createTables($conn);
        } else {
            throw new Exception("Error creating database: " . $conn->error);
        }
    } else {
        // Database exists, select it
        $conn->select_db($database);
    }
    
    // Set character set
    if (!$conn->set_charset("utf8mb4")) {
        throw new Exception("Error setting character set: " . $conn->error);
    }
    
} catch (Exception $e) {
    // For development, show the error
    die("Database connection error: " . $e->getMessage());
}

/**
 * Create necessary tables for the eVoting system
 * 
 * @param mysqli $conn The database connection object
 */
function createTables($conn) {
    // Create admin table
    $sql = "CREATE TABLE IF NOT EXISTS tbl_admin (
        admin_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        admin_username VARCHAR(50) NOT NULL UNIQUE,
        admin_password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Admin table created successfully.<br>";
        
        // Insert default admin user (username: admin, password: admin)
        // Note: In production, use a secure password
        $username = "admin";
        $password = password_hash("admin", PASSWORD_DEFAULT); // Using secure hashing
        
        $stmt = $conn->prepare("INSERT INTO tbl_admin (admin_username, admin_password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $password);
        
        if ($stmt->execute()) {
            echo "Default admin user created.<br>";
        } else {
            echo "Error creating default admin: " . $stmt->error . "<br>";
        }
        
        $stmt->close();
    } else {
        echo "Error creating admin table: " . $conn->error . "<br>";
    }
    
    // Create voters table
    $sql = "CREATE TABLE IF NOT EXISTS tbl_voters (
        voter_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        voter_name VARCHAR(100) NOT NULL,
        voter_email VARCHAR(100) NOT NULL UNIQUE,
        voter_password VARCHAR(255) NOT NULL,
        voter_status ENUM('active', 'inactive') DEFAULT 'active',
        voted TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Voters table created successfully.<br>";
    } else {
        echo "Error creating voters table: " . $conn->error . "<br>";
    }
    
    // Create candidates table
    $sql = "CREATE TABLE IF NOT EXISTS tbl_candidates (
        candidate_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        candidate_name VARCHAR(100) NOT NULL,
        candidate_position VARCHAR(100) NOT NULL,
        candidate_details TEXT,
        candidate_photo VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Candidates table created successfully.<br>";
    } else {
        echo "Error creating candidates table: " . $conn->error . "<br>";
    }
    
    // Create votes table
    $sql = "CREATE TABLE IF NOT EXISTS tbl_votes (
        vote_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        voter_id INT(11) NOT NULL,
        candidate_id INT(11) NOT NULL,
        position VARCHAR(100) NOT NULL,
        voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (voter_id) REFERENCES tbl_voters(voter_id),
        FOREIGN KEY (candidate_id) REFERENCES tbl_candidates(candidate_id)
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Votes table created successfully.<br>";
    } else {
        echo "Error creating votes table: " . $conn->error . "<br>";
    }
}

/**
 * User input sanitization function
 * 
 * @param string $data The data to sanitize
 * @param mysqli|null $conn The database connection object (optional)
 * @return string The sanitized data
 */
function test_input($data, $conn = null) {
    // Remove whitespace
    $data = trim($data);
    
    // Remove backslashes
    $data = stripslashes($data);
    
    // Convert special characters to HTML entities
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    
    // Escape special characters in a string for use in an SQL statement
    // Only if connection is provided
    if ($conn instanceof mysqli) {
        $data = mysqli_real_escape_string($conn, $data);
    }
    
    return $data;
}

/**
 * Validate token to prevent CSRF attacks
 * 
 * @param string $token The token from the form
 * @return bool Whether the token is valid
 */
function validate_token($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate a new CSRF token
 * 
 * @return string The new token
 */
function generate_token() {
    if (!isset($_SESSION)) {
        session_start();
    }
    
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    return $token;
}

// Echo success message after all setup is complete
echo "<div style='margin: 20px; padding: 10px; background-color: #dff0d8; border: 1px solid #d6e9c6; border-radius: 4px;'>";
echo "<h3 style='color: #3c763d;'>Database Connection Successful</h3>";
echo "<p>Your database is now set up and ready to use.</p>";
echo "<p><a href='index.html' style='color: #337ab7; text-decoration: none;'>Go to Homepage</a></p>";
echo "</div>";
?>