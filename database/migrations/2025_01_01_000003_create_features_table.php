<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();   // What this feature does — shown on approval page

            $table->enum('status', [
                'backlog',           // Not started, no approval needed
                'awaiting_approval', // Sent to client, waiting
                'approved',          // Client said go
                'building',          // You're actively working on it
                'review',            // PR open, in testing
                'shipped',           // Live
                'cancelled',         // Won't do
            ])->default('backlog');

            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');

            // Who owns / is watching this feature
            $table->enum('type', [
                'personal',      // You build, no approval needed
                'client',        // Client-facing, may need approval
                'collaborative', // Another dev is involved
            ])->default('personal');

            // Approval system
            $table->boolean('needs_approval')->default(false);
            $table->string('approval_token', 64)->unique()->nullable();
            $table->timestamp('approval_requested_at')->nullable();

            // AI-generated vs manually added
            $table->boolean('ai_suggested')->default(false);
            $table->text('ai_rationale')->nullable(); // Why the AI suggested it

            // Ordering within project (drag-to-reorder later)
            $table->unsignedInteger('sort_order')->default(0);

            // Optional: link to PR or external resource
            $table->string('external_url')->nullable();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('shipped_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('features');
    }
};
