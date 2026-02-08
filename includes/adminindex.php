<?php
include "config.php";
session_start();

if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    header("Location: admin.php");
    exit;
}

function chars($v) {
    return htmlspecialchars((string)$v, ENT_QUOTES, "UTF-8");
}

function url($p) {
    $qs = $_GET;
    $qs["page"] = $p;
    return "adminindex.php?" . http_build_query($qs);
}

$sortByOption = ["title" => "Title", "year_published" => "Publication", "created_at" => "Date Added"];
$sortDirOption = ["asc" => "Ascending", "desc" => "Descending"];

$query = isset($_GET["query"]) ? trim($_GET["query"]) : "";
$year = isset($_GET["year"]) ? trim($_GET["year"]) : "";
$department = isset($_GET["department"]) ? trim($_GET["department"]) : "";
$sortBy = isset($_GET["sortBy"]) && array_key_exists($_GET["sortBy"], $sortByOption) ? $_GET["sortBy"] : "year_published";
$sortDir = isset($_GET["sortDir"]) && array_key_exists($_GET["sortDir"], $sortDirOption) ? $_GET["sortDir"] : "asc";

$page = isset($_GET["page"]) ? (int)$_GET["page"] : 1;
$perPage = 5;

$where = [];
$joins = " FROM papers p
          JOIN departments d ON d.id = p.departmentId
          LEFT JOIN paper_authors pa ON pa.paperId = p.id
          LEFT JOIN authors a ON a.id = pa.authorId";

if ($query !== "") {
    $qEsc = mysqli_real_escape_string($conn, $query);
    $where[] = "(p.title LIKE '%$qEsc%' OR p.keywords LIKE '%$qEsc%' OR a.fName LIKE '%$qEsc%' OR a.lName LIKE '%$qEsc%')";
}
if ($year !== "" && ctype_digit($year)) {
    $where[] = "p.yearPublished = " . (int)$year;
}
if (mb_strlen($department) > 100) $department = mb_substr($department, 0, 100);
if ($department !== "") {
    $deptEsc = mysqli_real_escape_string($conn, $department);
    $where[] = "d.department LIKE '%$deptEsc%'";
}

$sqlState = count($where) > 0 ? " WHERE " . implode(" AND ", $where) : "";

$orderField = "p.yearPublished";
if ($sortBy === "title") $orderField = "p.title";
if ($sortBy === "created_at") $orderField = "p.createdAt";
$orderBy = $orderField . " " . ($sortDir === "desc" ? "DESC" : "ASC");

$resCount = mysqli_query($conn, "SELECT COUNT(DISTINCT p.id) AS cnt" . $joins . $sqlState);
$rowcount = ($resCount) ? (int)mysqli_fetch_assoc($resCount)["cnt"] : 0;
$pagecount = max(1, (int)ceil($rowcount / $perPage));
$page = max(1, min($page, $pagecount));
$offset = ($page - 1) * $perPage;

$sql = "SELECT
            p.id,
            p.title,
            d.department,
            p.yearPublished,
            p.createdAt,
            p.keywords,
            GROUP_CONCAT(
              DISTINCT TRIM(CONCAT(a.fName, ' ', IF(a.MI IS NULL OR a.MI = '', '', CONCAT(a.MI, '. ')), a.lName))
              ORDER BY pa.authorOrder
              SEPARATOR ', '
            ) AS authors
        " . $joins . $sqlState . "
        GROUP BY p.id
        ORDER BY $orderBy
        LIMIT $perPage OFFSET $offset";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin Dashboard - Research Paper Directory</title>
    <link rel="stylesheet" href="../css/AdminStyle.css?v=1">
</head>
<body>
    <nav class="navbar topNav">
        <div class="leftNav">
            <span class="navLogo">Research Paper Directory</span>
        </div>
        <div class="rightNav">
            <span class="adminName">Admin: <?= chars($_SESSION["admin_username"] ?? "admin") ?></span>
            <a class="navLink" href="../index.php">Public View</a>
            <a class="navBtn logout" href="admin.php?logout=1">Logout</a>
        </div>
    </nav>

    <div class="background">
        <div class="mainContainer">
            <header class="pageHeader">
                <h1 class="title">Admin Browse</h1>
            </header>

            <section class="filterCard">
                <form class="filters" method="get" autocomplete="off">
                    <div class="formRow" style="position: relative;">
                        <label>Search Directory</label>
                        <input type="text" id="query" name="query" value="<?= chars($query) ?>" placeholder="Title, author, or keyword...">
                        <div id="searchSuggestions" class="suggestions-container"></div>
                    </div>

                    <div class="formGrid">
                        <div class="field">
                            <label>Year</label>
                            <input type="text" name="year" value="<?= chars($year) ?>" placeholder="YYYY" maxlength="4">
                        </div>

                        <div class="field" style="position: relative;">
                          <label for="department">Department</label>
                          <input id="department" type="text" name="department" value="<?= chars($department) ?>" placeholder="Department" autocomplete="off" maxlength="100">
                          <div id="departmentSuggestions" class="suggestions-container"></div>
                        </div>

                        <div class="field">
                            <label>Sort By</label>
                            <select name="sortBy">
                                <?php foreach ($sortByOption as $val => $lab): ?>
                                    <option value="<?= chars($val) ?>" <?= ($sortBy === $val ? "selected" : "") ?>><?= chars($lab) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="filterActions">
                        <button class="btn btn-primary" type="submit">Apply</button>
                        <a class="btn btn-reset" href="adminindex.php">Reset</a>
                        <a class="btn btn-add" href="add.php">+ Add Paper</a>
                    </div>
                </form>
            </section>

            <section class="resultContainer">
                <div class="resultsMeta"><?= $rowcount ?> result(s) · Page <?= $page ?> of <?= $pagecount ?></div>

                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <div class="paperCard">
                            <div class="paperContent">
                                <h3 class="paperTitle"><?= chars($row["title"]) ?></h3>
                                <div class="paperMeta">
                                    <span class="deptTag"><?= chars($row["department"]) ?></span> • 
                                    <span class="yearTag"><?= chars($row["yearPublished"]) ?></span>
                                    <?php if (!empty($row["createdAt"])): ?>
                                        • <time class="dateTag"><?= chars($row["createdAt"]) ?></time>
                                    <?php endif; ?>
                                </div>
                                <div class="infoLine"><strong>Authors:</strong> <?= chars($row["authors"]) ?></div>
                                <div class="infoLine"><strong>Keywords:</strong> <?= chars($row["keywords"]) ?></div>
                            </div>
                            
                            <div class="paperActions">
                                <a class="actionBtn edit" href="edit.php?id=<?= (int)$row['id'] ?>">Edit</a>
                                <a class="actionBtn delete" href="delete.php?id=<?= (int)$row['id'] ?>" onclick="return confirm('Permanently delete this paper?')">Delete</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="emptyState">No research papers found.</div>
                <?php endif; ?>

                <?php if ($pagecount > 1): ?>
                <nav class="pagination">
                    <a href="<?= url(max(1, $page-1)) ?>" class="pgLink <?= ($page <= 1 ? 'disabled' : '') ?>">Previous</a>
                    <span class="pgInfo"><?= $page ?> / <?= $pagecount ?></span>
                    <a href="<?= url(min($pagecount, $page+1)) ?>" class="pgLink <?= ($page >= $pagecount ? 'disabled' : '') ?>">Next</a>
                </nav>
                <?php endif; ?>
            </section>
        </div>
    </div>
    <script src="../js/script.js"></script>
</body>
</html>
