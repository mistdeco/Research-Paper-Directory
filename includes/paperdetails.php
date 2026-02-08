<?php
include "config.php";
$id = $_GET['id'];
$result = mysqli_query($conn,
  "SELECT
      p.*,
      d.department,
      GROUP_CONCAT(
        DISTINCT TRIM(CONCAT(a.fName, ' ', IF(a.MI IS NULL OR a.MI = '', '', CONCAT(a.MI, '. ')), a.lName))
        ORDER BY pa.authorOrder
        SEPARATOR ', '
      ) AS authors
   FROM papers p
   JOIN departments d ON d.id = p.departmentId
   LEFT JOIN paper_authors pa ON pa.paperId = p.id
   LEFT JOIN authors a ON a.id = pa.authorId
   WHERE p.id=$id
   GROUP BY p.id"
);
$row = mysqli_fetch_assoc($result);

function chars($v) {
  return htmlspecialchars((string)$v, ENT_QUOTES, "UTF-8");
}

function apaCitation($title, $authors, $year) {
  $title = trim((string)$title);
  $year = trim((string)$year);
  if ($year === "") $year = "n.d.";

  $authorList = [];

  foreach (preg_split('/\s*,\s*/', (string)$authors) as $name) {
    $name = trim($name);
    if ($name === "") continue;

    // Expect: First M. Last
    $parts = preg_split('/\s+/', $name);
    if (count($parts) < 2) continue;

    $last = array_pop($parts);
    $initials = [];

    foreach ($parts as $p) {
      $p = str_replace('.', '', $p);
      if ($p !== "") {
        $initials[] = strtoupper(mb_substr($p, 0, 1)) . ".";
      }
    }

    $authorList[] = $last . ", " . implode(" ", $initials);
  }

  if (count($authorList) === 0) {
    $authorText = "Unknown author";
  } elseif (count($authorList) === 1) {
    $authorText = $authorList[0];
  } elseif (count($authorList) === 2) {
    $authorText = $authorList[0] . " & " . $authorList[1];
  } else {
    $last = array_pop($authorList);
    $authorText = implode(", ", $authorList) . ", & " . $last;
  }
  return $authorText . " (" . $year . "). " . $title . ".";
}

function mlaCitation($title, $authors, $year) {
  $title = trim((string)$title);
  $year = trim((string)$year);
  if ($year === "") $year = "n.d.";

  $names = array_filter(array_map("trim", preg_split('/\s*,\s*/', (string)$authors)));

  if (count($names) === 0) {
    $authorText = "Unknown author";
  } else {
    $first = array_shift($names);
    $parts = preg_split('/\s+/', $first);
    $last = array_pop($parts);
    $firstName = implode(" ", $parts);

    $authorText = $last . ", " . $firstName;

    if (count($names) === 1) {
      $second = preg_split('/\s+/', $names[0]);
      $secondLast = array_pop($second);
      $secondFirst = implode(" ", $second);
      $authorText .= ", and " . $secondFirst . " " . $secondLast;
    } elseif (count($names) > 1) {
      $authorText .= ", et al.";
    }
  }
  return $authorText . '. "' . $title . '." ' . $year . '.';
}

$apa = "";
$mla = "";
if ($row) {
  $apa = apaCitation($row["title"], $row["authors"], $row["yearPublished"]);
  $mla = mlaCitation($row["title"], $row["authors"], $row["yearPublished"]);
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Paper Details</title>
  <link rel="stylesheet" href="../css/StylePage.css">
</head>
<body>
  <div class="background">
    <div class="card">
      <header class="pageHeader">
        <h1 class="title">Paper Details</h1>
      </header>

      <section class="detailCard">
        <?php if (!$row) { ?>
          <div class="empty">Paper not found.</div>
          <div class="do">
            <a class="btn" href="/index.php">Back</a>
          </div>
        <?php } else { ?>
          <article class="paperDetails">
            <h2 class="paperTitle"><?= chars($row['title']) ?></h2>

            <div class="paperMeta">
              <span class="metaDepartment"><?= chars($row['department']) ?></span>
              <span class="lineBreak">·</span>
              <span class="mYear"><?= chars($row['yearPublished']) ?></span>
              <?php if (!empty($row['createdAt'])) { ?>
                <span class="lineBreak">·</span>
                <time class="mDate"><?= chars($row['createdAt']) ?></time>
              <?php } ?>
            </div>

            <div class="detailLIne">
              <strong>Authors:</strong>
              <span><?= chars($row['authors']) ?></span>
            </div>

            <div class="detailLIne">
              <strong>Keywords:</strong>
              <span><?= chars($row['keywords']) ?></span>
            </div>

            <div class="detailLIne">
              <strong>Abstract:</strong>
              <div class="abstractText">
                <?= nl2br(chars($row['abstract'])) ?>
              </div>
            </div>

            <div class="detailLIne">
              <strong>Citation:</strong>

              <div class="citationBox">
                <div class="field">
                  <div class="label">APA</div>
                  <textarea class="input" id="apaText" rows="2" readonly><?= chars($apa) ?></textarea>
                  <div class="do">
                    <button class="btn btn-primary" type="button" id="copyApa">Copy APA</button>
                  </div>
                </div>

                <div class="field">
                  <div class="label">MLA</div>
                  <textarea class="input" id="mlaText" rows="2" readonly><?= chars($mla) ?></textarea>
                  <div class="do">
                    <button class="btn btn-primary" type="button" id="copyMla">Copy MLA</button>
                  </div>
                </div>
              </div>
            </div>

            <div class="do">
              <a class="btn" href="/index.php">Back</a>
            </div>
          </article>
        <?php } ?>
      </section>

    </div>
  </div>

  <script src="../js/script.js"></script>
</body>
</html>
