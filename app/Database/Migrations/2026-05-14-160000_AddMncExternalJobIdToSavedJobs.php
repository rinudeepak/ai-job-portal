<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMncExternalJobIdToSavedJobs extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('saved_jobs')) {
            return;
        }

        $this->dropForeignKeyIfExists('saved_jobs', 'saved_jobs_job_id_foreign');

        $this->forge->modifyColumn('saved_jobs', [
            'job_id' => [
                'name' => 'job_id',
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
        ]);

        if (!$this->db->fieldExists('mnc_external_job_id', 'saved_jobs')) {
            $this->forge->addColumn('saved_jobs', [
                'mnc_external_job_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'after' => 'job_id',
                ],
            ]);
        }

        $this->addIndexIfMissing('saved_jobs', 'saved_jobs_mnc_external_job_id_index', 'mnc_external_job_id');
        $this->addUniqueIndexIfMissing('saved_jobs', 'saved_jobs_candidate_mnc_external_unique', ['candidate_id', 'mnc_external_job_id']);
        $this->addForeignKeyIfMissing('saved_jobs', 'saved_jobs_job_id_foreign', 'job_id', 'jobs', 'id');
    }

    public function down()
    {
        if (!$this->db->tableExists('saved_jobs')) {
            return;
        }

        $this->dropForeignKeyIfExists('saved_jobs', 'saved_jobs_job_id_foreign');
        $this->dropIndexIfExists('saved_jobs', 'saved_jobs_candidate_mnc_external_unique');
        $this->dropIndexIfExists('saved_jobs', 'saved_jobs_mnc_external_job_id_index');

        if ($this->db->fieldExists('mnc_external_job_id', 'saved_jobs')) {
            $this->db->table('saved_jobs')
                ->where('job_id', null)
                ->where('mnc_external_job_id IS NOT NULL', null, false)
                ->delete();
            $this->forge->dropColumn('saved_jobs', 'mnc_external_job_id');
        }

        $this->forge->modifyColumn('saved_jobs', [
            'job_id' => [
                'name' => 'job_id',
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
        ]);

        $this->addForeignKeyIfMissing('saved_jobs', 'saved_jobs_job_id_foreign', 'job_id', 'jobs', 'id');
    }

    private function addForeignKeyIfMissing(string $table, string $name, string $column, string $foreignTable, string $foreignColumn): void
    {
        if ($this->constraintExists($table, $name)) {
            return;
        }

        $this->db->query(
            "ALTER TABLE {$table} ADD CONSTRAINT {$name} FOREIGN KEY ({$column}) REFERENCES {$foreignTable} ({$foreignColumn}) ON DELETE CASCADE ON UPDATE CASCADE"
        );
    }

    private function dropForeignKeyIfExists(string $table, string $name): void
    {
        if (!$this->constraintExists($table, $name)) {
            return;
        }

        $this->db->query("ALTER TABLE {$table} DROP FOREIGN KEY {$name}");
    }

    private function addIndexIfMissing(string $table, string $name, string $column): void
    {
        if ($this->indexExists($table, $name)) {
            return;
        }

        $this->db->query("ALTER TABLE {$table} ADD INDEX {$name} ({$column})");
    }

    /**
     * @param array<int, string> $columns
     */
    private function addUniqueIndexIfMissing(string $table, string $name, array $columns): void
    {
        if ($this->indexExists($table, $name)) {
            return;
        }

        $this->db->query("ALTER TABLE {$table} ADD UNIQUE KEY {$name} (" . implode(', ', $columns) . ")");
    }

    private function dropIndexIfExists(string $table, string $name): void
    {
        if (!$this->indexExists($table, $name)) {
            return;
        }

        $this->db->query("ALTER TABLE {$table} DROP INDEX {$name}");
    }

    private function constraintExists(string $table, string $name): bool
    {
        $row = $this->db->query(
            'SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?',
            [$table, $name]
        )->getRowArray();

        return !empty($row);
    }

    private function indexExists(string $table, string $name): bool
    {
        $row = $this->db->query("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$name])->getRowArray();

        return !empty($row);
    }
}
