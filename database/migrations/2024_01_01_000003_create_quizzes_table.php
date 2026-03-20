<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('time_limit')->default(300)
                  ->comment('Seconds. 0 = no limit.');
            $table->unsignedTinyInteger('questions_per_attempt')->default(10);
            $table->unsignedTinyInteger('max_attempts')->default(3);
            $table->boolean('randomize_questions')->default(true);
            $table->boolean('randomize_answers')->default(true);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
