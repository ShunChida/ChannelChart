<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;

class UsersController extends Controller
{
    public function show($id)
    {
        $user = User::findOrFail($id);
        
        
        return view('users.show', [
            'user' => $user,
            'lists' => $user->channel_lists()->get(),
        ]);
    }
    
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if (\Auth::id() === $user->id) {
            $user->delete();
        }
        return redirect('/')->send();
    }
}
