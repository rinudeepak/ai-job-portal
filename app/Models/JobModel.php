<?php  

namespace App\Models;

use CodeIgniter\Model;

class JobModel extends Model
{
    protected $table = 'jobs';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'title',
        'category',
        'recruiter_id',
        'company',
        'location',
        'description',
        'required_skills',
        'experience_level',
        'min_ai_cutoff_score',
        'openings',
        'status',
        'employment_type'
    ];
    // Count open jobs
    public function getTotalOpenJobs()
    {
        return $this->where('status', 'open')->countAllResults();
    }

   /**
 * Get candidate's application behavior profile
 * Analyzes past applications to understand preferences
 */
public function getCandidateBehaviorProfile($candidateId)
{
    $db = \Config\Database::connect();

    // Get categories they've applied to most
    $topCategories = $db->query("
        SELECT j.category, COUNT(*) as apply_count
        FROM applications a
        JOIN jobs j ON a.job_id = j.id
        WHERE a.candidate_id = ?
        GROUP BY j.category
        ORDER BY apply_count DESC
        LIMIT 5
    ", [$candidateId])->getResultArray();

    // Get experience levels they target
    $topExperienceLevels = $db->query("
        SELECT j.experience_level, COUNT(*) as apply_count
        FROM applications a
        JOIN jobs j ON a.job_id = j.id
        WHERE a.candidate_id = ?
        GROUP BY j.experience_level
        ORDER BY apply_count DESC
        LIMIT 3
    ", [$candidateId])->getResultArray();

    // Get employment types they prefer
    $topEmploymentTypes = $db->query("
        SELECT j.employment_type, COUNT(*) as apply_count
        FROM applications a
        JOIN jobs j ON a.job_id = j.id
        WHERE a.candidate_id = ?
        GROUP BY j.employment_type
        ORDER BY apply_count DESC
        LIMIT 3
    ", [$candidateId])->getResultArray();

    // Get locations they prefer
    $topLocations = $db->query("
        SELECT j.location, COUNT(*) as apply_count
        FROM applications a
        JOIN jobs j ON a.job_id = j.id
        WHERE a.candidate_id = ?
        GROUP BY j.location
        ORDER BY apply_count DESC
        LIMIT 5
    ", [$candidateId])->getResultArray();

    // Get common skills in jobs they applied to
    $appliedSkills = $db->query("
        SELECT j.required_skills
        FROM applications a
        JOIN jobs j ON a.job_id = j.id
        WHERE a.candidate_id = ?
        ORDER BY a.applied_at DESC
        LIMIT 20
    ", [$candidateId])->getResultArray();

    // Extract and count skill frequency
    $skillFrequency = [];
    foreach ($appliedSkills as $row) {
        $skills = array_map('trim', explode(',', $row['required_skills']));
        foreach ($skills as $skill) {
            if (!empty($skill)) {
                $skill = strtolower($skill);
                $skillFrequency[$skill] = ($skillFrequency[$skill] ?? 0) + 1;
            }
        }
    }
    arsort($skillFrequency);

    return [
        'top_categories'       => $topCategories,
        'top_experience_levels'=> $topExperienceLevels,
        'top_employment_types' => $topEmploymentTypes,
        'top_locations'        => $topLocations,
        'applied_skill_frequency' => array_slice($skillFrequency, 0, 15, true),
    ];
}

/**
 * Basic skill + behavior scoring (non-AI fallback)
 *
 * FIX 1: candidate_skills stores ALL skills in ONE row as comma-separated string.
 *         array_column(...,'skill_name') returns ["PHP,MySQL,JavaScript"] not ["PHP","MySQL","JavaScript"]
 *         Must explode the string into individual skills first.
 *
 * FIX 2: HAVING match_score > 0 drops everyone with no behavior history.
 *         Use HAVING match_score >= 0 and ORDER BY score so new candidates
 *         still see jobs (sorted by recency as fallback).
 */
public function getSuggestedJobsBasic($candidateId, $limit = 10)
{
    $skillsModel    = new \App\Models\CandidateSkillsModel();
    $interestsModel = new \App\Models\CandidateInterestsModel();

    // ── FIX 1: Correctly parse comma-separated skills from single row ──
    $skillRow = $skillsModel->where('candidate_id', $candidateId)->first();
    $skills   = [];
    if ($skillRow && !empty($skillRow['skill_name'])) {
        // Explode the comma-separated string into individual skills
        $skills = array_filter(array_map('trim', explode(',', $skillRow['skill_name'])));
    }

    // Interests stored as one comma-separated row per candidate
    $interestRow = $interestsModel->where('candidate_id', $candidateId)->first();
    $interests   = [];
    if ($interestRow && !empty($interestRow['interest'])) {
        $interests = array_values(array_filter(array_map('trim', explode(',', $interestRow['interest']))));
    }

    $behavior = $this->getCandidateBehaviorProfile($candidateId);

    $scoreParts = [];

    // Skill match scoring (weight: 3 per skill)
    foreach ($skills as $skill) {
        if (empty(trim($skill))) continue;
        $escaped      = $this->db->escape(trim($skill));
        $scoreParts[] = "(CASE WHEN LOWER(required_skills) LIKE LOWER(CONCAT('%', {$escaped}, '%')) THEN 3 ELSE 0 END)";
    }

    // Interest match scoring (weight: 2 per interest)
    foreach ($interests as $interest) {
        if (empty(trim($interest))) continue;
        $escaped      = $this->db->escape(trim($interest));
        $scoreParts[] = "(CASE WHEN LOWER(category)    LIKE LOWER(CONCAT('%', {$escaped}, '%'))
                              OR LOWER(title)           LIKE LOWER(CONCAT('%', {$escaped}, '%'))
                              OR LOWER(description)     LIKE LOWER(CONCAT('%', {$escaped}, '%')) THEN 2 ELSE 0 END)";
    }

    // Behavior: preferred categories (weight: 2)
    foreach ($behavior['top_categories'] as $cat) {
        $escaped      = $this->db->escape($cat['category']);
        $scoreParts[] = "(CASE WHEN LOWER(category) = LOWER({$escaped}) THEN 2 ELSE 0 END)";
    }

    // Behavior: preferred experience level (weight: 1)
    foreach ($behavior['top_experience_levels'] as $exp) {
        $escaped      = $this->db->escape($exp['experience_level']);
        $scoreParts[] = "(CASE WHEN experience_level = {$escaped} THEN 1 ELSE 0 END)";
    }

    // Behavior: preferred employment type (weight: 1)
    foreach ($behavior['top_employment_types'] as $emp) {
        $escaped      = $this->db->escape($emp['employment_type']);
        $scoreParts[] = "(CASE WHEN employment_type = {$escaped} THEN 1 ELSE 0 END)";
    }

    // If no scoring signals at all (no skills, no interests, no behavior),
    // return empty — nothing to match against.
    if (empty($scoreParts)) {
        return [];
    }

    $scoreSQL = implode(' + ', $scoreParts);

    // Only return jobs that actually match at least one signal (match_score > 0)
    $db = \Config\Database::connect();
    return $db->query("
        SELECT jobs.*, ({$scoreSQL}) AS match_score
        FROM jobs
        WHERE status = 'open'
          AND id NOT IN (
              SELECT job_id FROM applications WHERE candidate_id = ?
          )
        HAVING match_score > 0
        ORDER BY match_score DESC, created_at DESC
        LIMIT {$limit}
    ", [$candidateId])->getResultArray();
}



}