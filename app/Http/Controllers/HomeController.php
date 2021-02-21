<?php


namespace App\Http\Controllers;


use App\Models\User;

class HomeController extends BaseController
{
    private $title = 'Home';

    public function index()
    {
        $user = User::find($this->userId);
        $users = User::where('id', '!=', $this->userId)->get(['id', 'name']);
        return view('home.index', [
            'title' => $this->title,
            'projects' => $user->projects,
            'users' => $users
        ]);
    }
}
