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

if (isset($_POST['save'])) {
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
    "INSERT INTO papers (title, authors, keywords, department, year_published, abstract)
     VALUES ('$title','$authors','$keywords','$department','$year_published','$abstract')"
  );

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
            <div class="label">Authors</div>

            <div id="authorsWrap">
              <div class="authorRow">
                <input class="input" type="text" name="authors[]" required>
                <button class="btn" type="button" id="addAuthor">+</button>
              </div>
            </div>

            <small class="meta">Use + to add more authors.</small>
          </div>

          <div class="field">
            <div class="label">Keywords</div>
            <input class="input" type="text" name="keywords" required>
          </div>

          <div class="field">
            <div class="label">Department</div>
            <select class="input" name="departmentItem" required>
              <option value="" disabled selected>Select department</option>
              <?php foreach ($departmentList as $value => $label) { ?>
                <?php if ($value !== "") { ?>
                  <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                  </option>
                <?php } ?>
              <?php } ?>
            </select>
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
