<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Memo extends Model
{
    use HasFactory;

    /**
     * メモ一覧を取得
     */
    public function getMyMemo()
    {
        $memos = [];

        // タグの絞り込み
        $queryTag = \Request::query('tag');

        // 共通クエリ
        $query = Memo::query()->select('memos.*')
            ->where('user_id', '=', \Auth::id())
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'DESC');

        // タグが指定されていたら、メモをタグIDで絞り込む
        if ($queryTag) {
            $query->leftJoin('memo_tags', 'memo_tags.memo_id', '=', 'memos.id')
            ->where('memo_tags.tag_id', '=', $queryTag)
            ->where('user_id', '=', \Auth::id());
        }

        $memos = $query->get();

        return $memos;
    }
}
