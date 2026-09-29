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
        Schema::table('technologies', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->string('slug')->unique()->after('name');
        });

        Schema::create('tech_stack_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('tech_stack_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('tech_stack_group_category', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->foreignIdFor(\App\Models\TechStackGroup::class, 'group_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\TechStackCategory::class, 'category_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['group_id', 'category_id']);
        });

        Schema::create('tech_stack_placements', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\TechStackGroupCategory::class, 'group_category_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Technology::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['group_category_id', 'technology_id']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tech_stack_placements');
        Schema::dropIfExists('tech_stack_group_category');
        Schema::dropIfExists('tech_stack_categories');
        Schema::dropIfExists('tech_stack_groups');

        Schema::table('technologies', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn(['name', 'slug']);
        });
    }
};
