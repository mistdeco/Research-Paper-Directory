<?php
include __DIR__ . "/config.php";

header("Content-Type: application/json");

$term = isset($_GET["term"]) ? trim($_GET["term"]) : "";
$field = isset($_GET["field"]) ? trim($_GET["field"]) : "";
$suggestions = [];

if (mb_strlen($term) >= 1) {
    $qEsc = mysqli_real_escape_string($conn, $term);

    if ($field === "department") {
        // Only suggest departments currently linked to at least one paper
        $sql = "
            SELECT DISTINCT d.department AS text
            FROM departments d
            INNER JOIN papers p ON d.id = p.departmentId
            WHERE d.department LIKE '%$qEsc%'
            ORDER BY d.department ASC
            LIMIT 10
        ";
    } else {
        $sql = "
            /* 1. Suggest Titles from existing papers */
            (SELECT p.title AS text
             FROM papers p
             WHERE p.title LIKE '%$qEsc%'
             LIMIT 10)
            
            UNION
            
            /* 2. Suggest Authors ONLY if they are linked to an existing paper */
            (SELECT DISTINCT TRIM(CONCAT(a.fName, ' ', IF(a.MI IS NULL OR a.MI = '', '', CONCAT(a.MI, '. ')), a.lName)) AS text
             FROM authors a
             INNER JOIN paper_authors pa ON a.id = pa.authorId
             INNER JOIN papers p2 ON pa.paperId = p2.id
             WHERE CONCAT(a.fName, ' ', IFNULL(a.MI, ''), ' ', a.lName) LIKE '%$qEsc%'
             LIMIT 10)
            
            UNION
            
            /* 3. Suggest Keywords from existing papers */
            (SELECT p.keywords AS text
             FROM papers p
             WHERE p.keywords LIKE '%$qEsc%'
             LIMIT 10)
        ";
    }

    $result = mysqli_query($conn, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            if (empty($row["text"])) continue;

            if ($field === "department") {
                $piece = trim($row["text"]);
                if ($piece !== "" && stripos($piece, $term) !== false) {
                    $suggestions[] = $piece;
                }
                continue;
            }

            // Explode handles comma-separated keywords
            foreach (explode(",", $row["text"]) as $piece) {
                $piece = trim($piece);
                if ($piece !== "" && stripos($piece, $term) !== false) {
                    $suggestions[] = $piece;
                }
            }
        }
    }
}

// Remove duplicates and limit to 6 suggestions
$suggestions = array_values(array_unique($suggestions));
$suggestions = array_slice($suggestions, 0, 6);

echo json_encode($suggestions);