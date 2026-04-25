<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class books extends Model
{
    use SoftDeletes;

    // テーブル名
    protected $table = 'books';
    // 主キー
    protected $primaryKey = 'id';
    // タイムスタンプ
    public $timestamps = true;
    protected $guarded = [];
}
