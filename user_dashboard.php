<?php
include "includes/db_connect.php";

if (!isset($_SESSION['member_id'])) {
    header("Location: index.php");
    exit();
}

$member_id = $_SESSION['member_id'];

// Name from DB
$member_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT full_name FROM members WHERE member_id = '$member_id'"));
$member_name = $member_data['full_name'];

// Count issued books
$issued_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM issued_books WHERE member_id = '$member_id' AND status = 'Issued'"));
$issued = $issued_data['total'];

// Count pending requests
$pending_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM requests WHERE member_id = '$member_id' AND status = 'Pending'"));
$pending = $pending_data['total'];

// Recent 5 issued books
$recent = mysqli_query($conn, "SELECT * FROM issued_books WHERE member_id = '$member_id' ORDER BY issue_id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Member Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <?php include "includes/sidebar.php"; ?>

    <div class="main main-member">

        <div class="flex" style="align-items: center; gap: 10px;">
            <div style="flex: 0.55;">
                <h1>Welcome,<br><?php echo $member_name; ?></h1>
            </div>

            <div class="cards" style="flex: 1; margin-bottom: 0;">
                <div class="black-card">
                    <p>My Issued Books</p>
                    <h3><?php echo $issued; ?></h3>
                </div>
                <div class="black-card">
                    <p>Pending Requests</p>
                    <h3><?php echo $pending; ?></h3>
                </div>
            </div>
        </div>

        <div class="white-box">
            <h2>My Recent Books</h2>
            <table>
                <tr>
                    <th>Issue ID</th>
                    <th>Book ID</th>
                    <th>Issue Date</th>
                    <th>Due Date</th>
                    <th>Status</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($recent)) { ?>
                    <tr>
                        <td><?php echo $row['issue_id']; ?></td>
                        <td><?php echo $row['book_id']; ?></td>
                        <td><?php echo $row['issue_date']; ?></td>
                        <td><?php echo $row['due_date']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>

    </div>

</body>
</html>