@extends('layouts.app')

@section('content')
    <div class="text-center">
        <h1>設定</h1>
    </div>

    <div class="row">
        <div class="col-sm-6 offset-sm-3">
            <div class="text-center" style="margin: 15px 0 30px;">
                <p>
                    ユーザー名：{{ $user->name }}<br>
                    メールアドレス：{{ $user->email }}
                </p>
            </div>
            
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-6">
                        <button type="button" class="btn btn-warning btn-block" style="margin-bottom: 30px;">{!! link_to_route('logout.get', 'ログアウト', [], ['class' => 'text-white text-decoration-none']) !!}</button>
                    </div>
                </div>
            </div>
            
            
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-6">
                        {{-- ポップアップ実装予定 --}}
                        {!! Form::open(['route' => ['users.destroy', $user->id], 'method' => 'delete']) !!}
                            {!! Form::submit('アカウントの削除', ['class' => "btn btn-danger btn-block"]) !!}
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
            
        </div>
    </div>
@endsection