# Troubleshooting: Cannot Enter Profile Details

## Issue
Unable to add work experience, education, or certifications through the profile modals.

## Step-by-Step Fix

### 1. Run the Database Migration

Open phpMyAdmin or your MySQL client and run this SQL:

```sql
-- File: database/fix_profile_tables.sql

DROP TABLE IF EXISTS `certifications`;
DROP TABLE IF EXISTS `education`;
DROP TABLE IF EXISTS `work_experiences`;

CREATE TABLE `work_experiences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `job_title` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `employment_type` enum('Full-time','Part-time','Contract','Internship','Freelance') DEFAULT 'Full-time',
  `location` varchar(255) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `is_current` tinyint(1) DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `work_experiences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `education` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `degree` varchar(255) NOT NULL,
  `field_of_study` varchar(255) DEFAULT NULL,
  `institution` varchar(255) NOT NULL,
  `start_year` year NOT NULL,
  `end_year` year DEFAULT NULL,
  `grade` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `education_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `certifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `certification_name` varchar(255) NOT NULL,
  `issuing_organization` varchar(255) NOT NULL,
  `issue_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `credential_id` varchar(255) DEFAULT NULL,
  `credential_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `certifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

### 2. Clear Browser Cache

- Press `Ctrl + Shift + Delete` (or `Cmd + Shift + Delete` on Mac)
- Clear cached images and files
- Reload the page with `Ctrl + F5`

### 3. Test Adding Work Experience

1. Go to your profile page: `http://localhost/ai-job-portal/candidate/profile`
2. Click on the **Experience** tab
3. Click the **Add** button
4. Fill in the form:
   - Job Title: "Software Developer"
   - Company Name: "Test Company"
   - Employment Type: Select "Full-time"
   - Location: "Remote"
   - Start Date: Select any date
   - Check "Currently working here" OR select an End Date
   - Description: "Test description"
5. Click **Save**

### 4. Check for Errors

If it still doesn't work, check:

#### A. Browser Console (F12)
- Look for JavaScript errors
- Check Network tab for failed requests
- Look for 404 or 500 errors

#### B. PHP Errors
Make sure `.env` file has:
```
CI_ENVIRONMENT = development
```

Then check for error messages on the page.

#### C. Check Logs
Look in `writable/logs/log-YYYY-MM-DD.log` for error messages.

### 5. Verify Routes

Make sure these routes exist in `app/Config/Routes.php`:

```php
$routes->post('candidate/add-work-experience', 'Candidate::addWorkExperience', ['filter' => 'auth']);
$routes->post('candidate/add-education', 'Candidate::addEducation', ['filter' => 'auth']);
$routes->post('candidate/add-certification', 'Candidate::addCertification', ['filter' => 'auth']);
```

### 6. Test Each Section

After fixing, test:

1. **Work Experience**: Add a job with all details
2. **Education**: Add a degree
3. **Certifications**: Add a certification

Each should:
- Show a success message
- Display the new entry in the list
- Allow you to delete it

## Common Error Messages

### "Failed to add work experience: Table doesn't exist"
**Solution**: Run the SQL migration in Step 1

### "Failed to add work experience: Unknown column 'name'"
**Solution**: The certifications table has wrong column name. Run the SQL migration.

### "CSRF token mismatch"
**Solution**: 
1. Clear browser cache
2. Make sure `<?= csrf_field() ?>` is in each form
3. Check that CSRF is enabled in `app/Config/Security.php`

### "Foreign key constraint fails"
**Solution**: Make sure you're logged in and your user_id exists in the users table.

## Still Not Working?

1. Check if Bootstrap 5 is loaded (modals won't work without it)
2. Verify you're logged in as a candidate
3. Check database connection in `.env` file
4. Try adding data directly via phpMyAdmin to test database structure
5. Check `writable/logs/` for detailed error messages

## Success Indicators

When working correctly, you should see:
- ✅ Green success message after saving
- ✅ New entry appears in the list immediately
- ✅ Delete button works with confirmation
- ✅ Data persists after page reload
