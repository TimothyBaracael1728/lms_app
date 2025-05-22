<?php 
session_start();
require_once('classess/database.php');
$con = new database();
$sweetAlertConfig = "";
if (isset($_POST['login'])){
  $email = $_POST['email'];
  $password = $_POST['password'];
  $user = $con->loginUser($email, $password);
  if ($user){
    
    $_SESSION['user_type'] = $user['user_type'];

    // Redirect based on user_type
    $redirectUrl = ($user['user_type'] == 1) ? 'admin_homepage.php' : 'homepage.php';
    $sweetAlertConfig = "<script>
        Swal.fire({
        icon: 'success',
        title: 'Login Successful',
        text: 'Welcome, " . addslashes(htmlspecialchars($user['user_FN'])) . "!',
        confirmButtonText: 'Continue'
        }).then(() => {
        window.location.href = '$redirectUrl';
        });
        </script>";

  } else {
    $sweetAlertConfig = "<script>
        Swal.fire({
        icon: 'error',
        title: 'Login Failed',
        text: 'Invalid username or password.'
        });
        </script>";
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/bootstrap.css">s
  <link rel="stylesheet" href="./package/dist/sweetalert2.css">
  <title>Library Management System</title>
</head>
<body>

    

  <div class="container custom-container rounded-3 shadow p-4 bg-light mt-5">
    <h3 class="text-center mb-4">Login</h3>
    <form method="post" action="" novalidate>
      <div class="form-group mb-3">
        <label for="email">Email:</label>
        <input type="email" class="form-control" name="email" placeholder="Enter email" required>
        <div class="valid-feedback">Looks good!</div>
        <div class="invalid-feedback">Please enter a valid email.</div>
      </div>
      <div class="form-group mb-3">
        <label for="password">Password:</label>
        <input type="password" class="form-control" name="password" placeholder="Enter password" required>
        <div class="valid-feedback">Looks good!</div>
        <div class="invalid-feedback">Please enter your password.</div>
      </div>
      <button type="submit" name="login" class="btn btn-primary w-100 py-2">Login</button>
      <div class="text-center mt-4">
        <a href="admin_homepage.php" class="text-decoration-none">Access Admin Homepage (For now)</a><br>
        <a href="registration.php" class="text-decoration-none">Don't have an account? Register here</a>
      </div>
    </form>
    <script src="./package/dist/sweetalert2.js"></script>
     <?php echo $sweetAlertConfig; ?>
  </div>

<script src="./bootstrap-5.3.3-dist/js/bootstrap.js"></script>
</body>
</html>