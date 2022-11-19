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
        $channel_list = new ChannelList;
        
        return view('lists.create', [
            'channels' => $channels,
            'lists' => $lists,
            'channel_list' => $channel_list,
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
        
        foreach($request->selection as $id) {
            $channel_list->add($id);
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
