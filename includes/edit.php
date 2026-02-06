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

$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM papers WHERE id=$id");
$row = mysqli_fetch_assoc($result);

$authorItems = [];
if ($row && isset($row['authors'])) {
  $authorItems = preg_split('/\s*,\s*/', trim($row['authors']));
  $authorItems = array_values(array_filter($authorItems, function($v){ return $v !== ""; }));
}
if (count($authorItems) === 0) $authorItems = [""];

if (isset($_POST['update'])) {
  $title = $_POST['title'];
  $authorsArr = isset($_POST['authors']) ? $_POST['authors'] : [];
  if (!is_array($authorsArr)) $authorsArr = [];
  $authorsArr = array_map('trim', $authorsArr);
  $authorsArr = array_values(array_filter($authorsArr, function($v){ return $v !== ""; }));
  $authors = implode(", ", $authorsArr);
  $keywords = $_POST['keywords'];
  $department = $_POST['departmentItem'];
  $year_published = $_POST['year_published'];
  $abstract = $_POST['abstract'];

  mysqli_query(
    $conn,
    "UPDATE papers
     SET title='$title',
         authors='$authors',
         keywords='$keywords',
         department='$department',
         year_published='$year_published',
         abstract='$abstract'
     WHERE id=$id"
  );

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
            <div class="label">Authors</div>
            <div id="authorsWrap">
              <?php for ($n = 0; $n < count($authorItems); $n++) { ?>
                <div class="authorRow">
                  <input class="input" type="text" name="authors[]" value="<?php echo htmlspecialchars($authorItems[$n]); ?>">
                  <?php if ($n === 0) { ?>
                    <button class="btn" type="button" id="addAuthor">+</button>
                  <?php } else { ?>
                    <button class="btn" type="button" onclick="this.parentNode.parentNode.removeChild(this.parentNode)">-</button>
                  <?php } ?>
                </div>
              <?php } ?>
            </div>
            <small class="meta">Use + to add more authors.</small>
          </div>

          <div class="field">
            <div class="label">Keywords</div>
            <input class="input" type="text" name="keywords" value="<?php echo htmlspecialchars($row['keywords']); ?>">
          </div>

          <div class="field">
            <div class="label">Department</div>
            <select class="input" name="departmentItem" required>
              <?php foreach ($departmentList as $value => $label) { ?>
                <?php if ($value !== "") { ?>
                  <option value="<?php echo htmlspecialchars($value); ?>" <?php echo ($row['department'] === $value ? "selected" : ""); ?>>
                    <?php echo htmlspecialchars($label); ?>
                  </option>
                <?php } ?>
              <?php } ?>
            </select>
          </div>

          <div class="field">
            <div class="label">Publication Year</div>
            <input class="input" type="text" name="year_published" value="<?php echo htmlspecialchars($row['year_published']); ?>">
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
  <script src="script.js"></script>
</body>
</html>
