<?php

?>
<div class="sidebar">
    <h2>Library</h2>

    <?php if (isset($_SESSION['member_id'])) : ?>

        <!-- Member menu -->
        <a href="user_dashboard.php">Dashboard</a>
        <a href="user_books.php">Browse Books</a>
        <a href="user_my_books.php">My Books</a>
        <a href="logout.php" class="logout">Logout</a>

    <?php else : ?>

        <!-- Staff / Admin menu -->
        <a href="dashboard.php">Dashboard</a>
        <a href="books.php">Books</a>
        <a href="members.php">Members</a>
        <a href="issue.php">Issue</a>
        <a href="return.php">Return</a>
        <a href="blacklist.php">Blacklist</a>
        <a href="fees.php">Fees</a>
        <a href="manage_staff.php">Staff</a>
        <a href="logout.php" class="logout">Logout</a>

    <?php endif; ?>
</div>
