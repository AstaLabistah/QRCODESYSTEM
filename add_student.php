<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title>Add Student</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>
<body style="padding: 20px; background: #f5f5f5;">

<div class="container" style="background:#fff;padding:20px;border-radius:10px;">
  <h2><i class="glyphicon glyphicon-plus"></i> Add New Student</h2>

  <!-- ✅ Message display area -->
  <?php
  if (isset($_SESSION['error'])) {
    echo "<div class='alert alert-danger'>".$_SESSION['error']."</div>";
    unset($_SESSION['error']);
  }
  if (isset($_SESSION['success'])) {
    echo "<div class='alert alert-success'>".$_SESSION['success']."</div>";
    unset($_SESSION['success']);
  }
  ?>

  <!-- ✅ Form -->
  <form method="POST" action="process_add_student.php">
    <div class="form-group">
      <label>Student ID:</label>
      <input type="text" name="studentid" class="form-control" required>
    </div>
    <div class="form-group">
      <label>First Name:</label>
      <input type="text" name="firstname" class="form-control" required>
    </div>
    <div class="form-group">
      <label>Last Name:</label>
      <input type="text" name="lastname" class="form-control" required>
    </div>
    <div class="form-group">
      <label>Course:</label>
      <input type="text" name="course" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">
      <i class="glyphicon glyphicon-floppy-disk"></i> Add
    </button>
    <a href="index.php" class="btn btn-default">Cancel</a>
  </form>
</div>

</body>
</html>
