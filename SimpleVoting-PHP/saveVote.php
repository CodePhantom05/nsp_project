<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SVS</title>

  <!-- Bootstrap CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Ubuntu|Raleway|Oswald|Roboto+Condensed" rel="stylesheet">

  <style>
    .headerFont {
      font-family: 'Ubuntu', sans-serif;
      font-size: 24px;
    }

    .subFont {
      font-family: 'Raleway', sans-serif;
      font-size: 14px;
    }

    .specialHead {
      font-family: 'Oswald', sans-serif;
    }

    .normalFont {
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
          <a href="index.html" class="navbar-brand headerFont text-lg">Online Voting System</a>
        </div>
      </div>
    </nav>

    <div class="container" style="padding-top:150px;">
      <div class="row">
        <div class="col-sm-4"></div>
        <div class="col-sm-4 text-center" style="border:2px solid gray; padding:50px;">
          <?php
            require('config.php'); // Include your DB connection

            // Sanitize input
            //function test_input($data) {
              //return htmlspecialchars(stripslashes(trim($data)));
            //}

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
              if (!empty($_POST["voterName"]) && !empty($_POST["voterEmail"]) && !empty($_POST["voterID"]) && !empty($_POST["selectedCandidate"])) {
                // Sanitize and assign values
                $name = test_input($_POST["voterName"]);
                $email = test_input($_POST["voterEmail"]);
                $voterID = test_input($_POST["voterID"]);
                $selection = test_input($_POST["selectedCandidate"]);

                // Connect to database (use your config.php for this)
                $conn = new mysqli("localhost", "root", "", "db_evoting");

                if ($conn->connect_error) {
                  die("<img src='images/error.png' width='70' height='70'><h3 class='text-danger'>Database connection failed!</h3>");
                }

                // Prepare SQL statement
                $sql = "INSERT INTO tbl_users (full_name, email, voter_id, selection) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);

                if ($stmt === false) {
                  die("Prepare failed: " . $conn->error); // This will output any SQL syntax or connection errors
                }

                // Bind parameters (s for string types)
                $stmt->bind_param("ssss", $name, $email, $voterID, $selection);

                // Execute the query
                if ($stmt->execute()) {
                  echo "<img src='images/success.png' width='70' height='70'>";
                  echo "<h3 class='text-info specialHead text-center'><strong>YOU'VE VOTED SUCCESSFULLY!</strong></h3>";
                  echo "<a href='index.html' class='btn btn-primary'> <span class='glyphicon glyphicon-ok'></span> <strong>Finish</strong> </a>";
                } else {
                  echo "<img src='images/error.png' width='70' height='70'>";
                  echo "<h3 class='text-danger specialHead text-center'><strong>Sorry! We encountered an issue.</strong></h3>";
                  echo "<a href='index.html' class='btn btn-primary'> <span class='glyphicon glyphicon-ok'></span> <strong>Finish</strong> </a>";
                }

                // Close statement and connection
                $stmt->close();
                $conn->close();
              } else {
                echo "<h4 class='text-danger'>All fields are required.</h4>";
              }
            } else {
              echo "<h4 class='text-danger'>Invalid request.</h4>";
            }
          ?>
        </div>
        <div class="col-sm-4"></div>
      </div>
    </div>
  </div>

  <!-- jQuery and Bootstrap JS -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
</body>
</html>
