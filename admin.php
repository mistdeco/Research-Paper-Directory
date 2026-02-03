<?php
include "config.php";
session_start();

if (isset($_GET["logout"])) {
  $_SESSION = [];
  session_destroy();
  header("Location: index.php");
  exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = $_POST["username"];
  $password = $_POST["password"];

  if ($username === "" || $password === "") {
    $error = "All fields are required.";
  } else {
    $u = mysqli_real_escape_string($conn, $username);

    $sql = "SELECT * FROM admins WHERE username = '$u' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
      $row = mysqli_fetch_assoc($result);

      if ($password === $row["password"]) {
        $_SESSION["admin_logged_in"] = true;
        $_SESSION["admin_username"] = $row["username"];
        header("Location: adminindex.php");
        exit;
      } else {
        $error = "Invalid username or password.";
      }
    } else {
      $error = "Invalid username or password.";
    }
  }
}

function chars($v) {
  return htmlspecialchars((string)$v, ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Login</title>
  <link rel="stylesheet" href="style.css?v=1">
</head>
<body>
  <div class="background">
    <div class="card small">
      <h2 class="title">Admin Login</h2>

      <?php if ($error !== "") { ?>
        <div class="error"><?= chars($error) ?></div>
      <?php } ?>

      <form method="post" action="admin.php">
        <div class="formRow">
          <label>Username</label>
          <input type="text" name="username">
        </div>

        <div class="formRow">
          <label>Password</label>
          <input type="password" name="password">
        </div>

        <div class="formRow actions">
          <button class="btn btn-primary" type="submit">Login</button>
          <a class="btn" href="index.php">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
