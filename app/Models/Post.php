<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['url', 'title', 'description', 'published_at', 'guid'];
    protected $hidden = ['id', 'feed_id', 'guid', 'created_at', 'updated_at'];
}
