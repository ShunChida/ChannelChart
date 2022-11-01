@extends('layouts.app')

@section('content')
    <div class="center jumbotron">
        <div class="text-center">
            <h1>ChannelChartとは？</h1>
            <h4>あなたのYouTubeアカウントで登録しているチャンネルを自由にリスト化して、<br>
            リスト内のチャンネルのコンテンツ配信状況を確認できます。</h4>
            {!! link_to_route('signup.get', 'ユーザー登録', [], ['class' => 'btn btn-lg btn-primary']) !!}
        </div>
    </div>
@endsection