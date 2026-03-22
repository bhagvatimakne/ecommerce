<?php include "../config/db.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Create account</title>
        <link rel="stylesheet" href="../assets/css/register.css">
        </head>
<body>

<div class="register-wrapper">
    <div class="card">
        <div class="brand">
            <h1>WowLady</h1>
        </div>
        <p class="lead">Create a new account to start shopping.</p>

        <form method="POST" action="../api/register.php">
            <div class="grid form-grid">
                <div class="field full">
                    <input type="text" name="name" placeholder="Full name" required>
                </div>

                <div class="field">
                    <input type="email" name="email" placeholder="Email address" required>
                </div>

                <div class="field">
                    <input type="text" name="phone" placeholder="Phone (optional)">
                </div>

                <div class="field">
                    <input type="password" name="password" placeholder="Password" required>
                </div>

            </div>

            <div class="field">
                <button type="submit" class="btn">Create account</button>
            </div>
        </form>

        <div class="meta">Already have an account? <a href="login.php">Sign in</a></div>
    </div>
</div>

</body>
</html>
