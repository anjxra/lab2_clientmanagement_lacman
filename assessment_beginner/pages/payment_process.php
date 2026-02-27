<?php
include "../db.php";

$booking_id = $_GET['booking_id'];

$booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM bookings WHERE booking_id=$booking_id"));

$paidRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(amount_paid),0) AS paid FROM payments WHERE booking_id=$booking_id"));
$total_paid = $paidRow['paid'];

$balance = $booking['total_cost'] - $total_paid;
$message = "";

if (isset($_POST['pay'])) {
  $amount = $_POST['amount_paid'];
  $method = $_POST['method'];

  if ($amount <= 0) {
    $message = "Invalid amount!";
  } else if ($amount > $balance) {
    $message = "Amount exceeds balance!";
  } else {
    mysqli_query($conn, "INSERT INTO payments (booking_id, amount_paid, method)
      VALUES ($booking_id, $amount, '$method')");

    $paidRow2 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(amount_paid),0) AS paid FROM payments WHERE booking_id=$booking_id"));
    $total_paid2 = $paidRow2['paid'];

    $new_balance = $booking['total_cost'] - $total_paid2;

    if ($new_balance <= 0.009) {
      mysqli_query($conn, "UPDATE bookings SET status='PAID' WHERE booking_id=$booking_id");
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