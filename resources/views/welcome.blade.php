@extends('layouts.app')

@section('content')
    @if (Auth::check())
        <div class="text-center">
            @if (null == $channel_list)
            <h1>すべてのチャンネル</h1>
            @else
            <h1>{{ $channel_list->name }}</h1>
            @endif
        </div>
        @include('channels.channels')
        @include('channels.videos')
    @else
    <div class="center jumbotron">
        <div class="text-center">
            <h1>ChannelChartとは？</h1>
            <h4>あなたのYouTubeアカウントで登録しているチャンネルを自由にリスト化して、<br>
            リスト内のチャンネルのコンテンツ配信状況を確認できます。</h4>
            <p><font size="2" color="#999">※ユーザー登録後、ユーザーから許可があった場合のみ、</br>
            ユーザーのYouTubeアカウントが登録しているチャンネルの情報を「YouTube Data API v3」より取得します。</br>
            取得した情報は第三者に提供しません。</font></p>
            {!! link_to_route('signup.get', 'ユーザー登録', [], ['class' => 'btn btn-lg btn-primary']) !!}
        </div>
    </div>
    @endif
@endsection