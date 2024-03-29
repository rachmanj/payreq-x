<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('general_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id');
            $table->date('posting_date');
            $table->string('document_type')->nullable();
            $table->string('journal_no');
            $table->string('remarks')->nullable();
            $table->string('project', 10)->nullable();
            $table->foreignId('cost_center_id')->nullable(); // department_id
            $table->decimal('debit', 19, 2)->nullable();
            $table->decimal('credit', 19, 2)->nullable();
            $table->foreignId('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_ledgers');
    }
};
