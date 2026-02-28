<?php
include "../db.php";

$message = "";

if (isset($_POST['save'])) {
  $service_name = $_POST['service_name'];
  $description = $_POST['description'];
  $hourly_rate = $_POST['hourly_rate'];
  $is_active = $_POST['is_active'];

  if ($service_name == "" || $hourly_rate == "") {
    $message = "Service name and hourly rate are required!";
  } else if (!is_numeric($hourly_rate) || $hourly_rate <= 0) {
    $message = "Hourly rate must be a number greater than 0.";
  } else {
    $sql = "INSERT INTO services (service_name, description, hourly_rate, is_active)
            VALUES ('$service_name', '$description', '$hourly_rate', '$is_active')";
    mysqli_query($conn, $sql);

    header("Location: services_list.php");
    exit;
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Add Service</title>
</head>
<body>
<?php include "../nav.php"; ?>

<div class="container">
  <h2>Add Service</h2>
  <p class="msg"><?php echo $message; ?></p>

  <div class="card">
    <form method="post">
      <label>Service Name*</label><br>
      <input type="text" name="service_name"><br><br>

      <label>Description</label><br>
      <textarea name="description" rows="4" cols="40"></textarea><br><br>

      <label>Hourly Rate (₱)*</label><br>
      <input type="text" name="hourly_rate"><br><br>

      <label>Active?</label><br>
      <select name="is_active">
        <option value="1">Yes</option>
        <option value="0">No</option>
      </select><br><br>

      <button type="submit" name="save">Save Service</button>
    </form>
  </div>
</div>

</body>
</html>