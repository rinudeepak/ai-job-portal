<!DOCTYPE html>
<html>
<head>
    <title>Admin Registration</title>
</head>
<body>

<h2>Admin Registration</h2>

<form method="post" action="<?= base_url('admin/register'); ?>">
    <input type="text" name="name" placeholder="Admin Name" required><br><br>
    <input type="email" name="email" placeholder="Email" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>
    
    <button type="submit">Register Admin</button>
</form>

</body>
</html>
