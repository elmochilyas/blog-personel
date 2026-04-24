<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = ['title', 'content', 'status', 'category_id', 'user_id', 'published_at'];
    protected $casts = ['published_at' => 'datetime'];
    public function category() { return $this->belongsTo(Category::class); }
    public function user() { return $this->belongsTo(User::class); }
}
