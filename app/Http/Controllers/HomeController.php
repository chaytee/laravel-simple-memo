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
            //タグの中からuser_idがログインしている、deleted_atがNullの時だけ
            $tags = Tag::where('user_id', '=', \Auth::id())->whereNull('deleted_at')->orderBy('id', 'DESC')->get();

            //compact('変数') viewに渡したいもの
        return view('create', compact('tags'));
    }
    public function store(Request $request)
    {
        $posts= $request->all();

        //ここからトランザクション
        //insertGetIdはデータをゲットしてIDに返す
        DB::transaction(function() use($posts) {
            // メモIDをインサートして取得
            $memo_id = Memo::insertGetId(['content' => $posts['content'], 'user_id' => \Auth::id()]);
            //データベースに存在するかいなか
            $tag_exists = Tag::where('user_id', '=', \Auth::id())->where('name', '=', $posts['new_tag'])->exists();
            // 新規タグの入力あり、入力されたものが存在しない場合はtagsテーブルにインサート。whereが続いたら「かつ」
            if( !empty($posts['new_tag']) && !$tag_exists ){
            //tagsテーブルにインサートし→IDを取得
                $tag_id = Tag::insertGetId(['user_id' => \Auth::id(), 'name' => $posts['new_tag']]);
                // memo_tagsにインサートして、メモとタグを紐付ける
                MemoTag::insert(['memo_id' => $memo_id, 'tag_id' => $tag_id]);
            }
            // 既存タグが紐付けられた場合→memo_tagsにインサート
            //undefinded indexエラーのため[0]
            //tagsはviewのnameの部分のこと。これが空でない場合は以下のMemoTagにデータを追加する
            if(!empty($posts['tags'][0])){
                foreach($posts['tags'] as $tag){
                    MemoTag::insert(['memo_id' => $memo_id, 'tag_id' => $tag]);
                }
            }
        });
        //dump dieの略　→メソッドの引数の取った値を展開して止める→データ確認
        //dd(\Auth::id());
        //データベースを入れるinsert(データベースのカラム名=>ユーザーが入れてきたもの)配列
        //テーブルのカラムメイト一致させる

        return redirect( route('home'));
    }
    public function edit($id)
    {
            //Memoから一つしかないidをとる
            //joinだけだとtagの紐づけがないタグを取得できない
            //memo_tags.memo_idとmemos.idをくっつける
            //.でどこのテーブルか指す。重複したものはエラーになるので。
        $edit_memo = Memo::select('memos.*', 'tags.id AS tag_id')
            ->leftJoin('memo_tags', 'memo_tags.memo_id', '=', 'memos.id')
            ->leftJoin('tags', 'memo_tags.tag_id', '=', 'tags.id')
            ->where('memos.user_id', '=', \Auth::id())
            ->where('memos.id', '=', $id)
            ->whereNull('memos.deleted_at')
            ->get();
            //複数取得したい場合はfindではなくget

            //編集画面で表示されたものの配列を作成する
        $inclued_tags = [];
        foreach($edit_memo as $memo){
            array_push($inclued_tags, $memo['tag_id']);
        }
        $tags = Tag::where('user_id', '=', \Auth::id())->whereNull('deleted_at')->orderBy('id', 'DESC')->get();

            //compact('変数')渡したいもの
        return view('edit', compact('edit_memo', 'inclued_tags', 'tags'));
    }
    public function update(Request $request)
    {
        $posts= $request->all();
        //dump dieの略　→メソッドの引数の取った値を展開して止める→データ確認
        //dd(\Auth::id());
        //データベースを入れるinsert(データベースのカラム名=>ユーザーが入れてきたもの)配列
        //テーブルのカラムメイト一致させる
        //どれをアップデートするかwhere
        DB::transaction(function () use($posts) {
            Memo::where('id', $posts['memo_id'])->update(['content' => $posts['content'], 'user_id' => \Auth::id()]);
            //メモとタグの紐づけを削除する
            MemoTag::where('memo_id', '=', $posts['memo_id'])->delete();
            //再度紐づけ
            foreach ($posts['tags'] as $tag) {
                Memotag::insert(['memo_id'=> $posts['memo_id'], 'tag_id' => $tag]);
            }
            //もし新しいタグが投げられてきたら
            //データベースに存在するかいなか
            $tag_exists = Tag::where('user_id', '=', \Auth::id())->where('name', '=', $posts['new_tag'])->exists();
            // 新規タグの入力あり、入力されたものが存在しない場合はtagsテーブルにインサート。whereが続いたら「かつ」
            if( !empty($posts['new_tag']) && !$tag_exists ){
            //tagsテーブルにインサートし→IDを取得
                $tag_id = Tag::insertGetId(['user_id' => \Auth::id(), 'name' => $posts['new_tag']]);
                // memo_tagsにインサートして、メモとタグを紐付ける
                MemoTag::insert(['memo_id' => $posts['memo_id'], 'tag_id' => $tag_id]);
            }
        });




        return redirect( route('home'));
    }
    public function destroy(Request $request)
    {
        $posts= $request->all();

        //deleat()はしない。物理的に全て削除してしまう。削除した日付を入れる。
        //そもそもデータ定義でdeleted_atに値が入った時点でデータ取得に当たらない
        //論理削除となる
        Memo::where('id', $posts['memo_id'])->update(['deleted_at' => date("Y-m-d H:i:s", time())]);

        return redirect( route('home'));
    }
}
