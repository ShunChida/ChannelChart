<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10 channels" style="margin-bottom:10px">	
        
        <div class="container">
            <div class="row justify-content-between">
                <div class="col">
                    <h6 class="margin-standard" style="transform: translate(0px, 12px);">登録チャンネル</h6>
                </div>
                
                @if (null !== $channel_list)
                <div class="col">
                    <div class="container">
                        <div class="row justify-content-end">
                                {!! Form::model($channel_list, ['route' => ['lists.edit', $channel_list->id], 'method' => 'get']) !!}
                                    {!! Form::submit('編集', ['class' => 'btn btn-success float-right', 'style' => 'transform: translate(20px, 10px); margin-right: 12px;']) !!}
                                {!! Form::close() !!}
                                
                                {!! Form::model($channel_list, ['route' => ['lists.destroy', $channel_list->id], 'method' => 'delete']) !!}
                                    {!! Form::submit('削除', ['class' => 'btn btn-danger float-right', 'style' => 'transform: translate(20px, 10px);']) !!}
                                {!! Form::close() !!}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        <div class="container" style="margin-bottom:15px"></div>

            <div class="d-flex flex-wrap" style="margin-bottom:7px">
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