<?php

// user_my_books.php — My Books (member only, view only)


include "includes/db_connect.php";

if (!isset($_SESSION['member_id'])) {
    header("Location: index.php");
    exit();
}

$member_id = $_SESSION['member_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Books</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="other-bg">

    <?php include "includes/sidebar.php"; ?>

    <div class="main main-member">

        <h1>My<br>Books</h1>

        <div class="blob-bg">
            <div class="box">

                <h2>My Issued Books</h2>

                <table>
                    <tr>
                        <th>Issue ID</th>
                        <th>Book ID</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                        <th>Status</th>
                    </tr>

                    <?php
                    $my_books = mysqli_query(
                        $conn,
                        "SELECT * FROM issued_books WHERE member_id = $member_id ORDER BY issue_id DESC"
                    );

                    while ($row = mysqli_fetch_assoc($my_books)) {
                        echo "<tr>"
                            . "<td>" . $row['issue_id']   . "</td>"
                            . "<td>" . $row['book_id']    . "</td>"
                            . "<td>" . $row['issue_date'] . "</td>"
                            . "<td>" . $row['due_date']   . "</td>"
                            . "<td>" . $row['status']     . "</td>"
                            . "</tr>";
                    }
                    ?>
                </table>

                <p>Return and fees are handled by staff only.</p>

            </div>
        </div>
    </div>

</body>
</html>
