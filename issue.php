<?php
include "includes/db_connect.php";

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

$error = "";

// Issue a book directly
if (isset($_POST['issue'])) {
    $member_id = $_POST['member_id'];
    $book_id = $_POST['book_id'];
    $member_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM members WHERE member_id = $member_id"));
    $book_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM books WHERE book_id = $book_id"));

    if ($member_data['status'] == 'Blocked') {
        $error = "Member is blocked! Cannot issue.";
    } elseif ($book_data['available_qty'] <= 0) {
        $error = "Book not available!";
    } else {
        $issue_date = date("Y-m-d");
        $due_date = date("Y-m-d", strtotime("+14 days"));
        mysqli_query($conn, "INSERT INTO issued_books (book_id, member_id, issue_date, due_date, status) VALUES ($book_id, $member_id, '$issue_date', '$due_date', 'Issued')");
        mysqli_query($conn, "UPDATE books SET available_qty = available_qty - 1 WHERE book_id = $book_id");
        $error = "Book issued successfully! Due: $due_date";
    }
}

// Approve a pending request
if (isset($_GET['approve'])) {
    $request_id = $_GET['approve'];
    $request_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM requests WHERE request_id = $request_id"));
    $issue_date = date("Y-m-d");
    $due_date = date("Y-m-d", strtotime("+14 days"));
    mysqli_query($conn, "INSERT INTO issued_books (book_id, member_id, issue_date, due_date, status) VALUES (" . $request_data['book_id'] . ", " . $request_data['member_id'] . ", '$issue_date', '$due_date', 'Issued')");
    mysqli_query($conn, "UPDATE books SET available_qty = available_qty - 1 WHERE book_id = " . $request_data['book_id']);
    mysqli_query($conn, "UPDATE requests SET status = 'Approved' WHERE request_id = $request_id");
    header("Location: issue.php");
    exit();
}

// Reject a pending request
if (isset($_GET['reject'])) {
    mysqli_query($conn, "UPDATE requests SET status = 'Rejected' WHERE request_id = " . $_GET['reject']);
    header("Location: issue.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Issue Books</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="other-bg">

    <?php include "includes/sidebar.php"; ?>

    <div class="main main-staff">
        <h1>Issue<br>Books</h1>

        <div class="blob-bg">
            <div class="flex">

                <!-- Issue a book directly -->
                <div class="box">
                    <h2>Issue Books</h2>

                    <?php if ($error != "") : ?>
                        <p style="font-weight: bold; color: green;"><?php echo $error; ?></p>
                    <?php endif; ?>

                    <form method="POST">
                        <label>Member</label>
                        <select name="member_id" required>
                            <option value="">Select Member</option>
                            <?php
                            $all_members = mysqli_query($conn, "SELECT * FROM members WHERE status = 'Active'");

                            while ($member = mysqli_fetch_assoc($all_members)) {
                                echo "<option value='" . $member['member_id'] . "'>" . $member['member_code'] . " - " . $member['full_name'] . "</option>";
                            }
                            ?>
                        </select>

                        <label>Book</label>
                        <select name="book_id" required>
                            <option value="">Select Book</option>
                            <?php
                            $all_books = mysqli_query($conn, "SELECT * FROM books WHERE available_qty > 0");

                            while ($book = mysqli_fetch_assoc($all_books)) {
                                echo "<option value='" . $book['book_id'] . "'>" . $book['book_id'] . " - " . $book['title'] . "</option>";
                            }
                            ?>
                        </select>

                        <label>Issue Date</label>
                        <input type="text" value="<?php echo date('Y-m-d'); ?>" readonly>

                        <label>Due Date</label>
                        <input type="text" value="<?php echo date('Y-m-d', strtotime('+14 days')); ?>" readonly>

                        <button type="submit" name="issue" class="btn-purple">Issue Book</button>
                    </form>
                </div>

                <!-- Pending member requests -->
                <div class="box">
                    <h2>Pending Requests</h2>

                    <table>
                        <tr>
                            <th>Member</th>
                            <th>Book</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>

                        <?php
                        $pending = mysqli_query($conn, "SELECT * FROM requests WHERE status = 'Pending'");

                        while ($request = mysqli_fetch_assoc($pending)) {
                            $member_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM members WHERE member_id = " . $request['member_id']));
                            $book_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM books WHERE book_id = " . $request['book_id']));

                            echo "<tr>"
                                . "<td>" . $member_data['full_name'] . "</td>"
                                . "<td>" . $book_data['title'] . "</td>"
                                . "<td>" . $request['request_date'] . "</td>"
                                . "<td>"
                                . "<a href='issue.php?approve=" . $request['request_id'] . "' class='btn btn-add'>Approve</a> "
                                . "<a href='issue.php?reject=" . $request['request_id'] . "' class='btn btn-del'>Reject</a>"
                                . "</td>"
                                . "</tr>";
                        }
                        ?>
                    </table>
                </div>

            </div>
        </div>
    </div>

</body>
</html>