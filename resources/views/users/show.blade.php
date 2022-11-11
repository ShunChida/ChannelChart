@extends('layouts.app')

@section('content')
    <div class="text-center">
        <h1>設定</h1>
    </div>

    <div class="row">
        <div class="col-sm-6 offset-sm-3">
            <p>
                ユーザー名：{{ $user->name }}<br>
                メールアドレス：{{ $user->email }}
            </p>
            
            <button type="button" class="btn btn-warning btn-block">{!! link_to_route('logout.get', 'ログアウト', [], ['class' => 'text-white text-decoration-none']) !!}</button>
            
            {{-- ポップアップ実装予定 --}}
            {!! Form::open(['route' => ['users.destroy', $user->id], 'method' => 'delete']) !!}
                {!! Form::submit('アカウントの削除', ['class' => "btn btn-danger btn-block"]) !!}
            {!! Form::close() !!}
        </div>
    </div>
@endsection