<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddConsultancyRecruiterAndJobFields extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('recruiter_profiles')) {
            $this->addRecruiterProfileFields();
        }

        if ($this->db->tableExists('jobs')) {
            $this->addJobPostingFields();
        }
    }

    public function down()
    {
        if ($this->db->tableExists('jobs')) {
            foreach (['candidate_fee_allowed', 'payroll_type', 'client_disclosure', 'client_company_name', 'posted_for'] as $field) {
                if ($this->db->fieldExists($field, 'jobs')) {
                    $this->forge->dropColumn('jobs', $field);
                }
            }
        }

        if ($this->db->tableExists('recruiter_profiles')) {
            foreach (['can_post_jobs', 'official_email', 'website', 'gst_number', 'agency_registration_number', 'verification_status', 'recruiter_type'] as $field) {
                if ($this->db->fieldExists($field, 'recruiter_profiles')) {
                    $this->forge->dropColumn('recruiter_profiles', $field);
                }
            }
        }
    }

    private function addRecruiterProfileFields(): void
    {
        if (!$this->db->fieldExists('recruiter_type', 'recruiter_profiles')) {
            $this->forge->addColumn('recruiter_profiles', [
                'recruiter_type' => [
                    'type' => 'VARCHAR',
                    'constraint' => 32,
                    'default' => 'direct_employer',
                    'after' => 'company_name_snapshot',
                ],
            ]);
        }

        if (!$this->db->fieldExists('verification_status', 'recruiter_profiles')) {
            $this->forge->addColumn('recruiter_profiles', [
                'verification_status' => [
                    'type' => 'VARCHAR',
                    'constraint' => 32,
                    'default' => 'verified',
                    'after' => 'recruiter_type',
                ],
            ]);
        }

        if (!$this->db->fieldExists('agency_registration_number', 'recruiter_profiles')) {
            $this->forge->addColumn('recruiter_profiles', [
                'agency_registration_number' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => true,
                    'after' => 'verification_status',
                ],
            ]);
        }

        if (!$this->db->fieldExists('gst_number', 'recruiter_profiles')) {
            $this->forge->addColumn('recruiter_profiles', [
                'gst_number' => [
                    'type' => 'VARCHAR',
                    'constraint' => 30,
                    'null' => true,
                    'after' => 'agency_registration_number',
                ],
            ]);
        }

        if (!$this->db->fieldExists('website', 'recruiter_profiles')) {
            $this->forge->addColumn('recruiter_profiles', [
                'website' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
                    'after' => 'gst_number',
                ],
            ]);
        }

        if (!$this->db->fieldExists('official_email', 'recruiter_profiles')) {
            $this->forge->addColumn('recruiter_profiles', [
                'official_email' => [
                    'type' => 'VARCHAR',
                    'constraint' => 191,
                    'null' => true,
                    'after' => 'website',
                ],
            ]);
        }

        if (!$this->db->fieldExists('can_post_jobs', 'recruiter_profiles')) {
            $this->forge->addColumn('recruiter_profiles', [
                'can_post_jobs' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 1,
                    'after' => 'official_email',
                ],
            ]);
        }
    }

    private function addJobPostingFields(): void
    {
        if (!$this->db->fieldExists('posted_for', 'jobs')) {
            $this->forge->addColumn('jobs', [
                'posted_for' => [
                    'type' => 'VARCHAR',
                    'constraint' => 32,
                    'default' => 'own_company',
                    'after' => 'company',
                ],
            ]);
        }

        if (!$this->db->fieldExists('client_company_name', 'jobs')) {
            $this->forge->addColumn('jobs', [
                'client_company_name' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
                    'after' => 'posted_for',
                ],
            ]);
        }

        if (!$this->db->fieldExists('client_disclosure', 'jobs')) {
            $this->forge->addColumn('jobs', [
                'client_disclosure' => [
                    'type' => 'VARCHAR',
                    'constraint' => 32,
                    'default' => 'visible',
                    'after' => 'client_company_name',
                ],
            ]);
        }

        if (!$this->db->fieldExists('payroll_type', 'jobs')) {
            $this->forge->addColumn('jobs', [
                'payroll_type' => [
                    'type' => 'VARCHAR',
                    'constraint' => 64,
                    'null' => true,
                    'after' => 'employment_type',
                ],
            ]);
        }

        if (!$this->db->fieldExists('candidate_fee_allowed', 'jobs')) {
            $this->forge->addColumn('jobs', [
                'candidate_fee_allowed' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0,
                    'after' => 'payroll_type',
                ],
            ]);
        }
    }
}
