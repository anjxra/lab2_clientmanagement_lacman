<?php
include "../db.php";

$message = "";

if (isset($_POST['assign'])) {
  $booking_id = (int) $_POST['booking_id'];
  $tool_id = (int) $_POST['tool_id'];
  $qty = (int) $_POST['qty_used'];

  $tStmt = mysqli_prepare($conn, "SELECT quantity_available FROM tools WHERE tool_id=?");
  mysqli_stmt_bind_param($tStmt, "i", $tool_id);
  mysqli_stmt_execute($tStmt);
  $toolRow = mysqli_fetch_assoc(mysqli_stmt_get_result($tStmt));
  mysqli_stmt_close($tStmt);

  if ($qty > $toolRow['quantity_available']) {
    $message = "Not enough available tools!";
  } else {
    $insStmt = mysqli_prepare($conn, "INSERT INTO booking_tools (booking_id, tool_id, qty_used) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($insStmt, "iii", $booking_id, $tool_id, $qty);
    mysqli_stmt_execute($insStmt);
    mysqli_stmt_close($insStmt);

    $updStmt = mysqli_prepare($conn, "UPDATE tools SET quantity_available = quantity_available - ? WHERE tool_id=?");
    mysqli_stmt_bind_param($updStmt, "ii", $qty, $tool_id);
    mysqli_stmt_execute($updStmt);
    mysqli_stmt_close($updStmt);

    $message = "Tool assigned successfully!";
  }
}

$tools = mysqli_query($conn, "SELECT * FROM tools ORDER BY tool_name ASC");
$bookings = mysqli_query($conn, "SELECT booking_id FROM bookings ORDER BY booking_id DESC");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Tools</title>
</head>
<body>
<?php include "../nav.php"; ?>

<div class="container">
  <h2>Tools / Inventory</h2>
  <p class="msg"><?php echo $message; ?></p>

  <div class="card" style="padding:0;">
    <table>
      <tr><th>Name</th><th>Total</th><th>Available</th></tr>
      <?php while($t = mysqli_fetch_assoc($tools)) { ?>
        <tr>
          <td><?php echo $t['tool_name']; ?></td>
          <td><?php echo $t['quantity_total']; ?></td>
          <td><?php echo $t['quantity_available']; ?></td>
        </tr>
      <?php } ?>
    </table>
  </div>

  <h2>Assign Tool to Booking</h2>

  <div class="card">
    <form method="post">
      <label>Booking ID</label><br>
      <select name="booking_id">
        <?php while($b = mysqli_fetch_assoc($bookings)) { ?>
          <option value="<?php echo $b['booking_id']; ?>">#<?php echo $b['booking_id']; ?></option>
        <?php } ?>
      </select><br><br>

      <label>Tool</label><br>
      <select name="tool_id">
        <?php
          $tools2 = mysqli_query($conn, "SELECT * FROM tools ORDER BY tool_name ASC");
          while($t2 = mysqli_fetch_assoc($tools2)) {
        ?>
          <option value="<?php echo $t2['tool_id']; ?>">
            <?php echo $t2['tool_name']; ?> (Avail: <?php echo $t2['quantity_available']; ?>)
          </option>
        <?php } ?>
      </select><br><br>

      <label>Qty Used</label><br>
      <input type="number" name="qty_used" min="1" value="1"><br><br>

      <button type="submit" name="assign">Assign</button>
    </form>
  </div>
</div>

</body>
</html>