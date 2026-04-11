# Naukri-like Company Job Search Implementation
## Status: In Progress

### 1. ✅ Create this TODO.md

### 2. 🔄 Update app/Controllers/CompanyProfile.php
- Simplify `searchJobs()`: Remove internal JobModel + TargetCompanyModel logic
- Always call `$service->fetchJobs($companyName, '', '', $limit)`
- Return `{company, count, jobs, status: 'official'}`

### 3. 🔄 Update app/Views/company/index.php  
- `renderJobs()`: Remove target_id, is_new_target, auto-add logic
- Always show "Found X jobs from official career page (AI)"
- Optional: Add "Save to targets?" button

### 4. 🔄 Optional: Enhance autocomplete (add CompanyModel names to suggest)

### 5. 🧪 Test
- Login candidate → /companies → search company (known/unknown) → verify ALWAYS external jobs table
- No internal check, no auto-target

### 6. ✅ Complete task

