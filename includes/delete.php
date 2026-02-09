<?php
include "config.php";

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // 1. Delete the paper (The links in paper_authors will usually delete if you have Foreign Keys, 
    // but we'll run the cleanup queries to be safe).
    mysqli_query($conn, "DELETE FROM papers WHERE id = $id");

    // 2. Automatically delete authors who no longer have any papers
    mysqli_query($conn, "DELETE FROM authors WHERE id NOT IN (SELECT DISTINCT authorId FROM paper_authors)");

    // 3. Automatically delete departments that no longer have any papers assigned to them
    mysqli_query($conn, "DELETE FROM departments WHERE id NOT IN (SELECT DISTINCT departmentId FROM papers)");
}

header("Location: adminindex.php");
exit;
?>