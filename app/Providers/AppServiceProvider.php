<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Memo;
use App\Models\Tag;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 全てのメソッドが呼ばれる前に呼ばれるメソッド
        view()->composer('*', function($view) {
            $memoModel = new Memo();
            // メモ一覧を取得
            $memos = $memoModel->getMyMemo();

            // ログイン中のユーザーのタグ一覧を取得
            $tags = Tag::where('user_id', '=', \Auth::id())
            ->whereNull('deleted_at')
            ->orderBy('id', 'DESC')
            ->get();

            $view->with('memos', $memos)->with('tags', $tags);

        });
    }
}
