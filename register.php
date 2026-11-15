<?php

// register.php — Member self-registration




include "includes/db_connect.php";

$message = "";

if (isset($_POST['register'])) {
    $member_name    = $_POST['full_name'];
    $member_nic     = $_POST['nic'];
    $member_phone   = $_POST['phone'];
    $member_email   = $_POST['email'];
    $member_address = $_POST['address'];

    $nic_length = strlen($member_nic);

    if ($nic_length != 10 && $nic_length != 12) {
        $message = "NIC must be 10 or 12 characters!";
    } else {
        $check_nic = mysqli_query($conn, "SELECT * FROM members WHERE nic = '$member_nic'");

        if (mysqli_num_rows($check_nic) > 0) {
            $message = "This NIC already registered!";
        } else {
           $max_row = mysqli_fetch_row(mysqli_query($conn, "SELECT MAX(member_id) FROM members"));
        $new_number = $max_row[0] + 1;
        $new_code = "M" . str_pad($new_number, 3, "0", STR_PAD_LEFT);

            mysqli_query(
                $conn,
                "INSERT INTO members (member_code, full_name, nic, phone, email, address, status)
                 VALUES ('$new_code', '$member_name', '$member_nic', '$member_phone', '$member_email', '$member_address', 'Active')"
            );

            $message = "Registered! Your Member Code is $new_code";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-body">

    <div class="login-wrap">

        <div class="login-left">
            <img src="img/loginpageimage.jpeg" alt="Library">
        </div>

        <div class="login-right">
            <div class="login-title-top">LIBRARY MANAGEMENT SYSTEM</div>

            <div class="login-card">
                <h2>REGISTER</h2>

                <?php if ($message != "") : ?>
                    <p style="text-align: center;"><?php echo $message; ?></p>
                <?php endif; ?>

                <form method="POST">
                    <label>Full Name</label>
                    <input type="text" name="full_name" required>

                    <label>NIC</label>
                    <input type="text" name="nic" required>

                    <label>Phone</label>
                    <input type="text" name="phone" required>

                    <label>Email</label>
                    <input type="email" name="email">

                    <label>Address</label>
                    <input type="text" name="address">

                    <button type="submit" name="register" class="login-btn">Register</button>
                </form>

                <div class="login-bottom">
                    <a href="index.php">Back to Login</a>
                </div>

            </div>
        </div>

    </div>

</body>
</html>
