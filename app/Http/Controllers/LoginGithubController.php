<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class LoginGithubController extends Controller
{
    public function get(Request $request){
        return Socialite::driver('github')->redirect();
    }
    public function login(){
        $githubUser = Socialite::driver('github')->user();
 
        $user = User::updateOrCreate([
            'email' => $githubUser->email,
        ], [
            'name' => $githubUser->name,
            'password' => 'password',
        ]);
    
        Auth::login($user);
        return redirect('/dashboard');
    }
}
