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
    
    public function channels()
    {
        return $this->belongsToMany(Channel::class, 'channel_selection', 'channel_list_id', 'channel_id')->withTimestamps();
    }
    
    public function add($channel_id)
    {
        if ($this->is_added($channel_id)) {
            // すでに追加している場合は何もしない
            return false;
        } else {
            // 上記以外は追加する
            $this->channels()->attach($channel_id);
            return true;
        }
    }
    
    public function remove($channel_id)
    {
        if (!$this->is_added($channel_id)) {
            // 追加していない場合は何もしない
            return false;
        } else {
            // 上記以外は削除する
            $this->channels()->detach($channel_id);
            return true;
        }
    }
    
    public function is_added($channel_id)
    {
        // 既に追加しているか
        return $this->channels()->where('channel_id', $channel_id)->exists();
    }
}
