<?php
session_start();

if (isset($_SESSION['username'])) {
  header("Location: index.php");
  exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['username'];
  $password = $_POST['password'];

  if ($username === "admin" && $password === "admin") {
    $_SESSION['username'] = "ADMIN";
    header("Location: index.php");
    exit();
  } else {
    $error = "Invalid username or password!";
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login</title>
</head>
<body>

<link rel="stylesheet" href="/assessment_beginner/style.css">

<div class="container">
  <h2>Login</h2>
  <p class="msg"><?php echo $error; ?></p>

  <div class="card">
    <form method="post">
      <label>Username</label><br>
      <input type="text" name="username" required><br><br>

      <label>Password</label><br>
      <input type="password" name="password" required><br><br>

      <button type="submit">Login</button>
    </form>
  </div>
</div>

</body>
</html>