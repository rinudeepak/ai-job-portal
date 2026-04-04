<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCopyPasteTrackingToAiInterviewRound1Attempts extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('ai_interview_round1_attempts')) {
            return;
        }

        $fields = [];

        if (!$this->db->fieldExists('client_context', 'ai_interview_round1_attempts')) {
            $fields['client_context'] = [
                'type' => 'TEXT',
                'null' => true,
            ];
        }

        if (!$this->db->fieldExists('integrity_flags', 'ai_interview_round1_attempts')) {
            $fields['integrity_flags'] = [
                'type' => 'TEXT',
                'null' => true,
            ];
        }

        if (!$this->db->fieldExists('paste_event_count', 'ai_interview_round1_attempts')) {
            $fields['paste_event_count'] = [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 0,
            ];
        }

        if (!$this->db->fieldExists('pasted_character_count', 'ai_interview_round1_attempts')) {
            $fields['pasted_character_count'] = [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 0,
            ];
        }

        if (!$this->db->fieldExists('copy_paste_detected', 'ai_interview_round1_attempts')) {
            $fields['copy_paste_detected'] = [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ];
        }

        if (!$this->db->fieldExists('large_insert_count', 'ai_interview_round1_attempts')) {
            $fields['large_insert_count'] = [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 0,
            ];
        }

        if (!$this->db->fieldExists('large_insert_character_count', 'ai_interview_round1_attempts')) {
            $fields['large_insert_character_count'] = [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 0,
            ];
        }

        if (!$this->db->fieldExists('large_insert_detected', 'ai_interview_round1_attempts')) {
            $fields['large_insert_detected'] = [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ];
        }

        if ($fields !== []) {
            $this->forge->addColumn('ai_interview_round1_attempts', $fields);
        }
    }

    public function down()
    {
        if (!$this->db->tableExists('ai_interview_round1_attempts')) {
            return;
        }

        $columns = [];
        foreach (['client_context', 'integrity_flags', 'paste_event_count', 'pasted_character_count', 'copy_paste_detected', 'large_insert_count', 'large_insert_character_count', 'large_insert_detected'] as $column) {
            if ($this->db->fieldExists($column, 'ai_interview_round1_attempts')) {
                $columns[] = $column;
            }
        }

        if ($columns !== []) {
            $this->forge->dropColumn('ai_interview_round1_attempts', $columns);
        }
    }
}
