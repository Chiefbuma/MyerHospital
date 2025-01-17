<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style2.css">
</head>

<body>
    <div class="wrapper">
        <h1>Login</h1>
        <form method="POST" class="user" action="loginuser.php">
            <div class="form-group">
                <label for="email-input">@</label>
                <input type="email" name="email" id="email-input" placeholder="Email" required>
            </div>
            <div class="form-group">
                <label for="password-input">🔒</label>
                <input type="password" name="password" id="password-input" placeholder="Password" required>
            </div>
            <?php
            if (isset($_SESSION['message'])) {
                echo "<center><label class='text-danger'>" . $_SESSION['message'] . "</label></center>";
                unset($_SESSION['message']); // Clear message after displaying
            }
            ?>
            <button type="submit" class="btn btn-primary btn-user btn-block" name="login">Login</button>
        </form>


    </div>
</body>

</html>