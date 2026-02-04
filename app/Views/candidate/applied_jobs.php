<?= view('Layouts/candidate_header', ['title' => 'Applied Jobs']) ?>

<!-- Hero Area Start-->
<div class="slider-area ">
    <div class="single-slider section-overly slider-height2 d-flex align-items-center"
        data-background="<?= base_url('assets/img/hero/about.jpg') ?>">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="hero-cap text-center">
                        <h2>Applied Jobs</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Hero Area End -->

<section class="contact-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="contact-title">Applied Jobs</h2>
            </div>

            <div class="col-lg-12">

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php if (!empty($jobs)): ?>
                            <?php foreach ($jobs as $job): ?>
                                <tr>
                                    <td><?= esc($job['title']) ?></td>

                                    <td>
                                        <?php
                                        $statusLabels = [
                                            'applied' => 'Applied',
                                            'ai_interview_started' => 'AI Interview In Progress',
                                            'ai_interview_completed' => 'AI Interview Completed',
                                            'ai_evaluated' => 'AI Evaluated',
                                            'shortlisted' => 'Shortlisted',
                                            'rejected' => 'Rejected',
                                            'interview_slot_booked' => 'Interview Slot Booked',
                                        ];
                                        echo $statusLabels[$job['status']] ?? 'Unknown';
                                        ?>
                                    </td>

                                    <td>
                                        <?php if ($job['status'] == 'applied'): ?>
                                            <a href="<?= base_url('interview/start/'.$job['application_id']) ?>" class="btn btn-success btn-sm">
                                                Start Interview
                                            </a>

                                        <?php elseif ($job['status'] == 'ai_interview_started'): ?>
                                            <!-- <a href="<?= base_url('interview/chat/'.$job['application_id']) ?>" class="btn btn-warning btn-sm">
                                                Resume Interview
                                            </a> -->

                                        <?php elseif ($job['status'] == 'ai_interview_completed'): ?>
                                            <span class="badge badge-info">Waiting for Evaluation</span>

                                        <?php elseif ($job['status'] == 'ai_evaluated'): ?>
                                            

                                        <?php elseif ($job['status'] == 'shortlisted'): ?>
                                            <a href="<?= base_url('candidate/book-slot/'.$job['application_id']) ?>" class="btn btn-success btn-sm">
                                                Book Slot
                                            </a>

                                        <?php elseif ($job['status'] == 'rejected'): ?>
                                            <span class="badge badge-danger">Rejected</span>

                                        <?php elseif ($job['status'] == 'interview_slot_booked'): ?>
                                            <a href="<?= base_url('candidate/reschedule-slot/'.$job['application_id']) ?>" class="btn btn-secondary btn-sm">
                                                Reschedule Slot
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center">No applied jobs found.</td>
                            </tr>
                        <?php endif; ?>

                    </tbody>
                </table>

            </div>
        </div>
    </div>
</section>

<?= view('layouts/candidate_footer') ?>
