<!DOCTYPE html>
<html>
<head>
    <title>Candidate Dashboard</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
        }

        .header {
            background: #2c3e50;
            color: #fff;
            padding: 15px 30px;
        }

        .container {
            padding: 30px;
        }

        .card {
            background: #fff;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .stats {
            display: flex;
            gap: 20px;
        }

        .stat-box {
            flex: 1;
            background: #3498db;
            color: #fff;
            padding: 20px;
            border-radius: 6px;
        }

        .stat-box h3 {
            margin: 0;
            font-size: 28px;
        }

        .stat-box p {
            margin: 5px 0 0;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            background: #27ae60;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
        }

        .btn.logout {
            background: #e74c3c;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
    </style>
</head>

<body>

<div class="header">
    <h2>Candidate Dashboard</h2>
</div>

<div class="container">

    <!-- Stats -->
    <div class="stats">
        <div class="stat-box">
            <h3>5</h3>
            <p>Jobs Applied</p>
        </div>

        <div class="stat-box">
            <h3>2</h3>
            <p>Interviews Pending</p>
        </div>

        <div class="stat-box">
            <h3>1</h3>
            <p>Interviews Completed</p>
        </div>
    </div>

    <!-- Profile Card -->
    <div class="card">
        <h3>My Profile</h3>
        <p><strong>Name:</strong> <?= session()->get('name') ?></p>
        <p><strong>Email:</strong> <?= session()->get('email') ?></p>

        <a href="#" class="btn">Update Profile</a>
    </div>

    <!-- Job List -->
    <div class="card">
        <h3>Available Jobs</h3>

        <table>
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Skills</th>
                    <th>Experience</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>PHP Developer</td>
                    <td>PHP, MySQL, CodeIgniter</td>
                    <td>2+ Years</td>
                    <td><a href="#" class="btn">Apply</a></td>
                </tr>

                <tr>
                    <td>Frontend Developer</td>
                    <td>HTML, CSS, JS</td>
                    <td>1+ Year</td>
                    <td><a href="#" class="btn">Apply</a></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Logout -->
    <a href="<?= base_url('logout') ?>" class="btn logout">Logout</a>

</div>

</body>
</html>
