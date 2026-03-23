<?php
include "../db.php";

$id = (int) $_GET['id'];

$get = mysqli_prepare($conn, "SELECT * FROM clients WHERE client_id = ?");
mysqli_stmt_bind_param($get, "i", $id);
mysqli_stmt_execute($get);
$client = mysqli_fetch_assoc(mysqli_stmt_get_result($get));
mysqli_stmt_close($get);

$message = "";

if (isset($_POST['update'])) {
  $full_name = trim($_POST['full_name']);
  $email = trim($_POST['email']);
  $phone = trim($_POST['phone']);
  $address = trim($_POST['address']);

  if ($full_name == "" || $email == "") {
    $message = "Name and Email are required!";
  } else {
    $stmt = mysqli_prepare($conn, "UPDATE clients SET full_name=?, email=?, phone=?, address=? WHERE client_id=?");
    mysqli_stmt_bind_param($stmt, "ssssi", $full_name, $email, $phone, $address, $id);
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
  <title>Edit Client</title>
</head>
<body>
<?php include "../nav.php"; ?>

<div class="container">
  <h2>Edit Client</h2>
  <p class="msg"><?php echo $message; ?></p>

  <div class="card">
    <form method="post">
      <label>Full Name*</label><br>
      <input type="text" name="full_name" value="<?php echo $client['full_name']; ?>"><br><br>

      <label>Email*</label><br>
      <input type="text" name="email" value="<?php echo $client['email']; ?>"><br><br>

      <label>Phone</label><br>
      <input type="text" name="phone" value="<?php echo $client['phone']; ?>"><br><br>

      <label>Address</label><br>
      <input type="text" name="address" value="<?php echo $client['address']; ?>"><br><br>

      <button type="submit" name="update">Update</button>
    </form>
  </div>
</div>

</body>
</html>