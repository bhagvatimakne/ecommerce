<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Login</title>
        <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>

<div class="login-wrapper">
    <div class="card">
        <div class="brand">
            <h1>WowLady</h1>
        </div>
        <p class="lead">Welcome back — please sign in to your account</p>

        <form method="POST" action="../api/login.php">
            <div class="field">
                <input type="email" name="email" placeholder="Email address" required>
            </div>

            <div class="field">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <div class="actions">
                <label class="remember"><input type="checkbox" name="remember"> Remember me</label>
            </div>

            <div class="field">
                <button type="submit" class="btn">Sign in</button>
            </div>
        </form>

        <div class="meta">Don't have an account? <a href="../public/register.php">Create one</a></div>
    </div>
</div>

</body>
</html>
