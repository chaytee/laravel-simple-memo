<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Memo;
use App\Models\Tag;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //View::composer(...)
        View::composer('*', function($view){
            //もしクエリパラメータtagがあればタグで絞;込み
            //バッグスラッシュはuse書いてなくても書ける
            $query_tag=\Request::query('tag');
            if(!empty($query_tag)){
                //絞り込みをする
                $memos = Memo::select('memos.*')
                //memo_tagsとmemoのテーブルをくっつける
                ->leftJoin('memo_tags', 'memo_tags.memo_id', '=', 'memos.id')
                //クエリパラメータと一致→絞り込み
                ->where('memo_tags.tag_id', '=', $query_tag)
                ->where('user_id', '=', \Auth::id())
                ->whereNull('deleted_at')
                ->orderBy('updated_at', 'DESC')// ASC＝小さい順、DESC=大きい順
                ->get();
            }else {
                //全て取得
                $memos = Memo::select('memos.*')
                ->where('user_id', '=', \Auth::id())
                ->whereNull('deleted_at')
                ->orderBy('updated_at', 'DESC')// ASC＝小さい順、DESC=大きい順
                ->get();
            }


            $tags = Tag::where('user_id', '=', \Auth::id())
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'DESC')
            ->get();

            //viewで使用する命名、第二引数は編数または配列
            $view->with('memos', $memos)->with('tags', $tags);
        });
    }
}
