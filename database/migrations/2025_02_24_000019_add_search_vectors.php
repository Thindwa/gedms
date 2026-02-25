<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            $this->upPostgres();
        } elseif (in_array($driver, ['mysql', 'mariadb'])) {
            $this->upMysql();
        }
    }

    protected function upPostgres(): void
    {
        DB::statement('ALTER TABLE files ADD COLUMN IF NOT EXISTS search_vector tsvector');
        DB::statement('CREATE INDEX IF NOT EXISTS files_search_vector_idx ON files USING GIN(search_vector)');
        DB::statement("
            CREATE OR REPLACE FUNCTION files_search_vector_trigger() RETURNS trigger AS $$
            BEGIN
                NEW.search_vector := setweight(to_tsvector('english', COALESCE(NEW.name, '')), 'A');
                RETURN NEW;
            END
            $$ LANGUAGE plpgsql;
        ");
        DB::statement('DROP TRIGGER IF EXISTS files_search_vector_trigger ON files');
        DB::statement('CREATE TRIGGER files_search_vector_trigger BEFORE INSERT OR UPDATE ON files FOR EACH ROW EXECUTE PROCEDURE files_search_vector_trigger()');

        DB::statement('ALTER TABLE documents ADD COLUMN IF NOT EXISTS search_vector tsvector');
        DB::statement('CREATE INDEX IF NOT EXISTS documents_search_vector_idx ON documents USING GIN(search_vector)');
        DB::statement("
            CREATE OR REPLACE FUNCTION documents_search_vector_trigger() RETURNS trigger AS $$
            BEGIN
                NEW.search_vector := setweight(to_tsvector('english', COALESCE(NEW.title, '')), 'A');
                RETURN NEW;
            END
            $$ LANGUAGE plpgsql;
        ");
        DB::statement('DROP TRIGGER IF EXISTS documents_search_vector_trigger ON documents');
        DB::statement('CREATE TRIGGER documents_search_vector_trigger BEFORE INSERT OR UPDATE ON documents FOR EACH ROW EXECUTE PROCEDURE documents_search_vector_trigger()');

        DB::statement("UPDATE files SET search_vector = setweight(to_tsvector('english', COALESCE(name, '')), 'A') WHERE search_vector IS NULL");
        DB::statement("UPDATE documents SET search_vector = setweight(to_tsvector('english', COALESCE(title, '')), 'A') WHERE search_vector IS NULL");
    }

    protected function upMysql(): void
    {
        Schema::table('files', function ($table) {
            $table->fullText('name', 'files_name_fulltext');
        });
        Schema::table('documents', function ($table) {
            $table->fullText('title', 'documents_title_fulltext');
        });
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            $this->downPostgres();
        } elseif (in_array($driver, ['mysql', 'mariadb'])) {
            $this->downMysql();
        }
    }

    protected function downPostgres(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS files_search_vector_trigger ON files');
        DB::statement('DROP FUNCTION IF EXISTS files_search_vector_trigger()');
        DB::statement('DROP INDEX IF EXISTS files_search_vector_idx');
        DB::statement('ALTER TABLE files DROP COLUMN IF EXISTS search_vector');

        DB::statement('DROP TRIGGER IF EXISTS documents_search_vector_trigger ON documents');
        DB::statement('DROP FUNCTION IF EXISTS documents_search_vector_trigger()');
        DB::statement('DROP INDEX IF EXISTS documents_search_vector_idx');
        DB::statement('ALTER TABLE documents DROP COLUMN IF EXISTS search_vector');
    }

    protected function downMysql(): void
    {
        Schema::table('files', function ($table) {
            $table->dropIndex('files_name_fulltext');
        });
        Schema::table('documents', function ($table) {
            $table->dropIndex('documents_title_fulltext');
        });
    }
};
