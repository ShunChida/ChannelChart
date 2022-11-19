<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10 channels" style="margin-bottom:10px">	
        
        <div class="container">
            <div class="row">
                <div class="col-8">
                    <p class="margin-standard">登録チャンネル</p>
                </div>
                
                @if (null !== $channel_list)
                <div class="col">
                    {!! Form::model($channel_list, ['route' => ['lists.destroy', $channel_list->id], 'method' => 'delete']) !!}
                        {!! Form::submit('削除', ['class' => 'btn btn-danger float-right', 'style' => 'transform: translate(18px, 10px);']) !!}
                    {!! Form::close() !!}
                </div>
                @endif
            </div>
        </div>


            <div class="d-flex flex-wrap" style="margin-bottom:5px">
                @foreach ($channels as $channel)
                    <a href="http://www.youtube.com/channel/{{ $channel['channel']['snippet']['resourceId']['channelId'] }}" target="_blank">
                        <div style="width:60px">
                            <img class="rounded-circle img-fluid icon" src="{{ $channel['channel']['snippet']['thumbnails']['default']['url'] }}" />
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>