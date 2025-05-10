<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SVS</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Oswald" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed" rel="stylesheet">

    <style>
      .headerFont{
        font-family: 'Ubuntu', sans-serif;
        font-size: 24px;
      }

      .subFont{
        font-family: 'Raleway', sans-serif;
        font-size: 14px;
      }
      
      .specialHead{
        font-family: 'Oswald', sans-serif;
      }

      .normalFont{
        font-family: 'Roboto Condensed', sans-serif;
      }
    </style>
  </head>
  <body>
	
	<div class="container">
  	<nav class="navbar navbar-default navbar-fixed-top navbar-inverse" role="navigation">
      <div class="container">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#example-nav-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <div class="navbar-header">
          <a href="index.html" class="navbar-brand headerFont text-lg">eVoting</a>
        </div>

        <div class="collapse navbar-collapse" id="example-nav-collapse">        
          <button type="submit" class="btn btn-success navbar-right navbar-btn"><span class="normalFont"><strong>Admin Panel</strong></span></button>
        </div>
      </div> <!-- end of container -->
    </nav>

    <div class="container" style="padding-top:150px;">
    	<div class="row">
    		<div class="col-sm-4"></div>
    		<div class="col-sm-4 text-center" style="border:2px solid gray;padding:50px;">

<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['admin_username'])) {
    // Redirect to login page if not logged in
    header("Location: index.html");
    exit();
}

// Database credentials
$hostname = "localhost";
$username = "root";
$password = "";
$database = "db_evoting";

// Initialize error variable
$error = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if fields are set and not empty
    if (empty($_POST['existingPassword']) || empty($_POST['newPassword'])) {
        $error = "All fields are required.";
    } else {
        // Input sanitization function
        function sanitize_input($data, $conn) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            $data = $conn->real_escape_string($data);
            return $data;
        }
        
        // Create mysqli connection
        $conn = new mysqli($hostname, $username, $password, $database);
        
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        // Get sanitized inputs
        $oldPassword = sanitize_input($_POST['existingPassword'], $conn);
        $newPassword = $_POST['newPassword']; // Don't escape the new password before hashing
        
        // Get current admin username from session
        $currentUser = $_SESSION['admin_username'];
        $currentUser = $conn->real_escape_string($currentUser);
        
        // Prepare and execute query to get current admin details
        $stmt = $conn->prepare("SELECT admin_password FROM tbl_admin WHERE admin_username = ?");
        $stmt->bind_param("s", $currentUser);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            
            // Check if old password is correct
            // For existing plain text passwords in DB:
            if (password_verify($oldPassword, $row['admin_password']) || $oldPassword === $row['admin_password']) {
                // Hash the new password
                $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                
                // Update password in database
                $updateStmt = $conn->prepare("UPDATE tbl_admin SET admin_password = ? WHERE admin_username = ?");
                $updateStmt->bind_param("ss", $hashedNewPassword, $currentUser);
                
                if ($updateStmt->execute()) {
                    // Password updated successfully
                    echo "<img src='images/success.png' width='70' height='70' alt='Success'>";
                    echo "<h3 class='text-info specialHead text-center'><strong>PASSWORD SUCCESSFULLY CHANGED</strong></h3>";
                    echo "<a href='cpanel.php' class='btn btn-primary'><span class='glyphicon glyphicon-ok'></span> <strong>Finish</strong></a>";
                } else {
                    // Error updating password
                    $error = "Error updating password: " . $conn->error;
                }
                $updateStmt->close();
            } else {
                $error = "Old password is incorrect.";
            }
        } else {
            $error = "User not found.";
        }
        
        $stmt->close();
        $conn->close();
    }
}

// Display error message if there is one
if (!empty($error)) {
    echo "<img src='images/error.png' width='70' height='70' alt='Error'>";
    echo "<h3 class='text-info specialHead text-center'><strong>" . htmlspecialchars($error) . "</strong></h3>";
    echo "<a href='change_password.php' class='btn btn-primary'><span class='glyphicon glyphicon-repeat'></span> <strong>Try Again</strong></a>";
}

// If the page is accessed directly without form submission
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo "<h3 class='text-info specialHead text-center'><strong>Invalid Request</strong></h3>";
    echo "<a href='cpanel.php' class='btn btn-primary'><span class='glyphicon glyphicon-home'></span> <strong>Return to Admin Panel</strong></a>";
}
?>

    		</div>
    		<div class="col-sm-4"></div>
    	</div>
    </div>

    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>