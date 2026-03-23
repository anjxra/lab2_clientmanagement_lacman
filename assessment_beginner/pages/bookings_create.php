<?php
include "../db.php";

$clients = mysqli_query($conn, "SELECT * FROM clients ORDER BY full_name ASC");
$services = mysqli_query($conn, "SELECT * FROM services WHERE is_active=1 ORDER BY service_name ASC");

$message = "";

if (isset($_POST['create'])) {
  $client_id = (int) $_POST['client_id'];
  $service_id = (int) $_POST['service_id'];
  $booking_date = $_POST['booking_date'];
  $hours = (int) $_POST['hours'];

  if ($client_id == "" || $service_id == "" || $booking_date == "" || $hours == "") {
    $message = "All fields are required!";
  } else {
    $sStmt = mysqli_prepare($conn, "SELECT hourly_rate FROM services WHERE service_id=?");
    mysqli_stmt_bind_param($sStmt, "i", $service_id);
    mysqli_stmt_execute($sStmt);
    $s = mysqli_fetch_assoc(mysqli_stmt_get_result($sStmt));
    mysqli_stmt_close($sStmt);

    if (!$s) {
      $message = "Selected service not found!";
    } else {
    $rate = (float) $s['hourly_rate'];

    $total = $rate * $hours;

    $stmt = mysqli_prepare($conn, "INSERT INTO bookings (client_id, service_id, booking_date, hours, hourly_rate_snapshot, total_cost, status) VALUES (?, ?, ?, ?, ?, ?, 'PENDING')");
    mysqli_stmt_bind_param($stmt, "iisidd", $client_id, $service_id, $booking_date, $hours, $rate, $total);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: bookings_list.php");
    exit;
    }
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Create Booking</title>
</head>
<body>
<?php include "../nav.php"; ?>

<div class="container">
  <h2>Create Booking</h2>
  <p class="msg"><?php echo $message; ?></p>

  <div class="card">
    <form method="post">
      <label>Client</label><br>
      <select name="client_id">
        <?php while($c = mysqli_fetch_assoc($clients)) { ?>
          <option value="<?php echo $c['client_id']; ?>"><?php echo $c['full_name']; ?></option>
        <?php } ?>
      </select><br><br>

      <label>Service</label><br>
      <select name="service_id">
        <?php while($s = mysqli_fetch_assoc($services)) { ?>
          <option value="<?php echo $s['service_id']; ?>">
            <?php echo $s['service_name']; ?> (₱<?php echo number_format($s['hourly_rate'],2); ?>/hr)
          </option>
        <?php } ?>
      </select><br><br>

      <label>Date</label><br>
      <input type="date" name="booking_date"><br><br>

      <label>Hours</label><br>
      <input type="number" name="hours" min="1" value="1"><br><br>

      <button type="submit" name="create">Create Booking</button>
    </form>
  </div>
</div>

</body>
</html>