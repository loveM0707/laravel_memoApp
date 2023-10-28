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

    /**
     * メモを編集画面に表示
     */
    public function edit($id)
    {
        // ログイン中のユーザーのメモ一覧を取得
        $memos = Memo::select('memos.*')
            ->where('user_id', '=', \Auth::id())
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'DESC')
            ->get();

        // IDから抽出する
        $edit_memo = Memo::find($id);

        return view('edit', compact('memos', 'edit_memo'));
    }

    /**
     * メモの更新処理
     */
    public function update(Request $request)
    {
        // POST値を全て取得
        $posts = $request->all();

        // DBを更新（whereを前の方に入れる）
        Memo::where('id', $posts['memo_id'])->update(['content' => $posts['content'], 'user_id' => \Auth::id()]);

        // ページをリダイレクト
        return redirect( route('home') );
    }

    /**
     * メモの削除処理
     */
    public function destroy(Request $request)
    {
        // POST値を全て取得
        $posts = $request->all();

        // DBを更新（whereを前の方に入れる）
        Memo::where('id', $posts['memo_id'])->update(['deleted_at' => date("Y-m-d H:i:s", time())]);

        // ページをリダイレクト
        return redirect( route('home') );
    }
}
