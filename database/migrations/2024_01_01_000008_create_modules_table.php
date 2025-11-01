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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('version')->default('1.0.0');
            $table->boolean('is_core')->default(false);
            $table->boolean('enabled')->default(false);
            $table->timestamp('installed_at')->nullable();
            $table->timestamps();

            $table->index(['slug', 'enabled']);
        });

        Schema::create('module_dependencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->string('required_module_slug');
            $table->string('minimum_version')->nullable();
            $table->timestamps();

            $table->index('module_id');
            $table->index('required_module_slug');
        });

        Schema::create('module_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('key');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['module_id', 'team_id', 'key']);
            $table->index('team_id');
        });

        Schema::create('installed_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->timestamp('installed_at');
            $table->foreignId('installed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['team_id', 'module_id']);
            $table->index('team_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installed_modules');
        Schema::dropIfExists('module_settings');
        Schema::dropIfExists('module_dependencies');
        Schema::dropIfExists('modules');
    }
};
