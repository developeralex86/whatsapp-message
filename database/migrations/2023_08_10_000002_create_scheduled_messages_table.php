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
        Schema::create('scheduled_messages', function (Blueprint $table) {
            $table->id();
            $table->enum('message_type', ['direct', 'template']);
            $table->text('direct_message')->nullable();
            $table->foreignId('template_id')->nullable()->constrained('message_templates')->nullOnDelete();
            $table->dateTime('scheduled_at');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->json('variables')->nullable();
            $table->integer('batch_size')->default(20);
            $table->integer('batch_delay')->default(60); // seconds between batches
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('scheduled_message_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheduled_message_id')->constrained()->onDelete('cascade');
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->dateTime('sent_at')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
            
            $table->unique(['scheduled_message_id', 'contact_id'], 'smc_message_contact_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_message_contacts');
        Schema::dropIfExists('scheduled_messages');
    }
};
