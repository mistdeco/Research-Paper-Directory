<?php
include __DIR__ . "/config.php";

header("Content-Type: application/json");

$term = isset($_GET["term"]) ? trim($_GET["term"]) : "";
$suggestions = [];

if (mb_strlen($term) >= 1) {
    $qEsc = mysqli_real_escape_string($conn, $term);

    $sql = "
        SELECT title, authors, keywords
        FROM papers
        WHERE title LIKE '%$qEsc%'
           OR authors LIKE '%$qEsc%'
           OR keywords LIKE '%$qEsc%'
        LIMIT 10
    ";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {

            // Title
            if (!empty($row["title"])) {
                $suggestions[] = $row["title"];
            }

            // Authors
            foreach (explode(",", $row["authors"]) as $a) {
                $a = trim($a);
                if ($a !== "" && stripos($a, $term) !== false) {
                    $suggestions[] = $a;
                }
            }

            // Keywords
            foreach (explode(",", $row["keywords"]) as $k) {
                $k = trim($k);
                if ($k !== "" && stripos($k, $term) !== false) {
                    $suggestions[] = $k;
                }
            }
        }
    }
}

$suggestions = array_values(array_unique($suggestions));
$suggestions = array_slice($suggestions, 0, 6);

echo json_encode($suggestions);