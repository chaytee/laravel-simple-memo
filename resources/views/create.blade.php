@extends('layouts.app')

@section('content')
<div class="container p-0">
	<div class="card">
		<div class="card-header">新規メモ作成</div>
        {{--route('store')と書くと/storeになる urlが変わってもOK--}}
		<form class="card-body" action="{{ route('store') }}" method="POST">
			@csrf
			<div class="form-group">
				<textarea class="form-control" name="content" rows="3" placeholder="ここにメモを入力"></textarea>
			</div>
            {{--複数のtagsから単数のtagを回す。name="tags[]"とすると配列で取得可能になる→forechで回す--}}
            @foreach ($tags as $t)
                <div class="form-check-inline mb-3">
                    <input class="form-check-input" type="checkbox" name="tags[]" id="{{ $t['id'] }}" value="{{ $t['id'] }}">
                    {{--labelのforとinputのidは同じ値--}}
                    <label class="form-check-label" for="{{ $t['id'] }}">{{ $t['name']}}</label>
                </div>
            @endforeach
            <input type="text" class="form-control mb-3 w-50" name="new_tag" placeholder="新しいタグを入力" />
            <button type="submit" class="btn btn-primary">保存</button>
		</form>
	</div>
</div>
@endsection
