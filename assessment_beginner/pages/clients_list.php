<?php
include "../db.php";
$result = mysqli_query($conn, "SELECT * FROM clients ORDER BY client_id DESC");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Clients</title>
</head>
<body>
<?php include "../nav.php"; ?>

<div class="container">
  <h2>Clients</h2>
  <p><a href="clients_add.php">+ Add Client</a></p>

  <div class="card" style="padding:0;">
    <table>
      <tr>
        <th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Action</th>
      </tr>
      <?php while($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
          <td><?php echo $row['client_id']; ?></td>
          <td><?php echo $row['full_name']; ?></td>
          <td><?php echo $row['email']; ?></td>
          <td><?php echo $row['phone']; ?></td>
          <td class="actions">
            <a href="clients_edit.php?id=<?php echo $row['client_id']; ?>">Edit</a>
          </td>
        </tr>
      <?php } ?>
    </table>
  </div>
</div>

</body>
</html>