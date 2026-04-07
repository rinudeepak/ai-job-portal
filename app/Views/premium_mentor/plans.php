<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-4 text-primary">AI Career Mentor</h1>
        <p class="lead">Your Personal AI Career Coach - Achieve Your Dream Job</p>
    </div>

    <div class="row">
        <?php foreach ($plans as $plan): ?>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card h-100 <?= $plan['name'] === 'Pro Monthly' ? 'border-primary' : '' ?>">
                    <?php if ($plan['name'] === 'Pro Monthly'): ?>
                        <div class="card-header bg-primary text-white text-center">
                            <i class="fas fa-star"></i> Most Popular
                        </div>
                    <?php endif; ?>
                    
                    <div class="card-body text-center">
                        <h4 class="card-title"><?= esc($plan['name']) ?></h4>
                        <div class="price mb-3">
                            <?php if ($plan['price'] == 0): ?>
                                <span class="h2 text-success">Free</span>
                            <?php else: ?>
                                <span class="h2 text-primary">₹<?= number_format($plan['price']) ?></span>
                                <small class="text-muted">
                                    /<?= $plan['duration_days'] == 30 ? 'month' : ($plan['duration_days'] == 90 ? 'quarter' : 'year') ?>
                                </small>
                            <?php endif; ?>
                        </div>
                        
                        <p class="text-muted"><?= esc($plan['description']) ?></p>
                        
                        <!-- Features List -->
                        <ul class="list-unstyled text-start">
                            <?php 
                            $features = json_decode($plan['features'], true);
                            foreach ($features as $feature): 
                            ?>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <?= esc($feature) ?>
                                </li>
                            <?php endforeach; ?>
                            
                            <?php if ($plan['chat_limit']): ?>
                                <li class="mb-2">
                                    <i class="fas fa-comment text-info me-2"></i>
                                    <?= $plan['chat_limit'] ?> chats per day
                                </li>
                            <?php else: ?>
                                <li class="mb-2">
                                    <i class="fas fa-infinity text-success me-2"></i>
                                    Unlimited chats
                                </li>
                            <?php endif; ?>
                            
                            <?php if ($plan['mentor_sessions_included'] > 0): ?>
                                <li class="mb-2">
                                    <i class="fas fa-user-tie text-warning me-2"></i>
                                    <?= $plan['mentor_sessions_included'] ?> mentor session(s)
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    
                    <div class="card-footer text-center">
                        <?php if ($plan['price'] == 0): ?>
                            <a href="/premium-mentor" class="btn btn-outline-primary">Get Started</a>
                        <?php else: ?>
                            <form method="post" action="/premium-mentor/subscribe">
                                <input type="hidden" name="plan_id" value="<?= $plan['id'] ?>">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    Subscribe Now
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Features -->
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="text-center mb-4">Premium AI Career Mentor Features</h3>
            
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="text-center">
                        <i class="fas fa-brain fa-3x text-primary mb-3"></i>
                        <h5>Advanced AI Analysis</h5>
                        <p class="text-muted">Deep career analysis with personalized guidance for your target role.</p>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="text-center">
                        <i class="fas fa-route fa-3x text-success mb-3"></i>
                        <h5>Personalized Roadmap</h5>
                        <p class="text-muted">Step-by-step career roadmap with specific milestones and timelines.</p>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="text-center">
                        <i class="fas fa-chart-line fa-3x text-warning mb-3"></i>
                        <h5>Progress Tracking</h5>
                        <p class="text-muted">Monitor career progress with detailed analytics and recommendations.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>