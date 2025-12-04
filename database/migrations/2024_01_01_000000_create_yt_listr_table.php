<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('yt_listr_videos', function (Blueprint $table) {
      $table->id();
      $table->string('video_id')->unique();
      $table->string('title');
      $table->text('description')->nullable();
      $table->string('thumbnail_url')->nullable();
      $table->timestamp('published_at')->nullable();
      $table->integer('duration')->nullable()->comment('Duration in seconds');
      $table->unsignedBigInteger('view_count')->default(0);
      $table->unsignedBigInteger('like_count')->default(0);
      $table->unsignedBigInteger('comment_count')->default(0);
      $table->string('channel_id')->nullable();
      $table->string('channel_title')->nullable();
      $table->timestamps();

      $table->index('published_at');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('yt_listr_videos');
  }
};
