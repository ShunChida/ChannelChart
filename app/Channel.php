<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $fillable = [
        'channel', 'channel_id_from_youtube',
    ];
    
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
    
    public function channel_lists()
    {
        return $this->belongsToMany(ChannelList::class, 'channel_selection', 'channel_id', 'channel_list_id')->withTimestamps();
    }
}
