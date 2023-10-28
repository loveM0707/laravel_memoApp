<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memo;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // ログイン中のユーザーのメモ一覧を取得
        $memos = Memo::select('memos.*')
            ->where('user_id', '=', \Auth::id())
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'DESC')
            ->get();

        return view('create', compact('memos'));
    }

    /**
     * メモの保存処理
     */
    public function store(Request $request)
    {
        // POST値を全て取得
        $posts = $request->all();

        // DBに保存
        Memo::insert(['content' => $posts['content'], 'user_id' => \Auth::id()]);

        // ページをリダイレクト
        return redirect( route('home') );
    }
}
