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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->string('vendor_name')->nullable();
            $table->string('vendor_email')->nullable();
            $table->string('vendor_phone')->nullable();
            $table->string('vendor_address')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('account_number')->nullable();
            $table->string('invoice_number')->nullable();
            $table->date('invoice_date')->nullable();
            $table->date('due_date')->nullable();
            $table->decimal('total_amount', 15, 2)->nullable();
            $table->decimal('tax_amount', 15, 2)->nullable();
            $table->string('currency', 10)->nullable();
            $table->string('original_file_path'); // Path file yang diupload
            $table->json('line_items')->nullable();
            $table->enum('status', ['pending', 'processing', 'failed', 'processed'])->default('pending');
            $table->json('raw_ai_response')->nullable(); // Untuk menyimpan response mentah dari AI
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
