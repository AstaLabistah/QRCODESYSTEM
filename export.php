<?php
session_start();

if (!isset($_SESSION['user_db']) || empty($_SESSION['user_db'])) {
    die("<div style='padding: 10px; background: #f44336; color: white;'>❌ Session is missing <code>user_db</code>. You may not have logged in properly, or your login script didn't set it. <br><br>Tip: Check that your login system sets <code>\$_SESSION['user_db']</code>.</div>");
}

$conn = new mysqli("localhost", "root", "", $_SESSION['user_db']);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch data
$sql = "SELECT attendance.ID, attendance.STUDENTID, attendance.TIMEIN, attendance.TIMEOUT, attendance.LOGDATE, student.FIRSTNAME, student.LASTNAME
        FROM attendance
        LEFT JOIN student ON attendance.STUDENTID = student.STUDENTID";
$result = $conn->query($sql);

// Set CSV headers
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="attendance_report.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Write header row to CSV
fputcsv($output, ['ID', 'NAME', 'STUDENT ID', 'TIME IN', 'TIME OUT', 'LOGDATE']);

// Write each row of data to CSV
while ($row = $result->fetch_assoc()) {
    $timein = $row['TIMEIN'] ? (new DateTime($row['TIMEIN']))->modify('+6 hours')->format('h:i:s A') : '';
    $timeout = $row['TIMEOUT'] ? (new DateTime($row['TIMEOUT']))->modify('+6 hours')->format('h:i:s A') : '';
    fputcsv($output, [
        $row['ID'],
        $row['FIRSTNAME'] . ' ' . $row['LASTNAME'],
        $row['STUDENTID'],
        $timein,
        $timeout,
        $row['LOGDATE']
    ]);
}

fclose($output);
exit();
?>
