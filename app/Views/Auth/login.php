
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-4">

            <div class="card shadow">
                <div class="card-body">

                    <h4 class="text-center mb-4">Login</h4>
                    <!-- ERROR MESSAGE Start-->
                    <?php if (session()->getFlashdata('error')) : ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                    <!-- ERROR MESSAGE End-->
                    <!-- Login Form -->
                    <form method="post" action="<?= base_url('login') ?>">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                    <span id="eyeIcon">👁️</span>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Login
                        </button>

                    </form>

                    <p class="text-center mt-3">
                        New candidate?
                        <a href="<?= base_url('register') ?>">Register here</a>
                    </p>

                </div>
            </div>

        </div>
    </div>
</div>

<script>
function togglePassword() {
    const pwd = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');
    if (pwd.type === 'password') {
        pwd.type = 'text';
        icon.textContent = '🙈';
    } else {
        pwd.type = 'password';
        icon.textContent = '👁️';
    }
}
</script>
</body>
</html>
