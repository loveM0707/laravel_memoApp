<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memo;
use App\Models\Tag;
use App\Models\MemoTag;
use DB;

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
        return view('create');
    }

    /**
     * メモの保存処理
     */
    public function store(Request $request)
    {
        // POST値を全て取得
        $posts = $request->all();

        // トランザクション
        DB::transaction(function() use($posts) {
            // メモをDBに登録
            $memoId = Memo::insertGetId(['content' => $posts['content'], 'user_id' => \Auth::id()]);

            // 新しいタグが既に存在するかどうかチェックする
            $newTag = $posts['new_tag'] ?? null;
            $isTagExist = Tag::where('user_id', '=', \Auth::id())->where('name', '=', $newTag)->exists();

            if ($newTag && !$isTagExist) {
                // タグをDBに登録
                $tagId = Tag::insertGetId(['user_id' => \Auth::id(), 'name' => $newTag]);

                // メモと紐付ける
                MemoTag::insert(['memo_id' => $memoId, 'tag_id' => $tagId]);
            };

            // 既存タグが紐づけられた場合：memo_tagsテーブルに登録
            $tags = $posts['tags'] ?? [];
            foreach ($tags as $tag) {
                MemoTag::insert(['memo_id' => $memoId, 'tag_id' => $tag]);
            }
        });

        // ページをリダイレクト
        return redirect( route('home') );
    }

    /**
     * メモを編集画面に表示
     */
    public function edit($id)
    {
        // IDから抽出する
        $editMemo = Memo::select('memos.*', 'tags.id AS tag_id')
            ->leftJoin('memo_tags', 'memo_tags.memo_id', '=', 'memos.id')
            ->leftJoin('tags', 'memo_tags.tag_id', '=', 'tags.id')
            ->where('memos.user_id', '=', \Auth::id())
            ->where('memos.id', '=', $id)
            ->whereNull('memos.deleted_at')
            ->get();

        $includeTags = [];
        foreach ($editMemo as $memo) {
            $includeTags[] = $memo['tag_id'];
        }

        return view('edit', compact('editMemo', 'includeTags'));
    }

    /**
     * メモの更新処理
     */
    public function update(Request $request)
    {
        // POST値を全て取得
        $posts = $request->all();

        // トランザクション
        DB::transaction(function() use($posts) {
            // メモのDBを更新（whereを前の方に入れる）
            Memo::where('id', $posts['memo_id'])->update(['content' => $posts['content'], 'user_id' => \Auth::id()]);

            // 既に紐付いているタグを無効にする
            MemoTag::where('memo_id', '=', $posts['memo_id'])->delete();

            // 画面上でチェックされているタグを紐付ける
            // 既存タグが紐づけられた場合：memo_tagsテーブルに登録
            $tags = $posts['tags'] ?? [];
            foreach ($tags as $tag) {
                MemoTag::insert(['memo_id' => $posts['memo_id'], 'tag_id' => $tag]);
            }

            // 新しいタグが既に存在するかどうかチェックする
            $newTag = $posts['new_tag'] ?? null;
            $isTagExist = Tag::where('user_id', '=', \Auth::id())->where('name', '=', $newTag)->exists();

            if ($newTag && !$isTagExist) {
                // タグをDBに登録
                $tagId = Tag::insertGetId(['user_id' => \Auth::id(), 'name' => $newTag]);

                // メモと紐付ける
                MemoTag::insert(['memo_id' => $posts['memo_id'], 'tag_id' => $tagId]);
            };
        });

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
