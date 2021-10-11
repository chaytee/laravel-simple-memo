@extends('layouts.app')

@section('content')
<div class="container p-0">
	<div class="card">
		<div class="card-header">メモ編集
            <form class="card-body" action="{{ route('destroy') }}" method="POST">
                @csrf
                {{--複数行とれる場合は[0]を指定する。何番目の何をとるのか--}}
                <input type="hidden" name="memo_id" value="{{ $edit_memo[0]['id'] }}" />
                <button type="submit">削除</button>
            </form>
        </div>
        {{--route('store')と書くと/storeになる urlが変わってもOK--}}
		<form class="card-body" action="{{ route('update') }}" method="POST">
			@csrf
            {{--どのidなのかコントローラーに教える必要がある--}}
            <input type="hidden" name="memo_id" value="{{$edit_memo[0]['id']}}">
			<div class="form-group">
				<textarea class="form-control" name="content" rows="3" placeholder="ここにメモを入力">{{ $edit_memo[0]['content'] }}</textarea>
			</div>
            @foreach ($tags as $t)
                <div class="form-check-inline mb-3">
                    {{--デフォルトチェック機能 $include_tagsにループで回っているタグのidが含まれればcheckedを入れる。name="tags[]"は配列で受け取りたい--}}
                    <input class="form-check-input" type="checkbox" name="tags[]" id="{{ $t['id'] }}" value="{{ $t['id'] }}" {{in_array($t['id'], $inclued_tags) ? 'checked': ''}}>
                    {{--labelのforとinputのidは同じ値--}}
                    <label class="form-check-label" for="{{ $t['id'] }}">{{ $t['name']}}</label>
                </div>
            @endforeach
            <input type="text" class="form-control mb-3 w-50" name="new_tag" placeholder="新しいタグを入力" />
            <button type="submit" class="btn btn-primary">更新</button>
		</form>
	</div>
</div>
@endsection
