<?php
session_start();

if (!isset($_SESSION['user_db']) || empty($_SESSION['user_db'])) {
    die("<div style='padding: 10px; background: #f44336; color: white;'>❌ Session is missing <code>user_db</code>. You may not have logged in properly, or your login script didn't set it. <br><br>Tip: Check that your login system sets <code>\$_SESSION['user_db']</code>.</div>");
}

$conn = new mysqli("localhost", "root", "", $_SESSION['user_db']);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT attendance.ID, attendance.STUDENTID, attendance.TIMEIN, attendance.TIMEOUT, attendance.LOGDATE, student.FIRSTNAME, student.LASTNAME
                        FROM attendance
                        LEFT JOIN student ON attendance.STUDENTID = student.STUDENTID");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="p-4">

<!-- Home Button -->
<div class="container">
    <a href="index.php" class="btn btn-primary mb-3">🏠 Home</a>
</div>

<!-- Export to Excel Button -->
<div class="container">
    <a href="export.php" class="btn btn-success mb-3">📥 Export to Excel</a>
</div>

<!-- Success/Error messages -->
<div class="container">
<?php
if (isset($_SESSION['error'])) {
    echo "<div class='alert alert-danger'>{$_SESSION['error']}</div>";
    unset($_SESSION['error']);
}
if (isset($_SESSION['success'])) {
    echo "<div class='alert alert-success'>{$_SESSION['success']}</div>";
    unset($_SESSION['success']);
}
?>
</div>

<!-- Attendance Table -->
<div class="container">
    <h2>Attendance Summary</h2>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th> <!-- Debug column, remove later -->
            <th>NAME</th>
            <th>STUDENT ID</th>
            <th>TIME IN</th>
            <th>TIME OUT</th>
            <th>LOGDATE</th>
            <th>ACTION</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()) { 
   
           // timezone conversion:
$timein = $row['TIMEIN'] ? (new DateTime($row['TIMEIN']))->setTimezone(new DateTimeZone('Asia/Manila'))->format('h:i:s A') : '';
$timeout = $row['TIMEOUT'] ? (new DateTime($row['TIMEOUT']))->setTimezone(new DateTimeZone('Asia/Manila'))->format('h:i:s A') : '';

        ?>
            <tr>
                <td><?= htmlspecialchars($row['ID']) ?></td>
                <td><?= htmlspecialchars(trim($row['FIRSTNAME'] . ' ' . $row['LASTNAME'])) ?></td>
                <td><?= htmlspecialchars($row['STUDENTID']) ?></td>
                <td><?= htmlspecialchars($timein) ?></td>
                <td><?= htmlspecialchars($timeout) ?></td>
                <td><?= htmlspecialchars($row['LOGDATE']) ?></td>
                <td>
                    <form method="POST" action="delete_attendance.php" onsubmit="return confirm('Are you sure you want to delete this record?')">
                        <input type="hidden" name="id" value="<?= $row['ID'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>   
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
