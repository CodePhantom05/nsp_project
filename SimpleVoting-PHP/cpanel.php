<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SVS</title>

  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="http://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
  <link href="http://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
  <link href="http://fonts.googleapis.com/css?family=Oswald" rel="stylesheet">
  <link href="http://fonts.googleapis.com/css?family=Roboto+Condensed" rel="stylesheet">

  <style>
    .headerFont { font-family: 'Ubuntu', sans-serif; font-size: 24px; }
    .subFont { font-family: 'Raleway', sans-serif; font-size: 14px; }
    .specialHead { font-family: 'Oswald', sans-serif; }
    .normalFont { font-family: 'Roboto Condensed', sans-serif; }
  </style>
</head>
<body>

<div class="container">
  <nav class="navbar navbar-default navbar-fixed-top navbar-inverse" role="navigation">
    <div class="container">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#example-nav-collapse">
        <span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
      </button>
      <div class="navbar-header">
        <a href="cpanel.php" class="navbar-brand headerFont text-lg">Online Voting System</a>
      </div>
      <div class="collapse navbar-collapse" id="example-nav-collapse">
        <ul class="nav navbar-nav">
          <li><a href="nomination.html"><span class="subFont"><strong>Nominations</strong></span></a></li>
          <li><a href="changePassword.php"><span class="subFont"><strong>Change Password</strong></span></a></li>
          <li><a href="users.php"><span class="subFont"><strong>Voters</strong></span></a></li> 
          <li><a href="feedbackReport.php"><span class="subFont"><strong>Feedback Report</strong></span></a></li> 
        </ul>
        <span class="normalFont"><a href="index.html" class="btn btn-danger navbar-right navbar-btn" style="border-radius:0%">Logout</a></span>
      </div>
    </div>
  </nav>

  <div class="container" style="padding:100px;">
    <div class="row">
      <div class="col-sm-12" style="border:2px outset gray;">
        <div class="page-header text-center">
          <h2 class="specialHead">ADMIN PANEL</h2>
          <p class="normalFont">Displaying all voting results</p>
        </div>

        <div class="col-sm-12">
          <?php
            require 'config.php';

            $conn = mysqli_connect($hostname, $username, $password, $database);
            if (!$conn) {
              die("Connection failed: " . mysqli_connect_error());
            }

            $candidates = [
              'JM' => ['name' => 'Juliet Mel', 'color' => 'danger'],
              'JRZ' => ['name' => 'Jaye Rozanne', 'color' => 'info'],
              'JW' => ['name' => 'John Walker', 'color' => 'warning'],
              'MAD' => ['name' => 'Mia Amanda', 'color' => 'success'],
              'DM' => ['name' => 'Diggory Mansel', 'color' => 'primary'],
            ];

            $totalVotes = 0;
            $voteCounts = [];

            foreach ($candidates as $code => $info) {
              $sql = "SELECT COUNT(*) AS count FROM tbl_users WHERE selection = '$code'";
              $result = mysqli_query($conn, $sql);
              
              if (!$result) {
                  die("Query failed for candidate $code: " . mysqli_error($conn));
              }
              
              $row = mysqli_fetch_assoc($result);
              
              $count = (int)$row['count'];
              $voteCounts[$code] = $count;
              $totalVotes += $count;
            }

            foreach ($candidates as $code => $info) {
              $count = $voteCounts[$code];
              $percentage = $totalVotes > 0 ? ($count / $totalVotes) * 100 : 0;
              echo "<strong>{$info['name']}</strong><br>";
              echo "
              <div class='progress'>
                <div class='progress-bar progress-bar-{$info['color']}' role='progressbar' 
                  aria-valuenow='".round($percentage)."' aria-valuemin='0' aria-valuemax='100' 
                  style='width: {$percentage}%'>
                  <span class='sr-only'>{$info['name']}</span>
                </div>
              </div>
              ";
            }

            echo "<hr>";
            echo "<div class='text-primary text-center'>
                    <h3 class='normalFont'>TOTAL VOTES: $totalVotes</h3>
                  </div>";

            mysqli_close($conn);
          ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
