<?php

//  Login (index.php)


//   * Member : Member Code + NIC
//   * Staff  : Username + password 


include "includes/db_connect.php";

if (!isset($_POST['login'])) {
    if (isset($_SESSION['admin'])) {
        header("Location: dashboard.php");
        exit();
    }
    if (isset($_SESSION['member_id'])) {
        header("Location: user_dashboard.php");
        exit();
    }
}

$error = "";

if (isset($_POST['login'])) {
    $login_tab = $_POST['tab'];

    // ------------------------------------------------------------------
    // Staff / Admin login
    // ------------------------------------------------------------------
    if ($login_tab == "staff") {
        $user = $_POST['username'];
        $pass = $_POST['password'];

        // Built-in admin account.
        if ($user == "admin" && $pass == "admin123") {
            $_SESSION['admin'] = $user;
            $_SESSION['name']  = "Admin";
            header("Location: dashboard.php");
            exit();
        }

        $staff_check = mysqli_query(
            $conn,
            "SELECT * FROM staff WHERE username = '$user' AND password = '$pass'"
        );

        if (mysqli_num_rows($staff_check) > 0) {
            $staff_data        = mysqli_fetch_assoc($staff_check);
            $_SESSION['admin'] = $user;
            $_SESSION['name']  = $staff_data['full_name'];
            header("Location: dashboard.php");
            exit();
        }

        $error = "Invalid username or password!";
    }

    // ------------------------------------------------------------------
    // Member login
    // ------------------------------------------------------------------
    else {
        $member_code = $_POST['member_code'];
        $member_nic  = $_POST['nic'];

        $member_check = mysqli_query(
            $conn,
            "SELECT * FROM members WHERE member_code = '$member_code' AND nic = '$member_nic'"
        );

        if (mysqli_num_rows($member_check) > 0) {
            $member_data           = mysqli_fetch_assoc($member_check);
            $_SESSION['member_id'] = $member_data['member_id'];
            $_SESSION['name']      = $member_data['full_name'];
            header("Location: user_dashboard.php");
            exit();
        }

        $error = "Wrong Member Code or NIC!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library MS - Login</title>
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
                <h2>LOGIN</h2>

                <div class="login-tabs">
                    <button type="button" id="memberTab" class="active-tab" onclick="showTab('member')">Member</button>
                    <button type="button" id="staffTab" onclick="showTab('staff')">Staff</button>
                </div>

                <?php if ($error != "") : ?>
                    <p style="color: red; text-align: center;"><?php echo $error; ?></p>
                <?php endif; ?>

                <form method="POST">

                    <!-- Member login fields -->
                    <div id="memberBox">
                        <label>Member Code</label>
                        <input type="text" name="member_code" placeholder="Enter Member Code">

                        <label>NIC</label>
                        <input type="text" name="nic" placeholder="Enter NIC" autocomplete="off">
                    </div>

                    <!-- Staff login fields -->
                    <div id="staffBox" style="display: none;">
                        <label>Username</label>
                        <input type="text" name="username" placeholder="Enter username">

                        <label>Password</label>
                        <input type="password" name="password" placeholder="Enter password">
                    </div>

                    <input type="hidden" name="tab" id="tab" value="member">
                    <button type="submit" name="login" class="login-btn">Login</button>

                </form>

                <div class="login-bottom" id="regLink">
                    Don't have an account?<br>
                    <a href="register.php">Register</a>
                </div>

            </div>
        </div>

    </div>

    <script>
        function showTab(tab) {
            var isMember = (tab == 'member');
             document.getElementById('tab').value = tab;
             document.getElementById('memberBox').style.display = isMember ? 'block' : 'none';
             document.getElementById('staffBox').style.display = isMember ? 'none' : 'block';
             document.getElementById('regLink').style.display = isMember ? 'block' : 'none';
             document.getElementById('memberTab').className = isMember ? 'active-tab' : '';
             document.getElementById('staffTab').className = isMember ? '' : 'active-tab';
        }
    </script>

</body>
</html>
