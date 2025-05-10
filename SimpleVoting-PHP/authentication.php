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

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
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
          <a href="index.html" class="navbar-brand headerFont text-lg">Online Voting System</a>
        </div>

        <div class="collapse navbar-collapse" id="example-nav-collapse">
          <ul class="nav navbar-nav">
            <!-- 
            <li><a href="#featuresTab"><span class="subFont"><strong>Features</strong></span></a></li>
            <li><a href="#feedbackTab"><span class="subFont"><strong>Feedback</strong></span></a></li>
            <li><a href="#"><span class="subFont"><strong>About</strong></span></a></li>
          -->
          </ul>
          
          <button type="submit" class="btn btn-success navbar-right navbar-btn"><span class="normalFont"><strong>Admin Panel</strong></span></button>
        </div>

      </div> <!-- end of container -->
    </nav>

    
    <div class="container" style="padding-top:150px;">
      <div class="row">
        <div class="col-sm-4"></div>
        <div class="col-sm-4 text-center" style="border:2px solid gray;padding:50px;">
          <?php
          // Start session
          session_start();
          
          // Credentials
          $hostname = "localhost";
          $username = "root";
          $password = "";
          $database = "db_evoting";

          // Establish Connection
          $conn = mysqli_connect($hostname, $username, $password, $database);

          // Check connection
          if (!$conn) {
              die("Connection Failed: " . mysqli_connect_error());
          }

          // Process login form
          if ($_SERVER["REQUEST_METHOD"] == "POST") {
              
              // Check if fields are empty
              if (empty($_POST['adminUserName']) || empty($_POST['adminPassword'])) {
                  $error = "Username or Password is Required.";
                  echo "<p class='alert alert-danger'><strong>" . htmlspecialchars($error) . "</strong></p>";
              } else {
                  // Prepare statement to prevent SQL injection
                  $stmt = mysqli_prepare($conn, "SELECT admin_username, admin_password FROM tbl_admin WHERE admin_username = ?");
                  
                  if ($stmt) {
                      // Bind parameters
                      mysqli_stmt_bind_param($stmt, "s", $admin_username);
                      
                      // Set parameters
                      $admin_username = $_POST['adminUserName'];
                      
                      // Execute query
                      mysqli_stmt_execute($stmt);
                      
                      // Store result
                      mysqli_stmt_store_result($stmt);
                      
                      // Check if user exists
                      if (mysqli_stmt_num_rows($stmt) == 1) {
                          // Bind result variables
                          mysqli_stmt_bind_result($stmt, $db_username, $db_password);
                          
                          if (mysqli_stmt_fetch($stmt)) {
                              // Verify password
                              if (password_verify($_POST['adminPassword'], $db_password) || $_POST['adminPassword'] === $db_password) {
                                  // Password is correct, start a new session
                                  session_regenerate_id();
                                  
                                  // Store data in session variables
                                  $_SESSION['admin_username'] = $db_username;
                                  $_SESSION['loggedin'] = true;
                                  
                                  // Redirect to admin panel
                                  header("Location: cpanel.php");
                                  exit();
                              } else {
                                  // Password is not valid
                                  $error = "Invalid username or password";
                              }
                          }
                      } else {
                          // Username doesn't exist
                          $error = "Invalid username or password";
                      }
                      
                      // Close statement
                      mysqli_stmt_close($stmt);
                  } else {
                      $error = "Error preparing statement: " . mysqli_error($conn);
                  }
                  
                  // If we get here, authentication failed
                  echo "<p class='alert alert-danger'><strong>" . htmlspecialchars($error) . "</strong></p>";
                  echo "<p class='normalFont text-primary'><strong>Your combination of Username and Password is incorrect.</strong></p>";
                  echo "<br><a href='admin.html' class='btn btn-primary'><span class='glyphicon glyphicon-refresh'></span> <strong>Try Again</strong></a>";
              }
          }

          // Close connection
          mysqli_close($conn);
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