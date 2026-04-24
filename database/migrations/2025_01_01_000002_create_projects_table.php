<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('client_name')->nullable();
            $table->string('client_email')->nullable();

            // The AI context block — what the AI reads to suggest features
            $table->text('brief')->nullable();         // What the app does, who uses it
            $table->string('stack')->nullable();        // "React, TypeScript, Laravel, MySQL"
            $table->json('tech_tags')->nullable();      // ["react","typescript","laravel"] for AI matching

            $table->enum('status', ['planning', 'active', 'review', 'shipped', 'archived'])
                  ->default('planning');

            $table->string('color', 7)->default('#7c6af7'); // Sidebar dot color
            $table->date('deadline')->nullable();

            // Share token for read-only client view
            $table->string('share_token', 64)->unique()->nullable();
            $table->boolean('share_enabled')->default(false);

            $table->timestamp('shipped_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
