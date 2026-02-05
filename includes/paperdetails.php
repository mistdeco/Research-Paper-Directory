<?php
include "config.php";
$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM papers WHERE id=$id");
$row = mysqli_fetch_assoc($result);

function chars($v) {
  return htmlspecialchars((string)$v, ENT_QUOTES, "UTF-8");
}

function apaCitation($title, $authors, $year) {
  $authors = trim((string)$authors);
  $year = trim((string)$year);
  $title = trim((string)$title);

  $a = [];
  if ($authors !== "") {
    $parts = preg_split('/\s*,\s*/', $authors);
    foreach ($parts as $p) {
      $p = trim($p);
      if ($p !== "") $a[] = $p;
    }
  }

  $authorText = "Unknown author";
  if (count($a) === 1) {
    $authorText = $a[0];
  } else if (count($a) === 2) {
    $authorText = $a[0] . " & " . $a[1];
  } else if (count($a) > 2) {
    $last = array_pop($a);
    $authorText = implode(", ", $a) . ", & " . $last;
  }

  if ($year === "") $year = "n.d.";

  return $authorText . " (" . $year . "). " . $title . ".";
}

function mlaCitation($title, $authors, $year) {
  $authors = trim((string)$authors);
  $year = trim((string)$year);
  $title = trim((string)$title);

  $a = [];
  if ($authors !== "") {
    $parts = preg_split('/\s*,\s*/', $authors);
    foreach ($parts as $p) {
      $p = trim($p);
      if ($p !== "") $a[] = $p;
    }
  }

  $authorText = "Unknown author";
  if (count($a) === 1) {
    $authorText = $a[0];
  } else if (count($a) === 2) {
    $authorText = $a[0] . " and " . $a[1];
  } else if (count($a) > 2) {
    $authorText = $a[0] . ", et al.";
  }

  if ($year === "") $year = "n.d.";

  return $authorText . ". \"" . $title . ".\" " . $year . ".";
}

$apa = "";
$mla = "";
if ($row) {
  $apa = apaCitation($row["title"], $row["authors"], $row["year_published"]);
  $mla = mlaCitation($row["title"], $row["authors"], $row["year_published"]);
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
              <span class="mYear"><?= chars($row['year_published']) ?></span>
              <?php if (!empty($row['created_at'])) { ?>
                <span class="lineBreak">·</span>
                <time class="mDate"><?= chars($row['created_at']) ?></time>
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

  <script>
    (function () {
      function copyFrom(id) {
        var el = document.getElementById(id);
        if (!el) return;
        el.focus();
        el.select();
        try { document.execCommand("copy"); } catch (e) {}
      }

      var a = document.getElementById("copyApa");
      var m = document.getElementById("copyMla");

      if (a) a.addEventListener("click", function () { copyFrom("apaText"); });
      if (m) m.addEventListener("click", function () { copyFrom("mlaText"); });
    })();
  </script>
</body>
</html>
