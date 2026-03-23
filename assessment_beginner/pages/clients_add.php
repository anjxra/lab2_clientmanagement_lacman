<?php
include "../db.php";

$message = "";

if (isset($_POST['save'])) {
  $full_name = trim($_POST['full_name']);
  $email = trim($_POST['email']);
  $phone = trim($_POST['phone']);
  $address = trim($_POST['address']);

  if ($full_name == "" || $email == "") {
    $message = "Name and Email are required!";
  } else {
    $stmt = mysqli_prepare($conn, "INSERT INTO clients (full_name, email, phone, address) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $full_name, $email, $phone, $address);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: clients_list.php");
    exit;
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Add Client</title>
</head>
<body>
<?php include "../nav.php"; ?>

<div class="container">
  <h2>Add Client</h2>
  <p class="msg"><?php echo $message; ?></p>

  <div class="card">
    <form method="post">
      <label>Full Name*</label><br>
      <input type="text" name="full_name"><br><br>

      <label>Email*</label><br>
      <input type="text" name="email"><br><br>

      <label>Phone</label><br>
      <input type="text" name="phone"><br><br>

      <label>Address</label><br>
      <input type="text" name="address"><br><br>

      <button type="submit" name="save">Save</button>
    </form>
  </div>
</div>

</body>
</html>