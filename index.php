<?php  
session_start();

// Fetch current time for Asia/Manila timezone from GeoNames API
$timezone = 'Asia/Manila';
$api_url = "http://api.geonames.org/timezoneJSON?lat=14.5995&lng=120.9842&username=francis09"; // GeoNames API with your username

// Fetch data from GeoNames API using file_get_contents (or you can use cURL here if needed)
$response = file_get_contents($api_url);
$data = json_decode($response, true);

// Check if the request was successful
if ($data && isset($data['time'])) {
    $philippine_time = $data['time']; // Format: 2025-05-29T12:34:56.789123+08:00
    $time = new DateTime($philippine_time);  // Assuming you already got this time string from the API
    $time->setTimezone(new DateTimeZone('Asia/Manila'));  // Ensure it's set to Philippine Time (Asia/Manila)
    echo $time->format('Y-m-d H:i:s');  // Output: 2025-05-29 12:34:56
} else {
    echo "Error fetching time from GeoNames API.";
    exit;
}

if (!isset($_SESSION['username']) || !isset($_SESSION['user_db'])) {
    header("Location: login.php");
    exit();
}
?>


<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>QR Code | Log in</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Scripts -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/webrtc-adapter/3.3.3/adapter.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.1.10/vue.min.js"></script>
  <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
  
  <!-- Styles -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <style>
    body {
      background: #eee;
    }
    #divvideo {
      box-shadow: 0px 0px 1px 1px rgba(0, 0, 0, 0.1);
    }
    .navbar {
      background-color: #4267B2;
      border-radius: 0;
    }
    .navbar-brand, .nav > li > a {
      color: white !important;
    }
    .navbar-right > li > a:hover,
    .nav > li > a:hover {
      background-color: #365899 !important;
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <ul class="nav">
    <li><a href="students.php">Student List</a></li>
    <li><a href="settings.php">Account Settings</a></li>
  </ul>

  <nav class="navbar navbar-inverse navbar-static-top">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand" href="#"><i class="glyphicon glyphicon-qrcode"></i> QR Code Attendance</a>
      </div>
      <ul class="nav navbar-nav">
        <li class="active"><a href="index.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>
        <li><a href="attendance.php"><span class="glyphicon glyphicon-calendar"></span> Attendance Report</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
      </ul>
    </div>
  </nav>

  <!-- Add Student Button -->
  <div class="container">
    <a href="add_student.php" class="btn btn-info" style="margin-bottom: 15px;">
      <i class="glyphicon glyphicon-plus"></i> Add Student
    </a>
  </div>

  <!-- Scanner + Form -->
  <div class="container">
    <div class="row">
      <div class="col-md-4" style="padding:10px;background:#fff;border-radius: 5px;" id="divvideo">
        <center><p class="login-box-msg"><i class="glyphicon glyphicon-camera"></i> TAP HERE</p></center>
        <video id="preview" width="100%" height="50%" style="border-radius:10px;"></video>
        <br><br>
        <?php
        if (isset($_SESSION['error'])) {
          echo "<div class='alert alert-danger alert-dismissible' style='background:red;color:#fff'>
                  <button type='button' class='close' data-dismiss='alert'>&times;</button>
                  <h4><i class='icon fa fa-warning'></i> Error!</h4>
                  ".$_SESSION['error']." 
                </div>";
          unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
          echo "<div class='alert alert-success alert-dismissible' style='background:green;color:#fff'>
                  <button type='button' class='close' data-dismiss='alert'>&times;</button>
                  <h4><i class='icon fa fa-check'></i> Success!</h4>
                  ".$_SESSION['success']." 
                </div>";
          unset($_SESSION['success']);
        }
        if (isset($_SESSION['info'])) {
          echo "<div class='alert alert-info alert-dismissible' style='background:#17a2b8;color:#fff'>
                  <button type='button' class='close' data-dismiss='alert'>&times;</button>
                  <h4><i class='icon fa fa-info'></i> Info!</h4>
                  ".$_SESSION['info']." 
                </div>";
          unset($_SESSION['info']);
        }
        ?>
      </div>

      <!-- Form and Attendance Table -->
      <div class="col-md-8">
        <form action="CheckInOut.php" method="post" class="form-horizontal" style="border-radius: 5px;padding:10px;background:#fff;" id="divvideo">
          <i class="glyphicon glyphicon-qrcode"></i> <label>SCAN QR CODE</label> <p id="time"></p>
          <input type="text" name="studentID" id="text" placeholder="scan qrcode" class="form-control" autofocus>
        </form>

        <div style="border-radius: 5px;padding:10px;background:#fff;" id="divvideo">
          <table id="example1" class="table table-bordered">
            <thead>
              <tr>
                <td>NAME</td>
                <td>STUDENT ID</td>
                <td>TIME IN</td>
                <td>TIME OUT</td>
                <td>LOGDATE</td>
              </tr>
            </thead>
            <tbody>
              <?php
              $server = "localhost";
              $username = "root";
              $password = "";
              $dbname = isset($_SESSION['user_db']) ? trim($_SESSION['user_db']) : 'qrdb';
              $conn = new mysqli($server, $username, $password, $dbname);
              $date = date('Y-m-d');

              if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
              }

              $sql = "SELECT * FROM attendance LEFT JOIN student ON attendance.STUDENTID=student.STUDENTID WHERE LOGDATE='$date'";
              $query = $conn->query($sql);

              while ($row = $query->fetch_assoc()) {
                // Explicitly set PHP's default timezone to Asia/Manila
                date_default_timezone_set('Asia/Manila'); // Set PHP default timezone to PHT

                if ($row['TIMEIN']) {
                    // Clean the time string by removing AM/PM suffix
                    $cleanTimeIn = str_replace([' AM', ' PM'], '', $row['TIMEIN']);  

                    // Parse the time as UTC, ensuring correct timezone handling
                    $timeIn = new DateTime($cleanTimeIn, new DateTimeZone('UTC'));  // UTC is used as reference
                    $timeIn->setTimezone(new DateTimeZone('Asia/Manila'));  // Convert to Philippine Time (PHT)

                    $timein = $timeIn->format('h:i:s A');  // Format to 12-hour AM/PM format
                } else {
                    $timein = '';
                }

                if ($row['TIMEOUT']) {
                    // Clean the time string by removing AM/PM suffix
                    $cleanTimeOut = str_replace([' AM', ' PM'], '', $row['TIMEOUT']);  

                    // Parse the time as UTC, ensuring correct timezone handling
                    $timeOut = new DateTime($cleanTimeOut, new DateTimeZone('UTC'));  // UTC is used as reference
                    $timeOut->setTimezone(new DateTimeZone('Asia/Manila'));  // Convert to Philippine Time (PHT)

                    $timeout = $timeOut->format('h:i:s A');  // Format to 12-hour AM/PM format
                } else {
                    $timeout = '';
                }

                echo "<tr>
                        <td>".$row['LASTNAME'].', '.$row['FIRSTNAME'].' '.$row['MNAME']."</td>
                        <td>".$row['STUDENTID']."</td>
                        <td>".$timein."</td>
                        <td>".$timeout."</td>
                        <td>".$row['LOGDATE']."</td>
                      </tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script>
    let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
    Instascan.Camera.getCameras().then(function (cameras) {
      if (cameras.length > 0) {
        scanner.start(cameras[0]);
      } else {
        alert('No cameras found.');
      }
    }).catch(function (e) {
      console.error(e);
    });

    scanner.addListener('scan', function (c) {
      document.getElementById('text').value = c;
      document.forms[0].submit();
    });

    var timestamp = '<?=time();?>';
    function updateTime() {
      $('#time').html(Date(timestamp));
      timestamp++;
    }
    $(function () {
      setInterval(updateTime, 1000);
    });
  </script>

  <script src="plugins/jquery/jquery.min.js"></script>
  <script src="plugins/bootstrap/js/bootstrap.min.js"></script>
  <script src="plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
  <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
  <script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
  <script>
    $(function () {
      $("#example1").DataTable({ responsive: true, autoWidth: false });
    });
  </script>
</body>
</html> 
