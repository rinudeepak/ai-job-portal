<?= view('Layouts/candidate_header', ['title' => 'Company Profile']) ?>
<?php
$brandingPhotos = [];
$brandingPhotosRaw = $company['workplace_photos'] ?? '';
if (is_string($brandingPhotosRaw) && trim($brandingPhotosRaw) !== '') {
    $decodedBrandingPhotos = json_decode($brandingPhotosRaw, true);
    if (is_array($decodedBrandingPhotos)) {
        $brandingPhotos = array_values(array_filter(array_map('strval', $decodedBrandingPhotos)));
    }
}

$benefits = [];
$benefitsRaw = trim((string) ($company['employee_benefits'] ?? ''));
if ($benefitsRaw !== '') {
    $benefits = preg_split('/[\r\n,]+/', $benefitsRaw) ?: [];
    $benefits = array_values(array_filter(array_map('trim', $benefits)));
}
$cultureText = trim((string) ($company['culture_summary'] ?? ''));
if ($cultureText === '') {
    $cultureText = trim((string) ($company['mission_values'] ?? ''));
}
$aboutCompanyText = trim((string) ($company['what_we_do'] ?? ''));
$hasBrandingSection = ($cultureText !== '') || !empty($benefits) || !empty($brandingPhotos);
$officeTourUrl = trim((string) ($company['office_tour_url'] ?? ''));
$officeTourTitle = trim((string) ($company['office_tour_title'] ?? ''));
$officeTourSummary = trim((string) ($company['office_tour_summary'] ?? ''));
if ($officeTourTitle === '') {
    $officeTourTitle = 'Take a Virtual Tour';
}

$averageRating = (float) ($reviewSummary['average_rating'] ?? 0);
$totalReviews = (int) ($reviewSummary['total_reviews'] ?? 0);
$reviewEligibility = is_array($reviewEligibility ?? null) ? $reviewEligibility : ['canInterviewReview' => false, 'canEmployeeReview' => false];
?>

<div class="job-details-jobboard">
    <section class="section-hero overlay inner-page bg-image" style="background-image: url('<?= base_url('jobboard/images/hero_1.jpg') ?>');" id="home-section">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h1 class="text-white font-weight-bold"><?= esc($company['name'] ?? 'Company Profile') ?></h1>
                    <div class="custom-breadcrumbs">
                        <a href="<?= base_url('candidate/dashboard') ?>">Home</a>
                        <span class="mx-2 slash">/</span>
                        <a href="<?= base_url('jobs') ?>">Jobs</a>
                        <span class="mx-2 slash">/</span>
                        <span class="text-white"><strong>Company Profile</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="site-section content-wrap">
        <div class="container">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="mr-3">
                                    <?php if (!empty($company['logo'])): ?>
                                        <img src="<?= base_url($company['logo']) ?>" alt="Company Logo" style="width:72px;height:72px;object-fit:cover;border-radius:10px;border:1px solid #ddd;">
                                    <?php else: ?>
                                        <div style="width:72px;height:72px;border-radius:10px;background:#1f2937;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;">
                                            <?= esc(strtoupper(substr((string) ($company['name'] ?? 'C'), 0, 1))) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h3 class="mb-1"><?= esc($company['name'] ?? 'Company') ?></h3>
                                    <?php if (!empty($company['short_description'])): ?>
                                        <p class="text-muted mb-0"><?= esc($company['short_description']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-2"><strong>Industry:</strong> <?= esc($company['industry'] ?? 'Not specified') ?></div>
                                <div class="col-md-6 mb-2"><strong>Company Size:</strong> <?= esc($company['size'] ?? 'Not specified') ?></div>
                                <div class="col-md-6 mb-2"><strong>HQ:</strong> <?= esc($company['hq'] ?? 'Not specified') ?></div>
                                <div class="col-md-6 mb-2"><strong>Branches:</strong> <?= esc($company['branches'] ?? 'Not specified') ?></div>
                                <div class="col-md-12 mb-2">
                                    <strong>Website:</strong>
                                    <?php if (!empty($company['website'])): ?>
                                        <a href="<?= esc($company['website']) ?>" target="_blank" rel="noopener"><?= esc($company['website']) ?></a>
                                    <?php else: ?>
                                        <span>Not specified</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($aboutCompanyText !== ''): ?>
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5>About Company</h5>
                                <p class="mb-0"><?= nl2br(esc($aboutCompanyText)) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($company['mission_values'])): ?>
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5>Mission / Values</h5>
                                <p class="mb-0"><?= nl2br(esc($company['mission_values'])) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($hasBrandingSection): ?>
                        <div class="card mb-4 employer-branding-company-card">
                            <div class="card-body">
                                <div class="employer-branding-head mb-4">
                                    <div>
                                        <h5 class="mb-1">Life At <?= esc($company['name'] ?? 'This Company') ?></h5>
                                        <p class="mb-0 text-muted">Explore the team environment, employee perks, and the workplace setup.</p>
                                    </div>
                                </div>

                                <div class="employer-branding-grid">
                                    <?php if ($cultureText !== ''): ?>
                                        <div class="employer-branding-card">
                                            <h4>Team Culture</h4>
                                            <p class="mb-0"><?= esc($cultureText) ?></p>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($benefits)): ?>
                                        <div class="employer-branding-card">
                                            <h4>Perks & Benefits</h4>
                                            <div class="branding-benefits-list">
                                                <?php foreach ($benefits as $benefit): ?>
                                                    <span class="branding-benefit-chip"><?= esc($benefit) ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($brandingPhotos)): ?>
                                    <div class="employer-branding-card mt-4">
                                        <div class="branding-gallery-head">
                                            <h4>Office & Team Gallery</h4>
                                            <span><?= count($brandingPhotos) . ' photos' ?></span>
                                        </div>
                                        <div class="branding-photo-grid">
                                            <?php foreach ($brandingPhotos as $photo): ?>
                                                <a href="<?= base_url($photo) ?>" target="_blank" rel="noopener" class="branding-photo-tile">
                                                    <img src="<?= base_url($photo) ?>" alt="<?= esc(($company['name'] ?? 'Company') . ' workplace photo') ?>">
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($officeTourUrl !== ''): ?>
                        <div class="card mb-4">
                            <div class="card-body office-tour-card">
                                <div class="d-flex justify-content-between align-items-start flex-wrap mb-3">
                                    <div>
                                        <h5 class="mb-1">Office Tour</h5>
                                        <p class="text-muted mb-0">Explore the workplace before you apply.</p>
                                    </div>
                                    <span class="badge badge-info">Virtual Tour</span>
                                </div>
                                <h4 class="office-tour-title"><?= esc($officeTourTitle) ?></h4>
                                <?php if ($officeTourSummary !== ''): ?>
                                    <p class="office-tour-summary"><?= esc($officeTourSummary) ?></p>
                                <?php endif; ?>
                                <a href="<?= esc($officeTourUrl) ?>" target="_blank" rel="noopener" class="btn btn-primary">
                                    <i class="fas fa-play-circle mr-1"></i> Watch Tour
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($openJobs)): ?>
                        <div class="card">
                            <div class="card-body">
                                <h5>Open Jobs (<?= (int) $openJobsCount ?>)</h5>
                                <ul class="list-unstyled mb-0">
                                    <?php foreach ($openJobs as $job): ?>
                                        <li class="mb-2">
                                            <a href="<?= base_url('job/' . $job['id']) ?>"><?= esc($job['title']) ?></a>
                                            <span class="text-muted">- <?= esc($job['location']) ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="card mt-4" id="company-reviews">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                <div>
                                    <h5 class="mb-1">Company Reviews</h5>
                                    <p class="text-muted mb-0">Candidate experience and workplace feedback.</p>
                                </div>
                                <div class="company-review-summary">
                                    <strong><?= $totalReviews > 0 ? number_format($averageRating, 1) : 'N/A' ?></strong>
                                    <span><?= $totalReviews ?> review<?= $totalReviews === 1 ? '' : 's' ?></span>
                                </div>
                            </div>

                            <?php if (!empty($reviews)): ?>
                                <div class="company-review-list">
                                    <?php foreach ($reviews as $review): ?>
                                        <article class="company-review-item">
                                            <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                                <div>
                                                    <h6 class="mb-1"><?= esc($review['headline'] ?: 'Candidate Review') ?></h6>
                                                    <small class="text-muted">
                                                        <?= esc($review['candidate_name'] ?: 'Candidate') ?> | <?= date('M d, Y', strtotime((string) ($review['updated_at'] ?? $review['created_at'] ?? 'now'))) ?>
                                                    </small>
                                                    <div class="mt-1">
                                                        <?php $reviewType = (string) ($review['review_type'] ?? 'interview'); ?>
                                                        <?php if ($reviewType === 'employee'): ?>
                                                            <span class="badge badge-success">Verified Employee</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-info">Verified Interview Candidate</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <span class="company-review-rating">
                                                    <?= str_repeat('*', max(0, min(5, (int) ($review['rating'] ?? 0)))) ?>
                                                </span>
                                            </div>
                                            <p class="mb-2"><?= nl2br(esc($review['review_text'] ?? '')) ?></p>
                                            <div class="company-review-pros-cons">
                                                <?php if (!empty($review['pros'])): ?>
                                                    <div><strong>Pros:</strong> <?= esc($review['pros']) ?></div>
                                                <?php endif; ?>
                                                <?php if (!empty($review['cons'])): ?>
                                                    <div><strong>Cons:</strong> <?= esc($review['cons']) ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </article>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="mb-0 text-muted">No reviews yet. Be the first candidate to write one.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <?php if ((string) session()->get('role') === 'candidate'): ?>
                        <div class="card mb-4" id="write-review">
                            <div class="card-body">
                                <h5 class="mb-1"><?= !empty($currentUserReview) ? 'Update Your Review' : 'Write a Review' ?></h5>
                                <p class="text-muted small mb-3">Interview reviews are default. Employee reviews require selected/hired status.</p>

                                <?php if (!($reviewEligibility['canInterviewReview'] ?? false)): ?>
                                    <div class="alert alert-warning py-2">
                                        You can write a review after applying/interviewing with this company.
                                    </div>
                                <?php endif; ?>

                                <form method="post" action="<?= base_url('company/' . (int) ($company['id'] ?? 0) . '/review') ?>">
                                    <?= csrf_field() ?>
                                    <?php $selectedReviewType = (string) old('review_type', $currentUserReview['review_type'] ?? 'interview'); ?>
                                    <div class="form-group">
                                        <label for="review_type">Review Type</label>
                                        <select name="review_type" id="review_type" class="form-control" required>
                                            <option value="interview" <?= $selectedReviewType === 'interview' ? 'selected' : '' ?> <?= !($reviewEligibility['canInterviewReview'] ?? false) ? 'disabled' : '' ?>>
                                                Interview Experience
                                            </option>
                                            <option value="employee" <?= $selectedReviewType === 'employee' ? 'selected' : '' ?> <?= !($reviewEligibility['canEmployeeReview'] ?? false) ? 'disabled' : '' ?>>
                                                Work Experience (Employee)
                                            </option>
                                        </select>
                                        <small class="text-muted d-block mt-1">
                                            Employee reviews are available only for selected/hired candidates.
                                        </small>
                                    </div>

                                    <div class="form-group">
                                        <label for="review_rating">Overall Rating</label>
                                        <select name="rating" id="review_rating" class="form-control" required>
                                            <option value="">Select rating</option>
                                            <?php $selectedRating = old('rating', $currentUserReview['rating'] ?? ''); ?>
                                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                                <option value="<?= $i ?>" <?= (string) $selectedRating === (string) $i ? 'selected' : '' ?>><?= $i ?> / 5</option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="review_headline">Headline</label>
                                        <input type="text" name="headline" id="review_headline" class="form-control" maxlength="180" value="<?= esc(old('headline', $currentUserReview['headline'] ?? '')) ?>" placeholder="e.g. Strong interview process and clear communication" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="review_text">Review</label>
                                        <textarea name="review_text" id="review_text" class="form-control" rows="4" placeholder="Describe your experience with this company." required><?= esc(old('review_text', $currentUserReview['review_text'] ?? '')) ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="review_pros">Pros</label>
                                        <textarea name="pros" id="review_pros" class="form-control" rows="2" placeholder="What stood out positively?"><?= esc(old('pros', $currentUserReview['pros'] ?? '')) ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="review_cons">Cons</label>
                                        <textarea name="cons" id="review_cons" class="form-control" rows="2" placeholder="Anything candidates should know?"><?= esc(old('cons', $currentUserReview['cons'] ?? '')) ?></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-block" <?= !($reviewEligibility['canInterviewReview'] ?? false) ? 'disabled' : '' ?>>
                                        <?= !empty($currentUserReview) ? 'Update Review' : 'Publish Review' ?>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ((int) ($company['contact_public'] ?? 0) === 1): ?>
                        <div class="card">
                            <div class="card-body">
                                <h5>Contact</h5>
                                <p class="mb-2"><strong>Email:</strong> <?= esc($company['contact_email'] ?: 'Not specified') ?></p>
                                <p class="mb-0"><strong>Phone:</strong> <?= esc($company['contact_phone'] ?: 'Not specified') ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<?= view('Layouts/candidate_footer') ?>
