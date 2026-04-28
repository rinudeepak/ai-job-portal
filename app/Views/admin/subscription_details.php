<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <?= view('Layouts/styles') ?>
</head>
<body>
<div class="container py-4">
    <?= view('Layouts/header') ?>
    <div class="mb-4">
        <a href="<?= base_url('admin/subscriptions') ?>" class="btn btn-sm btn-outline-secondary">&larr; Back to List</a>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold">Detailed Subscription Information</div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">Subscription ID</th>
                            <td>#<?= esc($subscription['id']) ?></td>
                        </tr>
                        <tr>
                            <th>User</th>
                            <td><?= esc($subscription['user_name']) ?> (<?= esc($subscription['user_email']) ?>)</td>
                        </tr>
                        <tr>
                            <th>Plan Name</th>
                            <td><span class="badge bg-primary"><?= esc($subscription['plan_name'] ?: 'N/A') ?></span></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td><span class="badge bg-<?= $subscription['status'] === 'active' ? 'success' : 'danger' ?>"><?= strtoupper(esc($subscription['status'])) ?></span></td>
                        </tr>
                        <tr>
                            <th>Validity</th>
                            <td><?= esc($subscription['start_date']) ?> to <?= esc($subscription['end_date']) ?></td>
                        </tr>
                        <tr>
                            <th>Amount Paid</th>
                            <td class="fw-bold text-success">₹<?= number_format($subscription['amount_paid'], 2) ?></td>
                        </tr>
                        <tr>
                            <th>Transaction References</th>
                            <td>
                                <div class="small text-muted">Order: <?= esc($subscription['order_id'] ?: 'N/A') ?></div>
                                <div class="small text-muted">Payment: <?= esc($subscription['payment_id'] ?: 'N/A') ?></div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= view('Layouts/footer') ?>
</body></html>