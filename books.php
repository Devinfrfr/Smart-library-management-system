<?php


include "includes/db_connect.php";

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

$error = "";


// Add a new book
if (isset($_POST['add_book'])) {
    $title  = $_POST['title'];
    $author = $_POST['author'];
    $isbn   = $_POST['isbn'];
    $cat_id = $_POST['category_id'];
    $qty    = $_POST['qty'];
    $shelf  = $_POST['shelf'];

    $check_isbn = mysqli_query($conn, "SELECT * FROM books WHERE isbn = '$isbn'");

    if (mysqli_num_rows($check_isbn) > 0) {
        $error = "This ISBN already exists! Use a different ISBN.";
    } else {
        mysqli_query(
            $conn,
            "INSERT INTO books (title, author, isbn, category_id, total_qty, available_qty, shelf_location)
             VALUES ('$title', '$author', '$isbn', '$cat_id', '$qty', '$qty', '$shelf')"
        );

        header("Location: books.php");
        exit();
    }
}



// Delete a book — blocked while the book has any history
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $check_issued = mysqli_query($conn, "SELECT * FROM issued_books WHERE book_id = $id");
    $check_requests = mysqli_query($conn, "SELECT * FROM requests WHERE book_id = $id");
    $check_return = mysqli_query($conn, "SELECT * FROM return_books WHERE book_id = $id");

    if (mysqli_num_rows($check_issued) > 0) {
        $error = "Cannot delete book! This book has issue history. Return book first!";
    } elseif (mysqli_num_rows($check_requests) > 0) {
        $error = "Cannot delete book! This book has request history.";
    } elseif (mysqli_num_rows($check_return) > 0) {
        $error = "Cannot delete book! This book has return history.";
    } else {
        mysqli_query($conn, "DELETE FROM books WHERE book_id = $id");
        header("Location: books.php");
        exit();
    }
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Books</title>
    <link rel="stylesheet" href="css/style.css?v=2">
</head>
<body class="other-bg">

    <?php include "includes/sidebar.php"; ?>

    <div class="main main-staff">
        <h1>Manage Books</h1>

        <?php if ($error != "") : ?>
            <p style="color: red; font-weight: bold;"><?php echo $error; ?></p>
        <?php endif; ?>

        <div class="flex">
            <div class="box" style="flex: 1.8;">
                <h2>Books List</h2>
                <input type="text" id="searchInput" class="search" placeholder="Search books..."
                       onkeyup="searchTable('searchInput', 'booksTable')">
                <table id="booksTable">
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>ISBN</th>
                        <th>Category ID</th>
                        <th>Total</th>
                        <th>Available</th>
                        <th>Shelf</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    $all_books = mysqli_query($conn, "SELECT * FROM books ORDER BY book_id ASC");
                    while ($book = mysqli_fetch_assoc($all_books)) {
                        echo "<tr>"
                            . "<td>" . $book['book_id']       . "</td>"
                            . "<td>" . $book['title']         . "</td>"
                            . "<td>" . $book['author']        . "</td>"
                            . "<td>" . $book['isbn']          . "</td>"
                            . "<td>" . $book['category_id']   . "</td>"
                            . "<td>" . $book['total_qty']     . "</td>"
                            . "<td>" . $book['available_qty'] . "</td>"
                            . "<td>" . $book['shelf_location'] . "</td>"
                            . "<td><a href='books.php?delete=" . $book['book_id'] . "' class='btn btn-del'>Delete</a></td>"
                            . "</tr>";
                    }
                    ?>
                </table>
            </div>

            <div class="box" style="flex: 1;">
                <h2>Add New Book</h2>
                <form method="POST">
                    <input type="text" name="title" placeholder="Title" required style="width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #CCCCCC; border-radius: 6px;">
                    <input type="text" name="author" placeholder="Author" required style="width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #CCCCCC; border-radius: 6px;">
                    <input type="text" name="isbn" placeholder="ISBN" required style="width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #CCCCCC; border-radius: 6px;">
                    <select name="category_id" required style="width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #CCCCCC; border-radius: 6px;">
                        <option value="">Select Category</option>
                        <option value="1">IT</option>
                        <option value="2">Fiction</option>
                        <option value="3">Science</option>
                        <option value="4">History</option>
                        <option value="5">Mathematics</option>
                    </select>
                    <input type="number" name="qty" placeholder="Quantity" required style="width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #CCCCCC; border-radius: 6px;">
                    <input type="text" name="shelf" placeholder="Shelf Location (e.g., A1)" required style="width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #CCCCCC; border-radius: 6px;">
                    <button type="submit" name="add_book" class="btn btn-add" style="width: 100%; padding: 12px;">Add Book</button>
                </form>
            </div>
        </div>
    </div>

    <script src="js/search.js"></script>

</body>
</html>