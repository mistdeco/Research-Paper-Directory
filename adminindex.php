<?php
include "config.php";
session_start();

if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
  header("Location: admin.php");
  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Research Paper Directory - Admin</title>
  <link rel="stylesheet" href="style.css?v=1">
</head>
<body>
  <div class="navbar topNav">
    <div class="leftNav">
      <span class="title">Research Paper Directory</span>
    </div>
    <div class="rightNav">
      <span class="meta">Admin: <?= htmlspecialchars((string)($_SESSION["admin_username"] ?? ""), ENT_QUOTES, "UTF-8") ?></span>
      <a class="btn" href="index.php">Public View</a>
      <a class="btn btn-admin" href="admin.php?logout=1">Logout</a>
    </div>
  </div>

  <div class="background">
    <div class="card">
      <header class="pageHeader">
        <h2 class="title">Admin Browse</h2>
      </header>

      <?php
      $departmentList = [
        "" => "All",
        "Computer Science" => "Computer Science",
        "Information Technology" => "Information Technology",
        "Engineering" => "Engineering",
        "Mathematics" => "Mathematics",
        "Business" => "Business"
      ];

      $sortByOption = [
        "title" => "Title",
        "year_published" => "Publication",
        "created_at" => "Date Added"
      ];

      $sortDirOption = [
        "asc" => "Ascending",
        "desc" => "Descending"
      ];

      $query = isset($_GET["query"]) ? trim($_GET["query"]) : "";
      $year = isset($_GET["year"]) ? trim($_GET["year"]) : "";
      $department = isset($_GET["department"]) ? trim($_GET["department"]) : "";
      $sortBy = isset($_GET["sortBy"]) ? trim($_GET["sortBy"]) : "year_published";
      $sortDir = isset($_GET["sortDir"]) ? trim($_GET["sortDir"]) : "asc";

      $page = isset($_GET["page"]) ? (int)$_GET["page"] : 1;
      $perPage = 5;
      if ($page < 1) $page = 1;

      if (mb_strlen($query) > 120) $query = mb_substr($query, 0, 120);

      if ($year !== "" && (!ctype_digit($year) || strlen($year) !== 4)) {
        $year = "";
      }

      if (!array_key_exists($department, $departmentList)) {
        $department = "";
      }

      if (!array_key_exists($sortBy, $sortByOption)) {
        $sortBy = "year_published";
      }

      if (!array_key_exists($sortDir, $sortDirOption)) {
        $sortDir = "asc";
      }

      $where = [];

      if ($query !== "") {
        $qEsc = mysqli_real_escape_string($conn, $query);
        $like = "'%" . $qEsc . "%'";
        $where[] = "(title LIKE $like OR authors LIKE $like OR keywords LIKE $like)";
      }

      if ($year !== "") {
        $yearInt = (int)$year;
        $where[] = "year_published = $yearInt";
      }

      if ($department !== "") {
        $deptEsc = mysqli_real_escape_string($conn, $department);
        $where[] = "department = '" . $deptEsc . "'";
      }

      $sqlState = "";
      if (count($where) > 0) {
        $sqlState = " WHERE " . implode(" AND ", $where);
      }

      $dirSql = ($sortDir === "desc") ? "DESC" : "ASC";
      $orderField = $sortBy;
      $orderBy = "$orderField $dirSql, id $dirSql";

      $sqlCount = "SELECT COUNT(*) AS cnt FROM papers" . $sqlState;
      $resCount = mysqli_query($conn, $sqlCount);
      $rowcount = 0;
      if ($resCount) {
        $cr = mysqli_fetch_assoc($resCount);
        $rowcount = (int)($cr["cnt"] ?? 0);
      }

      $pagecount = (int)ceil($rowcount / $perPage);
      if ($pagecount < 1) $pagecount = 1;
      if ($page > $pagecount) $page = $pagecount;

      $offset = ($page - 1) * $perPage;

      $sql = "SELECT id, title, department, year_published, authors, keywords, created_at
              FROM papers" . $sqlState . " ORDER BY $orderBy
              LIMIT $perPage OFFSET $offset";

      $result = mysqli_query($conn, $sql);

      function chars($v) {
        return htmlspecialchars((string)$v, ENT_QUOTES, "UTF-8");
      }

      function url($p) {
        $qs = $_GET;
        $qs["page"] = $p;
        return "adminindex.php?" . http_build_query($qs);
      }
      ?>

      <section class="filterCard">
        <form class="filters" method="get" action="adminindex.php">
          <div class="formRow">
            <label for="query">Search</label>
            <input id="query" type="text" name="query" value="<?= chars($query) ?>" maxlength="120" placeholder="Title, author, or keyword">
            <small class="meta"><span id="queryNum">0</span>/120</small>
          </div>

          <div class="formRow inline">
            <div class="field">
              <label for="year">Year</label>
              <input id="year" type="text" name="year" value="<?= chars($year) ?>" placeholder="YYYY" maxlength="4">
            </div>

            <div class="field">
              <label for="department">Department</label>
              <select id="department" name="department">
                <?php foreach ($departmentList as $value => $label) { ?>
                  <option value="<?= chars($value) ?>" <?= ($department === $value ? "selected" : "") ?>>
                    <?= chars($label) ?>
                  </option>
                <?php } ?>
              </select>
            </div>

            <div class="field">
              <label for="sortBy">Sort By</label>
              <select id="sortBy" name="sortBy">
                <?php foreach ($sortByOption as $value => $label) { ?>
                  <option value="<?= chars($value) ?>" <?= ($sortBy === $value ? "selected" : "") ?>>
                    <?= chars($label) ?>
                  </option>
                <?php } ?>
              </select>
            </div>

            <div class="field">
              <label for="sortDir">Order</label>
              <select id="sortDir" name="sortDir">
                <?php foreach ($sortDirOption as $value => $label) { ?>
                  <option value="<?= chars($value) ?>" <?= ($sortDir === $value ? "selected" : "") ?>>
                    <?= chars($label) ?>
                  </option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="formRow actions">
            <button class="btn btn-primary" type="submit">Apply</button>
            <a class="btn" href="adminindex.php">Reset</a>
          </div>
        </form>
      </section>

      <section class="resultCard">
        <div class="meta">
          <?= $rowcount ?> result(s) · Page <?= $page ?> of <?= $pagecount ?>
        </div>

        <div class="results">
          <?php if (!$result) { ?>
            <div class="empty">Query failed.</div>
          <?php } else if ($rowcount === 0) { ?>
            <div class="empty">No papers found.</div>
          <?php } else { ?>
            <ol class="resList">
              <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <li class="resItem">
                  <article class="result">
                    <h3 class="resHeading">
                      <a class="resTitle">
                        <?= chars($row["title"]) ?>
                      </a>
                    </h3>

                    <div class="metaResult">
                      <span class="metaDepartment"><?= chars($row["department"]) ?></span>
                      <span class="lineBreak">·</span>
                      <span class="mYear"><?= chars($row["year_published"]) ?></span>
                      <?php if (!empty($row["created_at"])) { ?>
                        <span class="lineBreak">·</span>
                        <time class="mDate"><?= chars($row["created_at"]) ?></time>
                      <?php } ?>
                    </div>

                    <div class="resultLIne">
                      <strong>Authors:</strong> <span><?= chars($row["authors"]) ?></span>
                    </div>

                    <div class="resultLIne">
                      <strong>Keywords:</strong> <span><?= chars($row["keywords"]) ?></span>
                    </div>

                    <nav class="resAction" aria-label="Entry actions">
                      <a href="edit.php?id=<?= (int)$row["id"] ?>">Edit</a>
                      <span> | </span>
                      <a href="delete.php?id=<?= (int)$row["id"] ?>">Delete</a>
                    </nav>
                  </article>
                </li>
              <?php } ?>
            </ol>
          <?php } ?>
        </div>

        <div class="actions bottomAction">
          <a class="btn btn-primary" href="add.php">Add Paper</a>
        </div>

        <?php if ($pagecount > 1) { ?>
          <nav class="pagination" aria-label="Pagination">
            <?php if ($page > 1) { ?>
              <a class="link" href="<?= chars(url($page - 1)) ?>">Previous</a>
            <?php } else { ?>
              <span class="link disabled">Previous</span>
            <?php } ?>

            <?php
            $screen = 2;
            $start = $page - $screen;
            $end = $page + $screen;
            if ($start < 1) $start = 1;
            if ($end > $pagecount) $end = $pagecount;

            if ($start > 1) {
            ?>
              <a class="link" href="<?= chars(url(1)) ?>">1</a>
              <?php if ($start > 2) { ?>
                <span class="pageEllipsis">...</span>
              <?php } ?>
            <?php } ?>

            <?php for ($n = $start; $n <= $end; $n++) { ?>
              <?php if ($n === $page) { ?>
                <span class="link active" aria-current="page"><?= $n ?></span>
              <?php } else { ?>
                <a class="link" href="<?= chars(url($n)) ?>"><?= $n ?></a>
              <?php } ?>
            <?php } ?>

            <?php if ($end < $pagecount) { ?>
              <?php if ($end < $pagecount - 1) { ?>
                <span class="pageEllipsis">...</span>
              <?php } ?>
              <a class="link" href="<?= chars(url($pagecount)) ?>"><?= $pagecount ?></a>
            <?php } ?>

            <?php if ($page < $pagecount) { ?>
              <a class="link" href="<?= chars(url($page + 1)) ?>">Next</a>
            <?php } else { ?>
              <span class="link disabled">Next</span>
            <?php } ?>
          </nav>
        <?php } ?>
      </section>

    </div>
  </div>

  <script>
    (function () {
      var q = document.getElementById("query");
      var c = document.getElementById("queryNum");
      function upd() { c.textContent = String(q.value.length); }
      q.addEventListener("input", upd);
      upd();
    })();
  </script>
</body>
</html>
