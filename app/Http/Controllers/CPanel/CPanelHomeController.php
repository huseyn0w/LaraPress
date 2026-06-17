<?php

namespace App\Http\Controllers\CPanel;


use App\Http\Models\Comments;
use App\Http\Models\Post;
use App\Http\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CPanelHomeController extends CPanelBaseController
{
    public function  __construct()
    {
        parent::__construct();
    }


    public function index(Request $request)
    {
        $count = 5;
        $posts = $this->getPosts($count);
        $users = $this->getUsers($count);
        $comments = $this->getComments($count);

        return view('cpanel.home', compact('posts','users', 'comments'));
    }

    private function getPosts($count)
    {
        // listsTranslations() joins post_translations, so `id` must be
        // qualified to avoid an ambiguous-column error.
        $posts = Post::listsTranslations('title')->orderBy('posts.id', 'desc')->take($count)->get();
        return $posts;
    }

    private function getUsers($count)
    {
        $users = User::select('username')->orderBy('id', 'desc')->take($count)->get();
        return $users;
    }

    private function getComments($count)
    {
        $pages = Comments::select('comment')->orderBy('id', 'desc')->take($count)->get();
        return $pages;
    }



}
