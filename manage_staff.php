<?php

// manage_staff.php — Manage Staff 


include "includes/db_connect.php";

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}


// Add a staff member

if (isset($_POST['add_staff'])) {
    $staff_name     = $_POST['full_name'];
    $staff_username = $_POST['username'];
    $staff_password = $_POST['password'];

    mysqli_query(
        $conn,
        "INSERT INTO staff (full_name, username, password)
         VALUES ('$staff_name', '$staff_username', '$staff_password')"
    );

    header("Location: manage_staff.php");
    exit();
}


// Delete a staff member

if (isset($_GET['delete'])) {
    mysqli_query($conn, "DELETE FROM staff WHERE staff_id = " . $_GET['delete']);
    header("Location: manage_staff.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Staff</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="other-bg">

    <?php include "includes/sidebar.php"; ?>

    <div class="main main-staff">
        <h1>Manage Staff</h1>

        <div class="flex">
            <div class="box" style="flex: 1.6;">
                <h2>Staff List</h2>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    $all_staff = mysqli_query($conn, "SELECT * FROM staff ORDER BY staff_id ASC");
                    while ($staff = mysqli_fetch_assoc($all_staff)) {
                        echo "<tr>"
                            . "<td>" . $staff['staff_id']  . "</td>"
                            . "<td>" . $staff['full_name'] . "</td>"
                            . "<td>" . $staff['username']  . "</td>"
                            . "<td><a href='manage_staff.php?delete=" . $staff['staff_id'] . "' class='btn btn-del'>Delete</a></td>"
                            . "</tr>";
                    }
                    ?>
                </table>
            </div>

            <div class="box" style="flex: 1;">
                <h2>Add New Staff</h2>
                <form method="POST">
                    <input type="text" name="full_name" placeholder="Full Name" required style="width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #CCCCCC; border-radius: 6px;">
                    <input type="text" name="username" placeholder="Username" required style="width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #CCCCCC; border-radius: 6px;">
                    <input type="password" name="password" placeholder="Password" required style="width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #CCCCCC; border-radius: 6px;">
                    <button type="submit" name="add_staff" class="btn btn-add" style="width: 100%; padding: 12px;">Add Staff</button>
                </form>
            </div>
        </div>
    </div>

    <script src="js/search.js"></script>

</body>
</html>