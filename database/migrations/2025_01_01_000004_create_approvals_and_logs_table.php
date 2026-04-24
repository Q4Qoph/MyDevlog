<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One approval record per feature approval request
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feature_id')->constrained()->cascadeOnDelete();

            $table->enum('decision', ['pending', 'approved', 'changes_requested'])->default('pending');
            $table->text('client_note')->nullable();   // Client's message when requesting changes
            $table->string('client_name')->nullable();
            $table->string('client_email')->nullable();

            $table->string('ip_address', 45)->nullable(); // For audit trail
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();
        });

        // Immutable log of everything that happens to a feature
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feature_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // e.g. "status_changed", "approval_sent", "approval_received", "note_added"
            $table->string('event');
            $table->json('meta')->nullable(); // {"from": "backlog", "to": "building"} etc.

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('approvals');
    }
};
