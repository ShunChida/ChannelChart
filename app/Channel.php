<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $fillable = ['channel'];
    
    protected $casts = [
        'channel' => 'json',
    ];    
        
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function videos()
    {
        return $this->hasMany(Video::class);
    }
}
