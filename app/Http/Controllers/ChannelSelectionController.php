<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ChannelList;

class ChannelSelectionController extends Controller
{
    public function create()
    {
        $user = \Auth::user();
        
        $channels = $user->channels()->get();
        $lists = $user->channel_lists()->get();
        
        return view('lists.create', [
            'channels' => $channels,
            'lists' => $lists,
            'channel_list' => null,
            'name' => null,
            'channel_ids_in_channel_list' => null,
        ]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:30',
        ]);
        
        \Auth::user()->channel_lists()->create([
            'name' => $request->name,
        ]);
        
        $channel_list = \Auth::user()->channel_lists()->latest()->firstOrFail();
        
        //チャンネル追加
        if (null !== $request->selection) {
            foreach($request->selection as $id) {
                $channel_list->add($id);
            }
        }
        return redirect()->route('channels.show', ['id' => $channel_list->id]);
    }
    
    public function edit($id)
    {
        $user = \Auth::user();
        
        $channels = $user->channels()->get();
        $lists = $user->channel_lists()->get();
        $channel_list = ChannelList::findOrFail($id);
        $channels_in_channel_list = $channel_list->channels()->get();
        
        //比較用チャンネルid作成
        $channel_ids_in_channel_list = [];
        foreach($channels_in_channel_list as $channel) {
            $channel_ids_in_channel_list[] = $channel['id'];
        }
        
        return view('lists.edit', [
            'channels' => $channels,
            'lists' => $lists,
            'channel_list' => $channel_list,
            'name' => $channel_list->name,
            'channel_ids_in_channel_list' => $channel_ids_in_channel_list,
        ]);
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:30',
        ]);
        
        //既存のチャンネル削除
        $channel_list = ChannelList::findOrFail($id);
        $channels = $channel_list->channels()->get();
        foreach($channels as $channel) {
            $channel_list->remove($channel['id']);
        }
        
        $channel_list->update([
            'name' => $request->name,
        ]);
        
        //新しくチャンネル追加
        if (null !== $request->selection) {
            foreach($request->selection as $id) {
                $channel_list->add($id);
            }
        }
        
        return redirect()->route('channels.show', ['id' => $channel_list->id]);
    }
    
    
    public function destroy($id)
    {
        $channel_list = ChannelList::findOrFail($id);
        $channel_list->delete();
        
        return redirect('/');
    }
    
}
