<?php
include "../db.php";

$booking_id = (int) $_GET['booking_id'];

$bStmt = mysqli_prepare($conn, "SELECT * FROM bookings WHERE booking_id=?");
mysqli_stmt_bind_param($bStmt, "i", $booking_id);
mysqli_stmt_execute($bStmt);
$booking = mysqli_fetch_assoc(mysqli_stmt_get_result($bStmt));
mysqli_stmt_close($bStmt);

$pStmt = mysqli_prepare($conn, "SELECT IFNULL(SUM(amount_paid),0) AS paid FROM payments WHERE booking_id=?");
mysqli_stmt_bind_param($pStmt, "i", $booking_id);
mysqli_stmt_execute($pStmt);
$paidRow = mysqli_fetch_assoc(mysqli_stmt_get_result($pStmt));
mysqli_stmt_close($pStmt);
$total_paid = $paidRow['paid'];

$balance = $booking['total_cost'] - $total_paid;
$message = "";

if (isset($_POST['pay'])) {
  $amount = (float) $_POST['amount_paid'];
  $method = $_POST['method'];

  if ($amount <= 0) {
    $message = "Invalid amount!";
  } else if ($amount > $balance) {
    $message = "Amount exceeds balance!";
  } else {
    $insStmt = mysqli_prepare($conn, "INSERT INTO payments (booking_id, amount_paid, method) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($insStmt, "ids", $booking_id, $amount, $method);
    mysqli_stmt_execute($insStmt);
    mysqli_stmt_close($insStmt);

    $p2Stmt = mysqli_prepare($conn, "SELECT IFNULL(SUM(amount_paid),0) AS paid FROM payments WHERE booking_id=?");
    mysqli_stmt_bind_param($p2Stmt, "i", $booking_id);
    mysqli_stmt_execute($p2Stmt);
    $paidRow2 = mysqli_fetch_assoc(mysqli_stmt_get_result($p2Stmt));
    mysqli_stmt_close($p2Stmt);
    $total_paid2 = $paidRow2['paid'];

    $new_balance = $booking['total_cost'] - $total_paid2;

    if ($new_balance <= 0.009) {
      $updStmt = mysqli_prepare($conn, "UPDATE bookings SET status='PAID' WHERE booking_id=?");
      mysqli_stmt_bind_param($updStmt, "i", $booking_id);
      mysqli_stmt_execute($updStmt);
      mysqli_stmt_close($updStmt);
    }

    header("Location: bookings_list.php");
    exit;
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Process Payment</title>
</head>
<body>
<?php include "../nav.php"; ?>

<div class="container">
  <h2>Process Payment (Booking #<?php echo $booking_id; ?>)</h2>

  <div class="card">
    <p>Total Cost: ₱<?php echo number_format($booking['total_cost'],2); ?></p>
    <p>Total Paid: ₱<?php echo number_format($total_paid,2); ?></p>
    <p><b>Balance: ₱<?php echo number_format($balance,2); ?></b></p>
  </div>

  <p class="msg"><?php echo $message; ?></p>

  <div class="card">
    <form method="post">
      <label>Amount Paid</label><br>
      <input type="number" name="amount_paid" step="0.01"><br><br>

      <label>Method</label><br>
      <select name="method">
        <option value="CASH">CASH</option>
        <option value="GCASH">GCASH</option>
        <option value="CARD">CARD</option>
      </select><br><br>

      <button type="submit" name="pay">Save Payment</button>
    </form>
  </div>
</div>

</body>
</html>