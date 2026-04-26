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
        Schema::table('books', function (Blueprint $table) {
            $table->renameColumn('isbn', 'isbn10');
            $table->string('isbn10')->comment('ISBN10')->change();
            $table->string('isbn13')->nullable()->comment('ISBN13')->after('isbn10');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('isbn13');
            $table->renameColumn('isbn10', 'isbn');
        });
    }
};
