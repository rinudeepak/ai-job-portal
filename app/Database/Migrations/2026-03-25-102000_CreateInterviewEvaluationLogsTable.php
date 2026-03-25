<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInterviewEvaluationLogsTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('interview_evaluation_logs')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'interview_session_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['queued', 'processing', 'completed', 'failed'],
                'default'    => 'queued',
            ],
            'provider' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'request_payload' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'response_payload' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'error_message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'started_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'completed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('interview_session_id');
        $this->forge->addKey('status');

        $this->forge->createTable('interview_evaluation_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('interview_evaluation_logs', true);
    }
}
