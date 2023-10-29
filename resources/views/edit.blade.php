@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        メモ編集
        <form class="card-body" action="{{ route('destroy') }}" method="POST">
            @csrf
            <input type="hidden" name="memo_id" value="{{ $editMemo[0]['id'] }}">
            <button type="submit">削除</button>
        </form>
    </div>
    <form class="card-body" action="{{ route('update') }}" method="POST">
        @csrf
        <input type="hidden" name="memo_id" value="{{ $editMemo[0]['id'] }}">
        <div class="input-group mb-3">
            <textarea class="form-control" name="content" rows="3" placeholder="ここにメモを入力">{{ $editMemo[0]['content'] }}</textarea>
        </div>
        @error('content')
            <div class="alert alert-danger">メモ内容を入力してください。</div>
        @enderror
        @foreach ($tags as $tag)
            <div class="form-check form-check-inline mb-3">
                <input class="form-check-input" type="checkbox" name="tags[]" id="tag-{{ $tag['id'] }}" value="{{ $tag['id'] }}" {{ in_array($tag['id'], $includeTags) ? 'checked' : ''}}>
                <label class="form-check-label" for="tag-{{ $tag['id'] }}">{{ $tag['name'] }}</label>
            </div>
        @endforeach
        <input type="text" class="form-control w-50 mb-3" name="new_tag" placeholder="新しいタグを入力">
        <button type="submit" class="btn btn-primary">更新</button>
    </form>
</div>
@endsection
