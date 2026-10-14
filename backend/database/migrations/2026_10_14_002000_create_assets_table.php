<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();

            $table->morphs('assetable');
            $table->string('collection');
            $table->string('disk');
            $table->string('path');
            $table->string('original_name')->nullable();
            $table->string('mime', 127)->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['assetable_type', 'assetable_id', 'collection'], 'assets_assetable_collection_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
