<?php
session_start();
if (!isset($_SESSION['user_db'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", $_SESSION['user_db']);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = $_POST['firstname'];
    $lname = $_POST['lastname'];
    $course = $_POST['course'];

    $stmt = $conn->prepare("UPDATE student SET FIRSTNAME=?, LASTNAME=?, COURSE=? WHERE STUDENTID=?");
    $stmt->bind_param("ssss", $fname, $lname, $course, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: students.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM student WHERE STUDENTID = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="p-4">
<div class="container">
    <h2>✏ Edit Student</h2>
    <form method="POST">
        <div class="form-group">
            <label>First Name:</label>
            <input type="text" name="firstname" class="form-control" value="<?= htmlspecialchars($student['FIRSTNAME']) ?>" required>
        </div>
        <div class="form-group">
            <label>Last Name:</label>
            <input type="text" name="lastname" class="form-control" value="<?= htmlspecialchars($student['LASTNAME']) ?>" required>
        </div>
        <div class="form-group">
            <label>Course:</label>
            <input type="text" name="course" class="form-control" value="<?= htmlspecialchars($student['COURSE']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">💾 Save</button>
        <a href="students.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
