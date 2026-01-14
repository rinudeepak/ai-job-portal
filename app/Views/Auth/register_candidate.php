<!DOCTYPE html>
<html>

<head>
    <title>Candidate Registration</title>
</head>

<body>

    <h2>Candidate Registration</h2>

    <form method="post" action="<?= base_url('register') ?>">
        <input type="text" name="name" placeholder="Full Name" required><br><br>
        <input type="email" name="email" placeholder="Email" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        
        <button type="submit">Register</button>
    </form>

    <p>
        Already have an account? <a href="<?= base_url('login') ?>">Login</a>
    </p>

</body>

</html>
