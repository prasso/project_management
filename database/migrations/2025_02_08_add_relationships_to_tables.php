<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'client_id')) {
                $table->foreignId('client_id')->constrained()->after('id');
            }
        });

        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'project_id')) {
                $table->foreignId('project_id')->constrained()->after('id');
            }
        });

        Schema::table('time_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('time_entries', 'task_id')) {
                $table->foreignId('task_id')->nullable()->constrained()->after('id');
            }
            if (!Schema::hasColumn('time_entries', 'client_id')) {
                $table->foreignId('client_id')->constrained()->after('task_id');
            }
        });
    }

    public function down()
    {
        
    }
};
