<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->unsignedSmallInteger('order')->default(0);
            $table->text('explanation')->nullable()
                  ->comment('Shown after quiz to explain correct answer');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->boolean('is_correct')->default(false);
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('attempt_number');
            $table->unsignedTinyInteger('score')->default(0);
            $table->unsignedTinyInteger('total_questions')->default(0);
            $table->unsignedInteger('time_taken')->nullable()
                  ->comment('Time taken in seconds');
            $table->json('question_order')->nullable()
                  ->comment('Ordered array of question IDs shown to user');
            $table->enum('status', ['in_progress', 'completed', 'timed_out'])
                  ->default('in_progress');
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['quiz_id', 'user_id', 'attempt_number']);
            $table->index(['user_id', 'quiz_id']);
        });

        Schema::create('attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('answer_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_correct')->default(false);
            $table->timestamps();

            $table->unique(['quiz_attempt_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attempt_answers');
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('answers');
        Schema::dropIfExists('questions');
    }
};
