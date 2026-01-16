<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Recruiter Registration</title>

    <!-- Bootstrap CDN -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-5">

            <div class="card shadow">
                <div class="card-body">

                    <h4 class="text-center mb-4">Recruiter Registration</h4>

                    <!-- ERROR MESSAGE Start-->
                    <?php if (session()->getFlashdata('error')) : ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                    <!-- ERROR MESSAGE End-->
                     
                    <!-- Registration Form -->
                    <form method="post" action="<?= base_url('recruiter/register') ?>">

                        <div class="mb-3">
                            <label>Company Name</label>
                            <input type="text" name="company_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Recruiter Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>


                        <button type="submit" class="btn btn-primary w-100">
                            Register as Recruiter
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

</body>
</html>
