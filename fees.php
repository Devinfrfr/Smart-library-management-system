<?php

// fees.php — Fee Collection 
// * Members with an unpaid fee older than 30 days are auto-blocked.
// * Marking a member's last unpaid fee as Paid auto-unblocks them.


include "includes/db_connect.php";

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// Auto-block members with an unpaid fee older than 30 days.
mysqli_query(
    $conn,
    "UPDATE members SET status = 'Blocked'
     WHERE member_id IN (
         SELECT member_id FROM fees
         WHERE status = 'Unpaid'
           AND DATEDIFF(CURDATE(), (SELECT issue_date FROM issued_books WHERE issue_id = fees.issue_id)) > 30
     )"
);

// ------------------------------------------------------------------
// Mark one fee as Paid (and unblock the member if nothing is unpaid)
// ------------------------------------------------------------------
if (isset($_GET['clear'])) {
    $id = $_GET['clear'];

    mysqli_query($conn, "UPDATE fees SET status = 'Paid', cleared_date = CURDATE() WHERE fee_id = $id");

    $fee_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM fees WHERE fee_id = $id"));

    $unpaid_check = mysqli_query(
        $conn,
        "SELECT * FROM fees WHERE member_id = " . $fee_data['member_id'] . " AND status = 'Unpaid'"
    );

    if (mysqli_num_rows($unpaid_check) == 0) {
        mysqli_query(
            $conn,
            "UPDATE members SET status = 'Active' WHERE member_id = " . $fee_data['member_id']
        );
    }

    header("Location: fees.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fee Collection</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="other-bg">

    <?php include "includes/sidebar.php"; ?>

    <div class="main main-staff">

        
            <h1>Fees</h1>
            
        

        <?php
        // Summary cards.
        $unpaid_row = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(total_fee) FROM fees WHERE status = 'Unpaid'"));
        $unpaid_total = $unpaid_row[0];
        if ($unpaid_total == "") {
            $unpaid_total = 0;
        }

        $paid_row = mysqli_fetch_row(
            mysqli_query(
                $conn,
                "SELECT SUM(total_fee) FROM fees
                 WHERE status = 'Paid'
                   AND MONTH(cleared_date) = MONTH(CURDATE())
                   AND YEAR(cleared_date)  = YEAR(CURDATE())"
            )
        );
        $paid_total = $paid_row[0];
        if ($paid_total == "") {
            $paid_total = 0;
        }

        echo "<div class='cards'>"
            . "<div class='black-card'><p>Unpaid Total</p><h3>Rs.$unpaid_total</h3></div>"
            . "<div class='black-card'><p>Paid Total - This Month</p><h3>Rs.$paid_total</h3></div>"
            . "</div>";
        ?>

        <div class="blob-bg">
            <div class="box">

                <table>
                    <tr>
                        <th>Fee ID</th>
                        <th>Issue ID</th>
                        <th>Member ID</th>
                        <th>Overdue Days</th>
                        <th>Total Fee</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>

                    <?php
                    $all_fees = mysqli_query($conn, "SELECT * FROM fees ORDER BY fee_id DESC");

                    while ($fee = mysqli_fetch_assoc($all_fees)) {
                        echo "<tr>"
                            . "<td>" . $fee['fee_id']       . "</td>"
                            . "<td>" . $fee['issue_id']     . "</td>"
                            . "<td>" . $fee['member_id']    . "</td>"
                            . "<td>" . $fee['overdue_days'] . "</td>"
                            . "<td>Rs." . $fee['total_fee'] . "</td>"
                            . "<td>" . $fee['status']       . "</td>"
                            . "<td>";

                        if ($fee['status'] == 'Unpaid') {
                            echo "<a href='fees.php?clear=" . $fee['fee_id'] . "' class='btn btn-add'>Mark as Paid</a>";
                        } else {
                            echo "Cleared";
                        }

                        echo "</td></tr>";
                    }
                    ?>
                </table>

            </div>
        </div>
    </div>

</body>
</html>
