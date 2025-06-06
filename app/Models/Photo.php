<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = ['paragraph_id', 'photo'];

    public function paragraph(){
        return $this->belongsTo(Paragraph::class, 'paragraph_id');
    }
}
