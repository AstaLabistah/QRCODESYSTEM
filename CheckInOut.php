<?php 
session_start();

// ✅ Use the logged-in user's personal database
$server = "localhost";
$username = "root";
$password = "";
$dbname = isset($_SESSION['user_db']) ? $_SESSION['user_db'] : 'qrdb';

$conn = new mysqli($server, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['studentID'])) {
    $studentID = $_POST['studentID'];
    $date = date('Y-m-d');

    // Set timezone to Asia/Manila
    date_default_timezone_set('Asia/Manila');
    $time = new DateTime();
    $time->setTimezone(new DateTimeZone('Asia/Manila')); // Ensure we're in Asia/Manila timezone
    $formattedTime = $time->format('h:i:s A');  // Get the time in 12-hour format with AM/PM

    // 1. Check if student exists in this user's DB
    $sql = "SELECT * FROM student WHERE STUDENTID = '$studentID'";
    $query = $conn->query($sql);

    if ($query->num_rows < 1) {
        $_SESSION['error'] = 'Cannot find QRCode number ' . $studentID;
    } else {
        $row = $query->fetch_assoc();
        $fullname = $row['FIRSTNAME'] . ' ' . $row['LASTNAME'];

        // 2. Check if already logged today
        $sql = "SELECT * FROM attendance WHERE STUDENTID='$studentID' AND LOGDATE='$date'";
        $query = $conn->query($sql);

        if ($query->num_rows > 0) {
            $log = $query->fetch_assoc();

            if ($log['TIMEOUT'] === null || $log['TIMEOUT'] === '') {
                // ✅ Add Time Out
                $sql = "UPDATE attendance SET TIMEOUT='$formattedTime', STATUS='1' WHERE STUDENTID='$studentID' AND LOGDATE='$date'";
                if ($conn->query($sql) === TRUE) {
                    $_SESSION['success'] = 'Successfully Time Out: ' . $fullname;
                } else {
                    $_SESSION['error'] = 'Database error on Time Out';
                }
            } else {
                // ✅ Attendance complete already
                $_SESSION['info'] = 'Attendance already saved today for ' . $fullname;
            }
        } else {
            // ✅ No record yet — insert Time In
            $sql = "INSERT INTO attendance (STUDENTID, TIMEIN, LOGDATE, STATUS) VALUES ('$studentID', '$formattedTime', '$date', '0')";
            if ($conn->query($sql) === TRUE) {
                $_SESSION['success'] = 'Successfully Time In: ' . $fullname;
            } else {
                $_SESSION['error'] = 'Database error on Time In';
            }
        }
    }
} else {
    $_SESSION['error'] = 'Please scan your QR Code number';
}

header("location: index.php");
$conn->close();
?>
