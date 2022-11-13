<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = ['video'];
    
    protected $casts = [
        'video' => 'json',
    ];    
        
    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }
}
