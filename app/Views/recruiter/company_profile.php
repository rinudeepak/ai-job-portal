<?= view('Layouts/recruiter_header', ['title' => 'Company Profile']) ?>

<section class="site-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-3">Edit Company Profile</h4>
                        <form method="post" action="<?= base_url('recruiter/company-profile') ?>" enctype="multipart/form-data">
                            <?= csrf_field() ?>

                            <div class="form-group">
                                <label>Company Name</label>
                                <input type="text" name="company_name" class="form-control" required value="<?= esc(old('company_name', $company['company_name'] ?? '')) ?>">
                            </div>

                            <div class="form-group">
                                <label>Company Logo</label>
                                <input type="file" name="company_logo" class="form-control" accept="image/*">
                                <?php if (!empty($company['logo'])): ?>
                                    <small class="d-block mt-2">Current: <a href="<?= base_url($company['logo']) ?>" target="_blank">View logo</a></small>
                                    <button type="submit" name="delete_logo" value="1" class="btn btn-sm btn-outline-danger mt-2" onclick="return confirm('Remove current company logo?')">
                                        Delete Logo
                                    </button>
                                <?php endif; ?>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Website</label>
                                    <input type="url" name="company_website" class="form-control" placeholder="https://example.com" value="<?= esc(old('company_website', $company['website'] ?? '')) ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Industry</label>
                                    <input type="text" name="company_industry" class="form-control" value="<?= esc(old('company_industry', $company['industry'] ?? '')) ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Company Size</label>
                                    <select name="company_size" class="form-control">
                                        <?php $size = old('company_size', $company['size'] ?? ''); ?>
                                        <option value="">Select</option>
                                        <option value="1-10" <?= $size === '1-10' ? 'selected' : '' ?>>1-10</option>
                                        <option value="10-50" <?= $size === '10-50' ? 'selected' : '' ?>>10-50</option>
                                        <option value="50-200" <?= $size === '50-200' ? 'selected' : '' ?>>50-200</option>
                                        <option value="200-500" <?= $size === '200-500' ? 'selected' : '' ?>>200-500</option>
                                        <option value="500-1000" <?= $size === '500-1000' ? 'selected' : '' ?>>500-1000</option>
                                        <option value="1000+" <?= $size === '1000+' ? 'selected' : '' ?>>1000+</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>HQ Location</label>
                                    <input type="text" name="company_hq" class="form-control" value="<?= esc(old('company_hq', $company['hq'] ?? '')) ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Branch Locations</label>
                                <textarea name="company_branches" class="form-control" rows="2" placeholder="City A, City B"><?= esc(old('company_branches', $company['branches'] ?? '')) ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>Short Description</label>
                                <input type="text" name="company_short_description" class="form-control" value="<?= esc(old('company_short_description', $company['short_description'] ?? '')) ?>">
                            </div>

                            <div class="form-group">
                                <label>About Company</label>
                                <textarea name="company_what_we_do" class="form-control" rows="4"><?= esc(old('company_what_we_do', $company['what_we_do'] ?? '')) ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>Mission / Values (Optional)</label>
                                <textarea name="company_mission_values" class="form-control" rows="3"><?= esc(old('company_mission_values', $company['mission_values'] ?? '')) ?></textarea>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>HR / Support Email</label>
                                    <input type="email" name="company_contact_email" class="form-control" value="<?= esc(old('company_contact_email', $company['contact_email'] ?? '')) ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Phone (Optional)</label>
                                    <input type="text" name="company_contact_phone" class="form-control" value="<?= esc(old('company_contact_phone', $company['contact_phone'] ?? '')) ?>">
                                </div>
                            </div>

                            <div class="form-group form-check">
                                <?php $visible = (int) old('company_contact_public', (string) ($company['contact_public'] ?? 0)); ?>
                                <input type="checkbox" class="form-check-input" id="contactPublic" name="company_contact_public" value="1" <?= $visible === 1 ? 'checked' : '' ?>>
                                <label class="form-check-label" for="contactPublic">Show contact info publicly to candidates</label>
                            </div>

                            <button type="submit" class="btn btn-primary">Save Company Profile</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= view('Layouts/recruiter_footer') ?>
