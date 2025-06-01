<?php 
session_start();
if (!isset($_SESSION['user_db'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", $_SESSION['user_db']);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Deleting the student and their attendance
if (isset($_GET['delete_student_id'])) {
    $student_id = $_GET['delete_student_id'];

    // Deleting the student attendance records first
    $conn->query("DELETE FROM attendance WHERE STUDENTID = '$student_id'");

    // Deleting the student record
    $conn->query("DELETE FROM student WHERE STUDENTID = '$student_id'");

    // Redirect back to student list page
    header("Location: students.php");
    exit();
}

$result = $conn->query("SELECT * FROM student");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="p-4">
<div class="container">
    <a href="index.php" class="btn btn-primary mb-3">🏠 Home</a>

    <h2>📋 Student List</h2>
    <a href="add_student.php" class="btn btn-success mb-3">➕ Add New Student</a>
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>Student ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Course</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['STUDENTID']) ?></td>
                <td><?= htmlspecialchars($row['FIRSTNAME']) ?></td>
                <td><?= htmlspecialchars($row['LASTNAME']) ?></td>
                <td><?= htmlspecialchars($row['COURSE']) ?></td>
                <td>
                    <a href="edit_student.php?id=<?= urlencode($row['STUDENTID']) ?>" class="btn btn-primary btn-sm">✏ Edit</a>
                    <a href="students.php?delete_student_id=<?= urlencode($row['STUDENTID']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this student?')">🗑 Delete</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
