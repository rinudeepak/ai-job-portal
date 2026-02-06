<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Candidate Registration</title>

    <!-- Bootstrap CDN -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>

<body class="bg-light">

    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-5">

                <div class="card shadow">
                    <div class="card-body">

                        <h4 class="text-center mb-4">Candidate Registration</h4>

                        <!-- ERROR MESSAGE Start-->
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger">
                                <?= session()->getFlashdata('error') ?>
                            </div>
                        <?php endif; ?>
                        <!-- ERROR MESSAGE End-->

                        <!-- Registration Form -->
                        <form method="post" action="<?= base_url('register') ?>">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label>Full Name</label>
                                <input type="text" name="name" class="form-control" value="<?= old('name') ?>" required>
                                <?php if(session('validation') && session('validation')->hasError('name')): ?>
                                    <small class="text-danger"><?= session('validation')->getError('name') ?></small>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label>Email Address</label>
                                <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
                                <?php if(session('validation') && session('validation')->hasError('email')): ?>
                                    <small class="text-danger"><?= session('validation')->getError('email') ?></small>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label>Phone</label>
                                <input type="tel" name="phone" class="form-control" value="<?= old('phone') ?>" required>
                                <?php if(session('validation') && session('validation')->hasError('phone')): ?>
                                    <small class="text-danger"><?= session('validation')->getError('phone') ?></small>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label>Password</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password" class="form-control" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                        <span id="eyeIcon">👁️</span>
                                    </button>
                                </div>
                                <?php if(session('validation') && session('validation')->hasError('password')): ?>
                                    <small class="text-danger"><?= session('validation')->getError('password') ?></small>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label>Confirm Password</label>
                                <div class="input-group">
                                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="toggleConfirmPassword()">
                                        <span id="eyeIcon2">👁️</span>
                                    </button>
                                </div>
                                <?php if(session('validation') && session('validation')->hasError('confirm_password')): ?>
                                    <small class="text-danger"><?= session('validation')->getError('confirm_password') ?></small>
                                <?php endif; ?>
                            </div>

                            <button type="submit" class="btn btn-success w-100">
                                Register
                            </button>

                        </form>

                        <p class="text-center mt-3">
                            Already have an account?
                            <a href="<?= base_url('login') ?>">Login</a>
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
    pwd.type = pwd.type === 'password' ? 'text' : 'password';
    icon.textContent = pwd.type === 'password' ? '👁️' : '🙈';
}
function toggleConfirmPassword() {
    const pwd = document.getElementById('confirm_password');
    const icon = document.getElementById('eyeIcon2');
    pwd.type = pwd.type === 'password' ? 'text' : 'password';
    icon.textContent = pwd.type === 'password' ? '👁️' : '🙈';
}
</script>
</body>

</html>