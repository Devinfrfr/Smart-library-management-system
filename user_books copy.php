<?php

// user_books.php — Browse Books (member only)


include "includes/db_connect.php";

if (!isset($_SESSION['member_id'])) {
    header("Location: index.php");
    exit();
}

$member_id = $_SESSION['member_id'];
$message   = "";

// ------------------------------------------------------------------
// Request a book
// ------------------------------------------------------------------
if (isset($_GET['request'])) {
    $book_id = $_GET['request'];

    $check_request = mysqli_query(
        $conn,
        "SELECT * FROM requests
         WHERE member_id = $member_id AND book_id = $book_id AND status = 'Pending'"
    );

    if (mysqli_num_rows($check_request) > 0) {
        $message = "Already requested!";
    } else {
        mysqli_query(
            $conn,
            "INSERT INTO requests (member_id, book_id, status)
             VALUES ($member_id, $book_id, 'Pending')"
        );

        $message = "Request sent! Wait for staff approval.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Browse Books</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="other-bg">

    <?php include "includes/sidebar.php"; ?>

    <div class="main main-member">

        <h1>Browse<br>Books</h1>

        <div class="blob-bg">
            <div class="box">

                <div class="box-head">
                    <h2>Browse Books</h2>
                    <input type="text" id="searchInput" class="search" placeholder="Search books..."
                           onkeyup="searchTable('searchInput', 'booksTable')">
                </div>

                <?php if ($message != "") : ?>
                    <p style="color: green; font-weight: bold;"><?php echo $message; ?></p>
                <?php endif; ?>

                <table id="booksTable">
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category ID</th>
                        <th>Available</th>
                        <th>Action</th>
                    </tr>

                    <?php
                    $all_books = mysqli_query($conn, "SELECT * FROM books ORDER BY book_id ASC");

                    while ($book = mysqli_fetch_assoc($all_books)) {
                        echo "<tr>"
                            . "<td>" . $book['title']         . "</td>"
                            . "<td>" . $book['author']        . "</td>"
                            . "<td>" . $book['category_id']   . "</td>"
                            . "<td>" . $book['available_qty'] . "</td>"
                            . "<td>";

                        if ($book['available_qty'] > 0) {
                            echo "<a href='user_books.php?request=" . $book['book_id'] . "' class='btn btn-add'>Request</a>";
                        } else {
                            echo "Not Available";
                        }

                        echo "</td></tr>";
                    }
                    ?>
                </table>

            </div>
        </div>
    </div>

    <script src="js/search.js"></script>

</body>
</html>
