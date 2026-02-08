<?php 
include "includes/config.php"; 
session_start(); // Added to detect active sessions
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Research Paper Directory</title>
  <link rel="stylesheet" href="css/style.css?v=1">
</head>
<body>
  <div class="navbar topNav">
    <div class="leftNav">
      <span class="title">Research Paper Directory</span>
    </div>
    <div class="rightNav">
      <?php if (isset($_SESSION["admin_logged_in"]) && $_SESSION["admin_logged_in"] === true): ?>
          <a class="btn btn-admin" href="includes/adminindex.php">Back to Dashboard</a>
      <?php else: ?>
          <a class="btn btn-admin" href="includes/admin.php">Admin Login</a>
      <?php endif; ?>
    </div>
  </div>

  <div class="background">
    <div class="card">
      <header class="pageHeader">
        <h2 class="title">Browse</h2>
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
        return "index.php?" . http_build_query($qs);
      }
      ?>

      <section class="filterCard">
        <form class="filters" method="get" action="index.php">
          <div class="formRow" style="position: relative;">
            <label>Search Directory</label>

    <input type="text"
        id="query"
        name="query"
        value="<?= chars($query ?? "") ?>"
        placeholder="Title, author, or keyword..."
        autocomplete="off">
    <div id="searchSuggestions" class="suggestions-container"></div>
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

          <div class="formRowActions">
            <button class="btn btn-primary" type="submit">Apply</button>
            <a class="btn btn-reset" href="index.php">Reset</a>
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
                      <a class="resTitle" href="includes/paperdetails.php?id=<?= (int)$row["id"] ?>">
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
                  </article>
                </li>
              <?php } ?>
            </ol>
          <?php } ?>
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
    <script src="js/script.js"></script>
</body>
</html>
