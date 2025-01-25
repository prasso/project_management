<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->decimal('budget', 10, 2)->nullable()->after('description');
            $table->decimal('hourly_rate', 8, 2)->nullable()->after('budget');
        });
    }

    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['budget', 'hourly_rate']);
        });
    }
};
