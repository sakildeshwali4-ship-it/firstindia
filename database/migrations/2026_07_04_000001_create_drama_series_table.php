<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drama_series', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('poster_url');
            $table->string('cover_url');
            $table->string('genre')->index();
            $table->string('language')->index();
            $table->decimal('rating', 2, 1)->default(0);
            $table->unsignedInteger('total_episodes')->default(0);
            $table->boolean('is_premium')->default(false);
            $table->string('status')->default('published')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drama_series');
    }
};
