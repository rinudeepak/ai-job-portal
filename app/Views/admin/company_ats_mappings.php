<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company ATS Mappings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
     <?= view('Layouts/styles') ?>
</head>
<body>
<div class="container py-4">
     <?= view('Layouts/header') ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="text-uppercase text-muted small fw-semibold">Admin</div>
            <h2 class="mb-0">Company ATS Mappings</h2>
            <div class="text-muted">Store the official careers source once, then reuse it for company search and job import.</div>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-secondary">Dashboard</a>
            <a href="<?= base_url('admin/logout') ?>" class="btn btn-outline-danger">Logout</a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc((string) session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc((string) session()->getFlashdata('error')) ?></div>
    <?php endif; ?>
    <?php if (!empty($warning)): ?>
        <div class="alert alert-warning"><?= esc((string) $warning) ?></div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="fw-semibold">Bulk import from CSV</div>
                <div class="text-muted small">Upload a CSV with headers like `company_name`, `platform`, `career_url`, and `website_url`.</div>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="<?= esc((string) ($importTemplateUrl ?? '#')) ?>" class="btn btn-outline-secondary">Download template</a>
                <form method="post" action="<?= base_url('admin/company-ats-mappings/import') ?>" enctype="multipart/form-data" class="d-flex gap-2 align-items-center">
                    <?= csrf_field() ?>
                    <input type="file" name="csv_file" class="form-control" accept=".csv,.txt" required>
                    <button type="submit" class="btn btn-primary">Import CSV</button>
                </form>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="post" action="<?= base_url('admin/company-ats-mappings/save') ?>" class="row g-3">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= esc((string) ($editing['id'] ?? '')) ?>">
                <div class="col-lg-3">
                    <label class="form-label">Company Name</label>
                    <input type="text" name="company_name" class="form-control" value="<?= esc((string) ($editing['company_name'] ?? '')) ?>" required>
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Company Key</label>
                    <input type="text" name="company_key" class="form-control" value="<?= esc((string) ($editing['company_key'] ?? '')) ?>" placeholder="auto-filled if empty">
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Platform</label>
                    <select name="platform" class="form-select" required>
                        <?php
                        $platform = (string) ($editing['platform'] ?? '');
                        $platformOptions = ['generic', 'greenhouse', 'lever', 'workday', 'smartrecruiters', 'taleo', 'successfactors', 'icims', 'tcs'];
                        ?>
                        <?php foreach ($platformOptions as $option): ?>
                            <option value="<?= esc($option) ?>" <?= $platform === $option ? 'selected' : '' ?>><?= esc(ucfirst($option)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Platform Slug</label>
                    <input type="text" name="platform_slug" class="form-control" value="<?= esc((string) ($editing['platform_slug'] ?? '')) ?>">
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Career URL</label>
                    <input type="url" name="career_url" class="form-control" value="<?= esc((string) ($editing['career_url'] ?? '')) ?>">
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Website URL</label>
                    <input type="url" name="website_url" class="form-control" value="<?= esc((string) ($editing['website_url'] ?? '')) ?>">
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Aliases</label>
                    <input type="text" name="aliases" class="form-control" value="<?= esc((string) ($editing['aliases'] ?? '')) ?>" placeholder="Comma or newline separated">
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Priority</label>
                    <input type="number" name="priority" class="form-control" value="<?= esc((string) ($editing['priority'] ?? 100)) ?>" min="1" max="999">
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Verified At</label>
                    <input type="text" name="last_verified_at" class="form-control" value="<?= esc((string) ($editing['last_verified_at'] ?? '')) ?>" placeholder="YYYY-mm-dd HH:ii:ss">
                </div>
                <div class="col-lg-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="2" class="form-control"><?= esc((string) ($editing['notes'] ?? '')) ?></textarea>
                </div>
                <div class="col-lg-12 d-flex align-items-center gap-3">
                    <div class="form-check">
                        <input type="checkbox" name="is_enabled" id="is_enabled" class="form-check-input" value="1" <?= !empty($editing) ? (((int) ($editing['is_enabled'] ?? 1) === 1) ? 'checked' : '') : 'checked' ?>>
                        <label for="is_enabled" class="form-check-label">Enabled</label>
                    </div>
                    <button type="submit" class="btn btn-primary"><?= !empty($editing) ? 'Update Mapping' : 'Add Mapping' ?></button>
                    <?php if (!empty($editing)): ?>
                        <a href="<?= base_url('admin/company-ats-mappings') ?>" class="btn btn-outline-secondary">Cancel edit</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Saved mappings</strong>
            <span class="text-muted small"><?= count($mappings ?? []) ?> total</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Company</th>
                        <th>Platform</th>
                        <th>Careers URL</th>
                        <th>Website</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($mappings)): ?>
                        <tr><td colspan="7" class="text-muted p-4">No mappings saved yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($mappings as $row): ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold"><?= esc((string) ($row['company_name'] ?? '')) ?></div>
                                    <div class="text-muted small"><?= esc((string) ($row['company_key'] ?? '')) ?></div>
                                    <?php if (!empty($row['aliases'])): ?>
                                        <div class="text-muted small">Aliases: <?= esc((string) $row['aliases']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><span class="code-pill"><?= esc((string) ($row['platform'] ?? '')) ?></span></td>
                                <td>
                                    <?php if (!empty($row['career_url'])): ?>
                                        <a href="<?= esc((string) $row['career_url']) ?>" target="_blank" rel="noopener noreferrer"><?= esc((string) $row['career_url']) ?></a>
                                    <?php else: ?>
                                        <span class="text-muted">Not set</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($row['website_url'])): ?>
                                        <a href="<?= esc((string) $row['website_url']) ?>" target="_blank" rel="noopener noreferrer"><?= esc((string) $row['website_url']) ?></a>
                                    <?php else: ?>
                                        <span class="text-muted">Not set</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc((string) ((int) ($row['priority'] ?? 100))) ?></td>
                                <td>
                                    <?php if ((int) ($row['is_enabled'] ?? 0) === 1): ?>
                                        <span class="badge bg-success">Enabled</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Disabled</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-primary" href="<?= base_url('admin/company-ats-mappings?edit=' . (int) ($row['id'] ?? 0)) ?>">Edit</a>
                                    <a class="btn btn-sm btn-outline-danger" href="<?= base_url('admin/company-ats-mappings/delete/' . (int) ($row['id'] ?? 0)) ?>" onclick="return confirm('Delete this mapping?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= view('Layouts/footer') ?>
</body>
</html>
