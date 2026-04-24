<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Project collaborators (other devs with access)
        Schema::create('collaborators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->string('name')->nullable();
            $table->string('invite_token', 64)->unique()->nullable();
            $table->enum('role', ['viewer', 'editor'])->default('editor');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'email']);
        });

        // AI suggestions cache — so we don't re-query on every page load
        Schema::create('ai_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();

            $table->string('title');
            $table->text('rationale');  // Why the AI thinks this is needed
            $table->string('source_project')->nullable(); // "You built this in Pweza Delivery"

            $table->enum('status', ['pending', 'accepted', 'dismissed'])->default('pending');
            $table->timestamp('generated_at');
            $table->timestamps();
        });

        // AI memory — cross-project context stored after archiving
        Schema::create('project_memories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();

            $table->string('project_name'); // Kept even if project deleted
            $table->json('stack_tags');
            $table->json('shipped_features');  // Titles of what shipped
            $table->json('cut_features');      // What was planned but cut
            $table->json('ai_ignored');        // What you dismissed from AI suggestions
            $table->text('retrospective')->nullable(); // Optional notes on the project
            $table->integer('days_to_ship')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_memories');
        Schema::dropIfExists('ai_suggestions');
        Schema::dropIfExists('collaborators');
    }
};
