<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->timestamp('sent_at')->nullable()->after('status');
            $table->timestamp('paid_at')->nullable()->after('sent_at');
            $table->timestamp('cancelled_at')->nullable()->after('paid_at');
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['sent_at', 'paid_at', 'cancelled_at']);
        });
    }
};
