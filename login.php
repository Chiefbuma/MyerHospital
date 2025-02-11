<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style2.css">
    <style>
        /* Add styling for the timeout message */
        .timeout-message {
            background-color: #ffcc00;
            /* Yellow background for visibility */
            color: #ff0000;
            /* Red text color */
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <h1>Login</h1>

        <?php
        // Display the session timeout message if the parameter is passed
        if (isset($_GET['timeout'])) {
            echo "<div class='timeout-message'>Your session has expired due to inactivity. Please log in again.</div>";
        }

        session_start();
        if (isset($_SESSION['message'])) {
            echo "<script>alert('" . htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8') . "');</script>";
            unset($_SESSION['message']); // Clear message after displaying
        }
        ?>

        <form method="POST" class="user" action="loginuser.php">
            <div class="form-group">
                <label for="email-input">@</label>
                <input type="email" name="email" id="email-input" placeholder="Email" required>
            </div>
            <div class="form-group">
                <label for="password-input">🔒</label>
                <input type="password" name="password" id="password-input" placeholder="Password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-user btn-block" name="login">Login</button>
        </form>

    </div>
</body>

</html>