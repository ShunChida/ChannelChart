<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $fillable = [
        'access_token', 'refresh_token_exists',
    ];
    
    protected $casts = [
        'access_token' => 'json'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
