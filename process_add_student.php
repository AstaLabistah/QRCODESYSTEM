<?php
session_start();

// TEMP FIX: Dev session override (remove this in production)
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // example user ID
}
if (!isset($_SESSION['user_db'])) {
    $_SESSION['user_db'] = 'qrdb'; // replace with dynamic DB if needed
}

require_once 'phpqrcode/qrlib.php';

// ✅ Connect to the correct user-specific database
$conn = new mysqli("localhost", "root", "", $_SESSION['user_db']);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ Get form data (make sure your form uses POST)
$studentid = $_POST['studentid'];
$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$course = $_POST['course'];

// ✅ Validate inputs (optional but good practice)
if (empty($studentid) || empty($firstname) || empty($lastname) || empty($course)) {
    $_SESSION['error'] = "All fields are required.";
    header("Location: add_student.php");
    exit();
}

// ✅ Generate QR content and file
$qrContent = $studentid;
$qrDir = 'qrcodes/';
if (!file_exists($qrDir)) {
    mkdir($qrDir, 0777, true);
}
$qrFile = $qrDir . $studentid . ".png";

// ✅ Create QR Code image
QRcode::png($qrContent, $qrFile, QR_ECLEVEL_L, 4);

// ✅ Ensure table structure is correct per-user (Add this auto-create line if needed)
$conn->query("CREATE TABLE IF NOT EXISTS student (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    STUDENTID VARCHAR(50) UNIQUE,
    FIRSTNAME VARCHAR(100),
    LASTNAME VARCHAR(100),
    COURSE VARCHAR(100)
)");

// ✅ Save student data
$stmt = $conn->prepare("INSERT INTO student (user_id, studentid, firstname, lastname, course) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $_SESSION['user_id'], $studentid, $firstname, $lastname, $course);

if ($stmt->execute()) {
    // ✅ Download QR code as image
    header('Content-Description: File Transfer');
    header('Content-Type: image/png');
    header('Content-Disposition: attachment; filename="' . basename($qrFile) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($qrFile));
    flush();
    readfile($qrFile);
    exit;
} else {
    $_SESSION['error'] = "Database error: " . $stmt->error;
    header("Location: index.php");
    exit;
}

$stmt->close();
$conn->close();
?>
