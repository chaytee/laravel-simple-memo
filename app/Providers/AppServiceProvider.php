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
            //外部のモデルを使用するためにインスタンス化
            $memo_model= new Memo();
            //メモの取得。詳細はモデルを見てください
            //出来るだけプロバイダーやビューコンポーザーはスッキリさせる
            $memos = $memo_model->getMyMemo();


            $tags = Tag::where('user_id', '=', \Auth::id())
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'DESC')
            ->get();

            //viewで使用する命名、第二引数は編数または配列
            $view->with('memos', $memos)->with('tags', $tags);
        });
    }
}
