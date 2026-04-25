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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable()->comment('書籍名');
            $table->string('author')->nullable()->comment('著者');
            $table->string('isbn')->nullable()->comment('ISBN');
            $table->string('publisher')->nullable()->comment('出版社');
            $table->date('publication_date')->nullable()->comment('出版日');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
