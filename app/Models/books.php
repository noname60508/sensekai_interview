<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\booksFactory;

class books extends Model
{
    use SoftDeletes, HasFactory;

    // テーブル名
    protected $table = 'books';
    // 主キー
    protected $primaryKey = 'id';
    // タイムスタンプ
    public $timestamps = true;
    protected $guarded = [];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return booksFactory::new();
    }
}
