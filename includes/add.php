<?php
include "config.php";
session_start();

if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
  header("Location: admin.php");
  exit;
}

function parseAuthorName($name) {
  $name = trim(preg_replace('/\s+/', ' ', (string)$name));
  if ($name === "") return ["", null, ""];

  $parts = preg_split('/\s+/', $name);
  $fName = $parts[0] ?? "";
  $lName = "";
  $MI = null;

  if (count($parts) === 1) {
    $lName = "";
  } else if (count($parts) === 2) {
    $lName = $parts[1];
  } else {
    $mid = $parts[1];
    $MI = strtoupper(mb_substr($mid, 0, 1));
    $lName = implode(' ', array_slice($parts, 2));
  }

  return [$fName, $MI, $lName];
}

if (isset($_POST['save'])) {
  $title = trim((string)($_POST['title'] ?? ""));
  $keywords = trim((string)($_POST['keywords'] ?? ""));
  $departmentName = trim((string)($_POST['departmentItem'] ?? ""));
  $yearPublished = (int)($_POST['year_published'] ?? 0);
  $abstract = trim((string)($_POST['abstract'] ?? ""));

  $authorsArr = isset($_POST['authors']) ? $_POST['authors'] : [];
  if (!is_array($authorsArr)) $authorsArr = [];
  $authorsArr = array_map('trim', $authorsArr);
  $authorsArr = array_values(array_filter($authorsArr, function($v){ return $v !== ""; }));

  $titleEsc = mysqli_real_escape_string($conn, $title);
  $keywordsEsc = mysqli_real_escape_string($conn, $keywords);
  $deptEsc = mysqli_real_escape_string($conn, $departmentName);
  $abstractEsc = mysqli_real_escape_string($conn, $abstract);

  mysqli_query($conn, "START TRANSACTION");

  mysqli_query(
    $conn,
    "INSERT INTO departments (department)
     VALUES ('$deptEsc')
     ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id)"
  );
  $departmentId = (int)mysqli_insert_id($conn);

  mysqli_query(
    $conn,
    "INSERT INTO papers (title, keywords, departmentId, yearPublished, abstract)
     VALUES ('$titleEsc', '$keywordsEsc', $departmentId, $yearPublished, '$abstractEsc')"
  );
  $paperId = (int)mysqli_insert_id($conn);

  $seenAuthorIds = [];
  $order = 1;

  foreach ($authorsArr as $authorRaw) {
    list($fName, $MI, $lName) = parseAuthorName($authorRaw);
    $fName = trim((string)$fName);
    $lName = trim((string)$lName);
    if ($fName === "" || $lName === "") continue;

    $fEsc = mysqli_real_escape_string($conn, $fName);
    $lEsc = mysqli_real_escape_string($conn, $lName);

    $miSql = "NULL";
    if ($MI !== null && trim((string)$MI) !== "") {
      $miSql = "'" . mysqli_real_escape_string($conn, $MI) . "'";
    }

    mysqli_query(
      $conn,
      "INSERT INTO authors (fName, MI, lName)
       VALUES ('$fEsc', $miSql, '$lEsc')
       ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id)"
    );
    $authorId = (int)mysqli_insert_id($conn);

    if ($authorId < 1 || isset($seenAuthorIds[$authorId])) continue;
    $seenAuthorIds[$authorId] = true;

    mysqli_query(
      $conn,
      "INSERT INTO paper_authors (paperId, authorId, authorOrder)
       VALUES ($paperId, $authorId, $order)"
    );
    $order++;
  }

  mysqli_query($conn, "COMMIT");

  header("Location: adminindex.php");
  exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Add Research Paper</title>
  <link rel="stylesheet" href="../css/styleAdd.css">
</head>
<body>
  <div class="background">
    <div class="card">
      <header class="pageHeader">
        <h1 class="title">Add Research Paper</h1>
      </header>

      <section class="formCard">
        <form class="form" method="POST">
          <div class="field">
            <div class="label">Title</div>
            <input class="input" type="text" name="title" required>
          </div>

          <div class="field">
            <div class="label">Number of Authors</div>
            <input
              class="input"
              type="number"
              id="authorCount"
              min="1"
              max="20"
              value="1"
              required
            >
          </div>

          <div class="field">
            <div class="label">Authors</div>
            <div id="authorsWrap">
              <div class="authorRow">
                <input class="input" type="text" name="authors[]" required>
              </div>
            </div>
            <small>Use the format: Firstname Middlename/MI Lastname</small>
          </div>

          <div class="field">
            <div class="label">Keywords</div>
            <input class="input" type="text" name="keywords" required>
          </div>

          <div class="field">
            <div class="label">Department</div>
            <input class="input" type="text" name="departmentItem" required>
          </div>

          <div class="field">
            <div class="label">Year of Publication</div>
            <input class="input" type="text" name="year_published" required>
          </div>

          <div class="field">
            <div class="label">Abstract</div>
            <textarea class="input" name="abstract" rows="6" required></textarea>
          </div>

          <div class="do">
            <button class="btn btn-primary" type="submit" name="save">Save</button>
            <a class="btn" href="adminindex.php">Cancel</a>
          </div>
        </form>
      </section>

    </div>
  </div>

  <script src="../js/script.js"></script>
</body>
</html>
