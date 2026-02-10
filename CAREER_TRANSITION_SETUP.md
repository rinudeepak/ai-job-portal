# Career Transition AI - Setup Guide

## 🚀 Feature Overview
This feature allows candidates to input their current role and target role (text or voice), and the system generates:
- Current role detection
- Target role detection  
- Skill gap analysis
- Learning roadmap (weeks/months)
- Daily 5-10 minute tasks

## 📋 Installation Steps

### 1. Database Setup
Run the SQL file to create required tables:
```sql
SOURCE database/career_transition.sql;
```

Or manually execute:
```bash
mysql -u root -p ai_job_portal < database/career_transition.sql
```

### 2. Environment Configuration
Add OpenAI API key to your `.env` file:
```
OPENAI_API_KEY=your_openai_api_key_here
```

### 3. Access the Feature
Navigate to: `http://localhost/ai-job-portal/career-transition`

## 🎯 Usage Flow

1. **Candidate Input**: "I am a ServiceNow Developer and want to become a ServiceNow Architect"
2. **System Analysis**: 
   - Detects current role: ServiceNow Developer
   - Detects target role: ServiceNow Architect
   - Analyzes skill gaps
3. **Output Generated**:
   - Learning roadmap with timeline
   - Daily micro-tasks (5-10 minutes each)
   - Progress tracking

## 📁 Files Created

- `app/Controllers/CareerTransition.php` - Main controller
- `app/Models/CareerTransitionModel.php` - Transition data model
- `app/Models/DailyTaskModel.php` - Task management model
- `app/Libraries/CareerTransitionAI.php` - AI analysis engine
- `app/Views/candidate/career_transition.php` - UI interface
- `database/career_transition.sql` - Database schema

## 🔗 Integration Points

### Add to Candidate Dashboard
Add this link in your candidate navigation:
```php
<a href="<?= base_url('career-transition') ?>" class="nav-link">
    🚀 Career Transition AI
</a>
```

### Voice Input (Optional Enhancement)
To add voice input, include Web Speech API in the view:
```javascript
const recognition = new webkitSpeechRecognition();
recognition.onresult = (e) => {
    document.querySelector('input[name="current_role"]').value = e.results[0][0].transcript;
};
```

## 🎨 Customization

### Modify AI Prompt
Edit `app/Libraries/CareerTransitionAI.php` line 13-24 to customize the analysis format.

### Adjust Task Duration
Modify `duration_minutes` in the daily_tasks table or controller logic.

## 🔧 Troubleshooting

- **No API response**: Check OPENAI_API_KEY in .env
- **Database error**: Ensure tables are created
- **Route not found**: Clear route cache: `php spark cache:clear`
