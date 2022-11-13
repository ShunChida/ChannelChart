<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChannelList extends Model
{
    protected $fillable = ['name'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
