<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
                $table->string('invoice_number')->unique();
                $table->date('issue_date');
                $table->date('due_date');
                $table->enum('status', ['draft', 'sent', 'paid', 'overdue', 'cancelled'])->default('draft');
                $table->decimal('subtotal', 10, 2);
                $table->decimal('tax_rate', 5, 2)->default(0);
                $table->decimal('tax_amount', 10, 2);
                $table->decimal('total', 10, 2);
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });

            Schema::create('invoice_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
                $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');
                $table->foreignId('task_id')->nullable()->constrained('tasks')->onDelete('set null');
                $table->string('description');
                $table->decimal('quantity', 8, 2);
                $table->decimal('rate', 10, 2);
                $table->decimal('amount', 10, 2);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
