<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paragraph extends Model
{
    use HasFactory;

    protected $fillable = ['post_id', 'title', 'subtitle', 'content'];

    public function photos(){
        return $this->hasMany(Photo::class);
    }
}
