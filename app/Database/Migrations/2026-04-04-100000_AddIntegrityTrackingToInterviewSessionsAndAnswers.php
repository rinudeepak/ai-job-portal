<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIntegrityTrackingToInterviewSessionsAndAnswers extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('interview_sessions')) {
            $sessionFields = [];

            if (!$this->db->fieldExists('integrity_events', 'interview_sessions')) {
                $sessionFields['integrity_events'] = [
                    'type' => 'LONGTEXT',
                    'null' => true,
                    'after' => 'conversation_history',
                ];
            }

            if (!$this->db->fieldExists('integrity_flags', 'interview_sessions')) {
                $sessionFields['integrity_flags'] = [
                    'type' => 'LONGTEXT',
                    'null' => true,
                    'after' => 'integrity_events',
                ];
            }

            if (!$this->db->fieldExists('integrity_warning_count', 'interview_sessions')) {
                $sessionFields['integrity_warning_count'] = [
                    'type' => 'INT',
                    'constraint' => 11,
                    'default' => 0,
                    'after' => 'integrity_flags',
                ];
            }

            if (!$this->db->fieldExists('tab_switch_count', 'interview_sessions')) {
                $sessionFields['tab_switch_count'] = [
                    'type' => 'INT',
                    'constraint' => 11,
                    'default' => 0,
                    'after' => 'integrity_warning_count',
                ];
            }

            if (!$this->db->fieldExists('hidden_duration_seconds', 'interview_sessions')) {
                $sessionFields['hidden_duration_seconds'] = [
                    'type' => 'INT',
                    'constraint' => 11,
                    'default' => 0,
                    'after' => 'tab_switch_count',
                ];
            }

            if (!$this->db->fieldExists('reconnect_count', 'interview_sessions')) {
                $sessionFields['reconnect_count'] = [
                    'type' => 'INT',
                    'constraint' => 11,
                    'default' => 0,
                    'after' => 'hidden_duration_seconds',
                ];
            }

            if (!$this->db->fieldExists('last_integrity_ping_at', 'interview_sessions')) {
                $sessionFields['last_integrity_ping_at'] = [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'reconnect_count',
                ];
            }

            if (!$this->db->fieldExists('last_resume_at', 'interview_sessions')) {
                $sessionFields['last_resume_at'] = [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'last_integrity_ping_at',
                ];
            }

            if ($sessionFields) {
                $this->forge->addColumn('interview_sessions', $sessionFields);
            }
        }

        if ($this->db->tableExists('interview_session_answers')) {
            $answerFields = [];

            if (!$this->db->fieldExists('client_context', 'interview_session_answers')) {
                $answerFields['client_context'] = [
                    'type' => 'LONGTEXT',
                    'null' => true,
                ];
            }

            if (!$this->db->fieldExists('integrity_flags', 'interview_session_answers')) {
                $answerFields['integrity_flags'] = [
                    'type' => 'LONGTEXT',
                    'null' => true,
                ];
            }

            if (!$this->db->fieldExists('tab_switch_count', 'interview_session_answers')) {
                $answerFields['tab_switch_count'] = [
                    'type' => 'INT',
                    'constraint' => 11,
                    'default' => 0,
                ];
            }

            if (!$this->db->fieldExists('hidden_duration_seconds', 'interview_session_answers')) {
                $answerFields['hidden_duration_seconds'] = [
                    'type' => 'INT',
                    'constraint' => 11,
                    'default' => 0,
                ];
            }

            if (!$this->db->fieldExists('recording_health', 'interview_session_answers')) {
                $answerFields['recording_health'] = [
                    'type' => 'VARCHAR',
                    'constraint' => 30,
                    'null' => true,
                ];
            }

            if (!$this->db->fieldExists('recording_metrics', 'interview_session_answers')) {
                $answerFields['recording_metrics'] = [
                    'type' => 'LONGTEXT',
                    'null' => true,
                ];
            }

            if ($answerFields) {
                $this->forge->addColumn('interview_session_answers', $answerFields);
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('interview_sessions')) {
            foreach ([
                'integrity_events',
                'integrity_flags',
                'integrity_warning_count',
                'tab_switch_count',
                'hidden_duration_seconds',
                'reconnect_count',
                'last_integrity_ping_at',
                'last_resume_at',
            ] as $column) {
                if ($this->db->fieldExists($column, 'interview_sessions')) {
                    $this->forge->dropColumn('interview_sessions', $column);
                }
            }
        }

        if ($this->db->tableExists('interview_session_answers')) {
            foreach ([
                'client_context',
                'integrity_flags',
                'tab_switch_count',
                'hidden_duration_seconds',
                'recording_health',
                'recording_metrics',
            ] as $column) {
                if ($this->db->fieldExists($column, 'interview_session_answers')) {
                    $this->forge->dropColumn('interview_session_answers', $column);
                }
            }
        }
    }
}
