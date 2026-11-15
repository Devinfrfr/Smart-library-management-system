<?php

// members.php — Manage Members (staff / admin only)




include "includes/db_connect.php";

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

$error = "";

$total_members = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM members"))[0];

// ------------------------------------------------------------------
// Delete a member — blocked while they have issued books
// ------------------------------------------------------------------
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $check_issued = mysqli_query(
        $conn,
        "SELECT * FROM issued_books WHERE member_id = $id AND status = 'Issued'"
    );

    $check_requests = mysqli_query(
        $conn,
        "SELECT * FROM requests WHERE member_id = $id AND status = 'Pending'"
    );

    $check_fees = mysqli_query(
        $conn,
        "SELECT * FROM fees WHERE member_id = $id AND status = 'Unpaid'"
    );

    if (mysqli_num_rows($check_issued) > 0) {
        $error = "Cannot delete member with issued books! Return books first!";
    } elseif (mysqli_num_rows($check_requests) > 0) {
        $error = "Cannot delete member! They have a pending request.";
    } elseif (mysqli_num_rows($check_fees) > 0) {
        $error = "Cannot delete member! They have unpaid fees.";
    } else {
        mysqli_query($conn, "DELETE FROM members WHERE member_id = $id");
        header("Location: members.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Members</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="other-bg">

    <?php include "includes/sidebar.php"; ?>

    <div class="main main-staff">

        
            <h1>Manage<br>Members</h1>
            
      

        <div class="blob-bg">
            <div class="box">

                <?php if ($error != "") : ?>
                    <p style="color: red; font-weight: bold;"><?php echo $error; ?></p>
                <?php endif; ?>

                <div class="flex" style="align-items: center; justify-content: space-between;">
                    <input type="text" id="searchInput" class="search" placeholder="Search members..."
                    onkeyup="searchTable('searchInput', 'membersTable')" style="margin-bottom: 0;">

    <p style="margin: 0; font-weight: bold; color: #5F4B7A;">Total Members: <?php echo $total_members; ?></p>
</div>
                

                <table id="membersTable">
                    <tr>
                        <th>ID</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>NIC</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>

                    <?php
                    $all_members = mysqli_query($conn, "SELECT * FROM members ORDER BY member_id ASC");

                    while ($member = mysqli_fetch_assoc($all_members)) {
                        echo "<tr>"
                            . "<td>" . $member['member_id']   . "</td>"
                            . "<td>" . $member['member_code'] . "</td>"
                            . "<td>" . $member['full_name']   . "</td>"
                            . "<td>" . $member['nic']         . "</td>"
                            . "<td>" . $member['phone']       . "</td>"
                            . "<td>" . $member['status']      . "</td>"
                            . "<td><a href='members.php?delete=" . $member['member_id'] . "' class='btn btn-del'>Delete</a></td>"
                            . "</tr>";
                    }
                    ?>
                </table>

                

            </div>
        </div>
    </div>

    

    <script src="js/search.js"></script>

</body>
</html>
