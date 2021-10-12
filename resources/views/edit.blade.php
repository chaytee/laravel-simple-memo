@extends('layouts.app')

@section('javascript')
<script src="/js/comfirm.js"></script>
@section('content')
<div class="container p-0">
	<div class="card">
		<div class="my-card-title card-header d-flex justify-content-between align-items-center">メモ編集
            <form id="delete-form" action="{{ route('destroy') }}" method="POST" id="delete-form">
                @csrf
                {{--複数行とれる場合は[0]を指定する。何番目の何をとるのか--}}
                <input type="hidden" name="memo_id" value="{{ $edit_memo[0]['id'] }}" />
                <i class="fas fa-trash mb-0 mt-1" onclick="deleteHandle(event);"></i>
            </form>
        </div>
        {{--route('store')と書くと/storeになる urlが変わってもOK--}}
		<form class="card-body my-card-body" action="{{ route('update') }}" method="POST">
			@csrf
            {{--どのidなのかコントローラーに教える必要がある--}}
            <input type="hidden" name="memo_id" value="{{$edit_memo[0]['id']}}">
			<div class="form-group">
				<textarea class="form-control" name="content" rows="3" placeholder="ここにメモを入力">{{ $edit_memo[0]['content'] }}</textarea>
                @error('content')
                    <div class="alert-danger alert mt-3">メモを入力してください！</div>
                @enderror
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
