# AI Job Portal

An intelligent job portal built with CodeIgniter 4 featuring AI-powered interviews, career transition guidance, and comprehensive candidate-recruiter matching.

## 🚀 Features

### For Candidates
- **Smart Profile Management**: Naukri-style detailed profiles with work experience, education, and certifications
- **Resume Parsing**: Automatic skill extraction from uploaded resumes
- **GitHub Integration**: Analyze GitHub repositories to extract programming languages and skills
- **AI Interview System**: Automated technical interviews with AI evaluation
- **Career Transition AI**: 
  - Skill gap analysis
  - Personalized learning roadmaps
  - Daily micro-tasks (5-10 minutes)
  - Offline-ready course content
- **Interview Slot Booking**: Book and manage interview schedules
- **Application Tracking**: Track job applications and interview status

### For Recruiters
- **Job Posting**: Create and manage job listings
- **Application Management**: View and filter candidate applications
- **Candidate Profiles**: Access detailed candidate profiles with complete work history
- **AI Interview Results**: Review AI-generated interview evaluations
- **Interview Scheduling**: Manage interview slots and bookings
- **Dashboard Analytics**: Track applications, interviews, and hiring metrics

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx with mod_rewrite enabled
- Composer
- XAMPP/WAMP/LAMP (for local development)

## 🛠️ Installation

### 1. Clone Repository
```bash
git clone https://github.com/yourusername/ai-job-portal.git
cd ai-job-portal
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Environment Configuration
```bash
# Copy example environment file
cp .env.example .env

# Edit .env file and update:
# - app.baseURL (your project URL)
# - database credentials
# - API keys (OpenAI, Mistral, GitHub)
```

### 4. Generate Encryption Key
```bash
php spark key:generate
```

### 5. Database Setup
```sql
-- Create database
CREATE DATABASE ai_job_portal;

-- Import schema
mysql -u root -p ai_job_portal < database/schema.sql

-- Import additional tables
mysql -u root -p ai_job_portal < database/career_transition.sql
mysql -u root -p ai_job_portal < database/course_content.sql
mysql -u root -p ai_job_portal < database/naukri_style_profile.sql
```

### 6. Configure Base URL

**⚠️ IMPORTANT: Update the base URL in `.env` file to match your setup**

**Option A: XAMPP (Quick Setup)**
```env
app.baseURL=http://localhost/ai-job-portal/public/
```
**Note:** Replace `ai-job-portal` with your actual folder name if different.

Access: `http://localhost/ai-job-portal/public/`

**Option B: Virtual Host (Recommended)**

1. Edit `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:
```apache
<VirtualHost *:80>
    ServerName jobportal.local
    DocumentRoot "C:/xampp/htdocs/ai-job-portal/public"
    <Directory "C:/xampp/htdocs/ai-job-portal/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```
**Note:** Update the path if your project is in a different location.

2. Edit `C:\Windows\System32\drivers\etc\hosts` (Run as Administrator):
```
127.0.0.1 jobportal.local
```

3. Update `.env`:
```env
app.baseURL=http://jobportal.local/
```

4. Restart Apache and access: `http://jobportal.local`

**Option C: Production/Custom Domain**
```env
app.baseURL=https://yourdomain.com/
app.forceGlobalSecureRequests=true
```

### 7. Set Permissions
```bash
# Linux/Mac
chmod -R 755 writable/
chmod -R 755 public/uploads/

# Windows - ensure writable/ folder has write permissions
```

## 🔑 API Keys Setup

### OpenAI API Key
1. Visit https://platform.openai.com/api-keys
2. Create new API key
3. Add to `.env`: `OPENAI_API_KEY=sk-...`

### Mistral API Key
1. Visit https://console.mistral.ai/
2. Create API key
3. Add to `.env`: `MISTRAL_API_KEY=...`

### GitHub Token
1. Visit https://github.com/settings/tokens
2. Generate new token with `repo` scope
3. Add to `.env`: `GITHUB_TOKEN=ghp_...`

## 📁 Project Structure

```
ai-job-portal/
├── app/
│   ├── Controllers/       # Application controllers
│   ├── Models/           # Database models
│   ├── Views/            # View templates
│   ├── Libraries/        # Custom libraries (AI, Resume Parser)
│   └── Config/           # Configuration files
├── public/               # Public assets (CSS, JS, images)
├── writable/            # Logs, cache, uploads
├── database/            # SQL schema files
└── .env                 # Environment configuration
```

## 🎯 Usage

### Default Login Credentials

**Recruiter:**
- Email: `recruiter1@gmail.com`
- Password: `recruiter1`

**Candidate:**
- Email: `manju@gmail.com`
- Password: `manjupswd`

### Creating New Users

**Register as Candidate:**
- Visit: `/register`
- Fill registration form
- Upload resume for automatic skill extraction

**Register as Recruiter:**
- Visit: `/recruiter/register`
- Note: This route may be restricted in production

## 🔧 Configuration

### Database
Edit `.env`:
```env
database.default.hostname=localhost
database.default.database=ai_job_portal
database.default.username=root
database.default.password=your_password
```

### Email (Optional)
Configure SMTP in `app/Config/Email.php` for notifications.

### File Uploads
- Resumes: `writable/uploads/resumes/`
- Profile Photos: `public/uploads/profiles/`
- Max upload size: 5MB (configurable in `php.ini`)

## 🚨 Troubleshooting

### Modals Not Opening
- Ensure jQuery and Bootstrap JS are loaded
- Check browser console for errors
- Clear browser cache (Ctrl+F5)

### Database Connection Failed
- Verify MySQL is running
- Check database credentials in `.env`
- Ensure database exists

### 404 Errors
- Enable `mod_rewrite` in Apache
- Check `.htaccess` exists in `public/` folder
- Verify `app.baseURL` in `.env`

### CSRF Token Mismatch
- Clear browser cookies
- Check `security.csrfProtection` in `.env`
- Ensure forms include `<?= csrf_field() ?>`

## 📚 Key Features Documentation

### Career Transition AI
- Detects skill mismatches when applying to jobs
- Compares job requirements with resume + GitHub skills
- Generates personalized learning roadmaps
- Provides daily 5-10 minute tasks
- Offline-ready course content

### Naukri-Style Profiles
- Multiple work experiences with full details
- Education history with grades
- Professional certifications with verification links
- Recruiter-friendly profile view

### AI Interview System
- Automated technical interviews
- Real-time question generation
- AI-powered evaluation
- Detailed feedback and scoring

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📝 License

This project is open-source and available under the MIT License.

## 🐛 Known Issues

- Bootstrap 4 compatibility (modals use `data-toggle` instead of `data-bs-toggle`)
- Year input type may not work in older browsers (use number input)

## 📧 Support

For issues and questions:
- Open an issue on GitHub
- Check existing issues for solutions
- Review troubleshooting section above

## 🔄 Updates

Check the [CHANGELOG.md](CHANGELOG.md) for version history and updates.

## 🙏 Acknowledgments

- CodeIgniter 4 Framework
- OpenAI API for AI features
- Mistral AI for alternative AI processing
- Bootstrap 4 for UI components
- Font Awesome for icons

---

**Built with ❤️ using CodeIgniter 4**
