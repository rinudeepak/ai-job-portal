-- Premium Career Mentor Chatbot - Subscription System

CREATE TABLE subscription_plans (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    duration_days INT NOT NULL,
    features TEXT NOT NULL,
    chat_limit INT DEFAULT NULL,
    mentor_sessions_included INT DEFAULT 0,
    priority_support BOOLEAN DEFAULT FALSE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE user_subscriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('active', 'expired', 'cancelled') DEFAULT 'active',
    payment_id VARCHAR(100),
    amount_paid DECIMAL(10,2) NOT NULL,
    auto_renewal BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES subscription_plans(id)
);

CREATE TABLE chatbot_usage (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    session_id VARCHAR(50) NOT NULL,
    message_count INT DEFAULT 1,
    feature_used VARCHAR(50),
    date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_date (user_id, date)
);

CREATE TABLE premium_career_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    session_type ENUM('goal_review', 'skill_assessment', 'interview_prep', 'career_strategy') NOT NULL,
    current_role VARCHAR(100),
    target_role VARCHAR(100) NOT NULL,
    timeline VARCHAR(50),
    ai_analysis TEXT,
    action_plan TEXT,
    progress_tracking TEXT,
    status ENUM('active', 'completed', 'paused') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO subscription_plans (name, description, price, duration_days, features, chat_limit, mentor_sessions_included, priority_support) VALUES
('Free Plan', 'Basic career guidance with limited features', 0.00, 30, 
 '["Basic career chat", "3 SMART goals", "Job matching tips"]', 10, 0, 0),

('Pro Monthly', 'Advanced AI career mentor with unlimited access', 999.00, 30, 
 '["Unlimited career chat", "Unlimited SMART goals", "Weekly progress reviews", "Skill gap analysis", "Interview preparation", "Resume optimization", "LinkedIn profile tips", "Salary negotiation guidance"]', NULL, 1, 1),

('Pro Quarterly', 'Advanced AI career mentor - 3 months with bonus features', 2499.00, 90, 
 '["Everything in Pro Monthly", "Monthly 1-on-1 mentor calls", "Personalized learning roadmap", "Industry insights", "Network building guidance", "Career transition support"]', NULL, 3, 1),

('Enterprise Annual', 'Complete career transformation package', 7999.00, 365, 
 '["Everything in Pro Quarterly", "Weekly mentor sessions", "Personal branding strategy", "Executive coaching", "Leadership development", "Career acceleration program", "Priority job referrals"]', NULL, 12, 1);