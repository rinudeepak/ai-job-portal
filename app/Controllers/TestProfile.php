<?php

namespace App\Controllers;

class TestProfile extends BaseController
{
    public function checkTables()
    {
        $db = \Config\Database::connect();
        
        echo "<h2>Database Table Check</h2>";
        
        // Check work_experiences table
        echo "<h3>1. work_experiences table</h3>";
        if ($db->tableExists('work_experiences')) {
            echo "✅ Table EXISTS<br>";
            $fields = $db->getFieldNames('work_experiences');
            echo "Columns: " . implode(', ', $fields) . "<br>";
        } else {
            echo "❌ Table DOES NOT EXIST<br>";
        }
        
        // Check education table
        echo "<h3>2. education table</h3>";
        if ($db->tableExists('education')) {
            echo "✅ Table EXISTS<br>";
            $fields = $db->getFieldNames('education');
            echo "Columns: " . implode(', ', $fields) . "<br>";
        } else {
            echo "❌ Table DOES NOT EXIST<br>";
        }
        
        // Check certifications table
        echo "<h3>3. certifications table</h3>";
        if ($db->tableExists('certifications')) {
            echo "✅ Table EXISTS<br>";
            $fields = $db->getFieldNames('certifications');
            echo "Columns: " . implode(', ', $fields) . "<br>";
        } else {
            echo "❌ Table DOES NOT EXIST<br>";
        }
        
        // Test insert (if logged in)
        if (session()->get('logged_in')) {
            echo "<h3>4. Test Insert</h3>";
            $userId = session()->get('user_id');
            echo "User ID: $userId<br>";
            
            try {
                $workExpModel = new \App\Models\WorkExperienceModel();
                $testData = [
                    'user_id' => $userId,
                    'job_title' => 'Test Job',
                    'company_name' => 'Test Company',
                    'employment_type' => 'Full-time',
                    'start_date' => date('Y-m-d'),
                    'is_current' => 1
                ];
                
                $id = $workExpModel->insert($testData);
                
                if ($id) {
                    echo "✅ Test insert SUCCESSFUL (ID: $id)<br>";
                    // Clean up
                    $workExpModel->delete($id);
                    echo "✅ Test record deleted<br>";
                } else {
                    echo "❌ Test insert FAILED<br>";
                    echo "Errors: " . print_r($workExpModel->errors(), true) . "<br>";
                }
            } catch (\Exception $e) {
                echo "❌ ERROR: " . $e->getMessage() . "<br>";
            }
        } else {
            echo "<h3>4. Test Insert</h3>";
            echo "⚠️ Not logged in - cannot test insert<br>";
        }
        
        echo "<hr>";
        echo "<p><a href='" . base_url('candidate/profile') . "'>Go to Profile</a></p>";
    }
}
