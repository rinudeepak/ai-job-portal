<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Analytics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Admin Analytics</h3>
            <div class="d-flex gap-2">
                <form method="get" action="<?= base_url('admin/dashboard') ?>" class="d-flex align-items-center gap-2">
                    <label for="daysRange" class="form-label mb-0 text-muted small">Show last</label>
                    <div class="input-group" style="width: 180px;">
                        <input
                            type="number"
                            min="1"
                            max="60"
                            class="form-control"
                            id="daysRange"
                            name="days"
                            value="<?= esc((string) $days) ?>"
                            aria-label="Number of days">
                        <span class="input-group-text">days</span>
                    </div>
                    <button type="submit" class="btn btn-outline-secondary">Update range</button>
                </form>
                <a class="btn btn-outline-danger" href="<?= base_url('admin/logout') ?>">Logout</a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">API Calls (All Time)</div>
                        <div class="fs-4 fw-semibold"><?= esc((string) ($apiTotals['total_calls'] ?? 0)) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">Cost Consumed (USD, All Time)</div>
                        <div class="fs-4 fw-semibold">$<?= esc(number_format((float) ($apiTotals['total_cost'] ?? 0), 4)) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">Tracked Days</div>
                        <div class="fs-4 fw-semibold"><?= esc((string) $days) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header">No. of Users by Day (Distinct Logins)</div>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead><tr><th>Date</th><th>Users</th></tr></thead>
                            <tbody>
                                <?php if (empty($dailyUsers)): ?>
                                    <tr><td colspan="2" class="text-muted">No data yet</td></tr>
                                <?php else: ?>
                                    <?php foreach ($dailyUsers as $row): ?>
                                        <tr>
                                            <td><?= esc((string) ($row['day'] ?? '')) ?></td>
                                            <td><?= esc((string) ((int) ($row['users_count'] ?? 0))) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header">API Calls and Cost by Day</div>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead><tr><th>Date</th><th>Calls</th><th>Cost (USD)</th></tr></thead>
                            <tbody>
                                <?php if (empty($dailyApi)): ?>
                                    <tr><td colspan="3" class="text-muted">No data yet</td></tr>
                                <?php else: ?>
                                    <?php foreach ($dailyApi as $row): ?>
                                        <tr>
                                            <td><?= esc((string) ($row['day'] ?? '')) ?></td>
                                            <td><?= esc((string) ((int) ($row['calls_count'] ?? 0))) ?></td>
                                            <td>$<?= esc(number_format((float) ($row['cost_usd'] ?? 0), 4)) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header">API Usage by Provider (Selected Range)</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Provider</th><th>Calls</th><th>Cost (USD)</th></tr></thead>
                    <tbody>
                        <?php if (empty($providerBreakdown ?? [])): ?>
                            <tr><td colspan="3" class="text-muted">No data yet</td></tr>
                        <?php else: ?>
                            <?php foreach (($providerBreakdown ?? []) as $row): ?>
                                <tr>
                                    <td><?= esc((string) ($row['provider'] ?? 'unknown')) ?></td>
                                    <td><?= esc((string) ((int) ($row['calls_count'] ?? 0))) ?></td>
                                    <td>$<?= esc(number_format((float) ($row['cost_usd'] ?? 0), 4)) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header">First Page Login Duration by User (Latest 100)</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Login At</th>
                            <th>User</th>
                            <th>Role</th>
                            <th>First Page</th>
                            <th>Duration (ms)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($firstPageDurations)): ?>
                            <tr><td colspan="5" class="text-muted">No data yet</td></tr>
                        <?php else: ?>
                            <?php foreach ($firstPageDurations as $row): ?>
                                <tr>
                                    <td><?= esc((string) ($row['login_at'] ?? '')) ?></td>
                                    <td><?= esc((string) ($row['user_name'] ?: $row['user_email'] ?: ('User #' . ($row['user_id'] ?? '')))) ?></td>
                                    <td><?= esc((string) ($row['user_role'] ?? '')) ?></td>
                                    <td><?= esc((string) ($row['first_page_path'] ?? '')) ?></td>
                                    <td><?= esc((string) ((int) ($row['duration_ms'] ?? 0))) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
