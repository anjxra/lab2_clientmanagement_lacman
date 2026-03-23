<?php
include "../db.php";

$id = (int) $_GET['id'];

$get = mysqli_prepare($conn, "SELECT * FROM services WHERE service_id = ?");
mysqli_stmt_bind_param($get, "i", $id);
mysqli_stmt_execute($get);
$service = mysqli_fetch_assoc(mysqli_stmt_get_result($get));
mysqli_stmt_close($get);

$message = "";

if (isset($_POST['update'])) {
  $name = trim($_POST['service_name']);
  $desc = trim($_POST['description']);
  $rate = $_POST['hourly_rate'];
  $active = (int) $_POST['is_active'];

  if ($name == "" || $rate == "") {
    $message = "Service Name and Hourly Rate are required!";
  } else {
    $stmt = mysqli_prepare($conn, "UPDATE services SET service_name=?, description=?, hourly_rate=?, is_active=? WHERE service_id=?");
    $rate_float = (float) $rate;
    mysqli_stmt_bind_param($stmt, "ssdii", $name, $desc, $rate_float, $active, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: services_list.php");
    exit;
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Edit Service</title>
</head>
<body>
<?php include "../nav.php"; ?>

<div class="container">
  <h2>Edit Service</h2>
  <p class="msg"><?php echo $message; ?></p>

  <div class="card">
    <form method="post">
      <label>Service Name*</label><br>
      <input type="text" name="service_name" value="<?php echo $service['service_name']; ?>"><br><br>

      <label>Description</label><br>
      <textarea name="description" rows="4" cols="40"><?php echo $service['description']; ?></textarea><br><br>

      <label>Hourly Rate*</label><br>
      <input type="text" name="hourly_rate" value="<?php echo $service['hourly_rate']; ?>"><br><br>

      <label>Active</label><br>
      <select name="is_active">
        <option value="1" <?php if($service['is_active']==1) echo "selected"; ?>>Yes</option>
        <option value="0" <?php if($service['is_active']==0) echo "selected"; ?>>No</option>
      </select><br><br>

      <button type="submit" name="update">Update</button>
    </form>
  </div>
</div>

</body>
</html>