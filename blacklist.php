<?php
include "includes/db_connect.php";

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blacklist</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="other-bg">

    <?php include "includes/sidebar.php"; ?>

    <div class="main main-staff">
        <h1>Blacklist</h1>

        <div class="blob-bg">
            <div class="box">
                <h2>Blacklisted Members</h2>

                <table>
                    <tr>
                        <th>Member ID</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Reason</th>
                        <th>Fees Due</th>
                    </tr>

                    <?php
                    $all_blocked = mysqli_query($conn, "SELECT * FROM members WHERE status = 'Blocked'");

                    while ($member = mysqli_fetch_assoc($all_blocked)) {
                        $fee_row = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(total_fee) FROM fees WHERE member_id = " . $member['member_id'] . " AND status = 'Unpaid'"));
                        $fee_due = $fee_row[0] ?? 0;

                        echo "<tr>"
                            . "<td>" . $member['member_id'] . "</td>"
                            . "<td>" . $member['member_code'] . "</td>"
                            . "<td>" . $member['full_name'] . "</td>"
                            . "<td>Blocked</td>"
                            . "<td>Rs." . $fee_due . "</td>"
                            . "</tr>";
                    }
                    ?>
                </table>

            </div>
        </div>
    </div>

</body>
</html>