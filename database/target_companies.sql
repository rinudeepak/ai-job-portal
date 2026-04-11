CREATE TABLE candidate_target_companies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    company_name VARCHAR(150) NOT NULL,
    careers_platform VARCHAR(50) DEFAULT NULL,
    platform_slug VARCHAR(150) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_company (user_id, company_name)
);

-- Well-known companies with their job platform slugs
INSERT INTO candidate_target_companies (user_id, company_name, careers_platform, platform_slug) VALUES
-- These are sample entries, replace user_id as needed
-- Format: platform = 'greenhouse' | 'lever' | 'workable' | 'direct'
-- slug = the company's board token on that platform
(1, 'Stripe', 'greenhouse', 'stripe'),
(1, 'Shopify', 'greenhouse', 'shopify'),
(1, 'Airbnb', 'greenhouse', 'airbnb');