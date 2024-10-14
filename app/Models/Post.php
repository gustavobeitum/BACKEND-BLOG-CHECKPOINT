<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    
    protected $fillable = ['user_id','title','type','image','description' ];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function paragraphs(){
        return $this->hasMany(Paragraph::class);
    }
    public function comments(){
        return $this->hasMany(Comment::class)->with('answers');
    }
}
