<?php
include "config.php";
session_start();

if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
  header("Location: admin.php");
  exit;
}

$departmentList = [
  "" => "All",
  "Computer Science" => "Computer Science",
  "Information Technology" => "Information Technology",
  "Engineering" => "Engineering",
  "Mathematics" => "Mathematics",
  "Business" => "Business"
];

function parseAuthorName($name) {
    $name = trim(preg_replace('/\s+/', ' ', (string)$name));
    if ($name === "") return ["---", null, "---"]; // Placeholder to prevent skip

    $parts = preg_split('/\s+/', $name);
    
    if (count($parts) === 1) {
        return [" ", null, $parts[0]]; // Treat single name as Last Name
    } 
    
    $fName = $parts[0];
    if (count($parts) === 2) {
        return [$fName, null, $parts[1]];
    }
    
    // For 3+ parts: First, MI, and the rest is Last Name
    $MI = strtoupper(mb_substr($parts[1], 0, 1));
    $lName = implode(' ', array_slice($parts, 2));
    return [$fName, $MI, $lName];
}

$id = $_GET['id'];
$result = mysqli_query($conn,
  "SELECT p.*, d.department AS departmentName
   FROM papers p
   JOIN departments d ON d.id = p.departmentId
   WHERE p.id=$id"
);
$row = mysqli_fetch_assoc($result);

$authorItems = [];
if ($row) {
  $aRes = mysqli_query(
    $conn,
    "SELECT a.fName, a.MI, a.lName, pa.authorOrder
     FROM paper_authors pa
     JOIN authors a ON a.id = pa.authorId
     WHERE pa.paperId=$id
     ORDER BY pa.authorOrder ASC"
  );
  if ($aRes) {
    while ($aRow = mysqli_fetch_assoc($aRes)) {
      $full = trim($aRow['fName'] . ' ' . ($aRow['MI'] ? ($aRow['MI'] . '.') : '') . ' ' . $aRow['lName']);
      $full = trim(preg_replace('/\s+/', ' ', $full));
      if ($full !== "") $authorItems[] = $full;
    }
  }
}
if (count($authorItems) === 0) $authorItems = [""];

if (isset($_POST['update'])) {
  $title = $_POST['title'];
  $authorsArr = isset($_POST['authors']) ? $_POST['authors'] : [];
  if (!is_array($authorsArr)) $authorsArr = [];
  $authorsArr = array_map('trim', $authorsArr);
  $authorsArr = array_values(array_filter($authorsArr, function($v){ return $v !== ""; }));
  $keywords = $_POST['keywords'];
  $department = $_POST['departmentItem'];
  $year_published = $_POST['year_published'];
  $abstract = $_POST['abstract'];

  $titleEsc = mysqli_real_escape_string($conn, $title);
  $keywordsEsc = mysqli_real_escape_string($conn, $keywords);
  $deptName = trim((string)$department);
  $deptEsc = mysqli_real_escape_string($conn, $deptName);
  $yearInt = (int)$year_published;
  $abstractEsc = mysqli_real_escape_string($conn, $abstract);

  mysqli_query($conn, "START TRANSACTION");
  mysqli_query($conn, 
    "INSERT INTO departments (department)
     VALUES ('$deptEsc')
     ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id)"
  );
  $departmentId = (int)mysqli_insert_id($conn);

  mysqli_query($conn,
    "UPDATE papers
     SET title='$titleEsc',
         keywords='$keywordsEsc',
         departmentId=$departmentId,
         yearPublished=$yearInt,
         abstract='$abstractEsc'
     WHERE id=$id"
  );

  mysqli_query($conn, "DELETE FROM paper_authors WHERE paperId=$id");

$seenAuthorIds = [];
$order = 1;

foreach ($authorsArr as $authorRaw) {
    list($fName, $MI, $lName) = parseAuthorName($authorRaw);

    $fEsc = mysqli_real_escape_string($conn, $fName);
    $lEsc = mysqli_real_escape_string($conn, $lName);
    $miEsc = ($MI !== null) ? "'" . mysqli_real_escape_string($conn, $MI) . "'" : "NULL";

    // Find existing author
    $check = mysqli_query(
        $conn,
        "SELECT id FROM authors
         WHERE fName='$fEsc'
           AND lName='$lEsc'
           AND (MI <=> $miEsc)
         LIMIT 1"
    );

    if ($check && mysqli_num_rows($check) > 0) {
        $authorId = mysqli_fetch_assoc($check)['id'];
    } else {
        mysqli_query(
            $conn,
            "INSERT INTO authors (fName, MI, lName)
             VALUES ('$fEsc', $miEsc, '$lEsc')"
        );
        $authorId = mysqli_insert_id($conn);
    }

    if (!$authorId || isset($seenAuthorIds[$authorId])) continue;

    $seenAuthorIds[$authorId] = true;

    mysqli_query(
        $conn,
        "INSERT INTO paper_authors (paperId, authorId, authorOrder)
         VALUES ($id, $authorId, $order)"
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
  <title>Edit Research Paper</title>
  <link rel="stylesheet" href="../css/styleAdd.css">
</head>
<body>
  <div class="background">
    <div class="card">
      <header class="pageHeader">
        <h1 class="title">Edit Research Paper</h1>
      </header>

      <section class="formCard">
        <form id="editForm" class="form" method="POST">
          <div class="field">
            <div class="label">Title</div>
            <input class="input" type="text" name="title" value="<?php echo htmlspecialchars($row['title']); ?>" required>
          </div>

          <div class="field">
            <div class="label">Number of Authors</div>
            <input
              class="input"
              type="number"
              id="authorCount"
              min="1"
              max="20"
              value="<?= count($authorItems) ?>"
              required
            >
          </div>

          <div class="field">
  <div class="label">Authors</div>
  <div id="authorsWrap">
    <?php foreach ($authorItems as $i => $author): ?>
      <div class="authorRow">
        <input class="input" type="text" name="authors[]" 
               value="<?= htmlspecialchars($author, ENT_QUOTES, 'UTF-8') ?>" 
               placeholder="Author <?= ($i + 1) ?>" required>
      </div>
    <?php endforeach; ?>
  </div>
  <small>Use the format: Firstname Middlename/MI Lastname</small>
</div>


          <div class="field">
            <div class="label">Keywords</div>
            <input class="input" type="text" name="keywords" value="<?php echo htmlspecialchars($row['keywords']); ?>">
          </div>

          <div class="field">
            <div class="label">Department</div>
            <input class="input" type="text" name="departmentItem" value="<?php echo htmlspecialchars($row['departmentName']); ?>" required>
          </div>

          <div class="field">
            <div class="label">Publication Year</div>
            <input class="input" type="text" name="year_published" value="<?php echo htmlspecialchars($row['yearPublished']); ?>">
          </div>

          <div class="field">
            <div class="label">Abstract</div>
            <textarea class="input" name="abstract" rows="6"><?php echo htmlspecialchars($row['abstract']); ?></textarea>
          </div>

          <div class="do">
            <button class="btn btn-primary" type="submit" name="update">Update</button>
            <a id="backBtn" class="btn" href="adminindex.php">Back</a>
          </div>
        </form>
      </section>
    </div>
  </div>
  <script src="../js/script.js"></script>
</body>
</html>