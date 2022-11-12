<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10 channels" style="margin-bottom:10px">						
            <p class="margin-standard">登録チャンネル</p>
        
            <div class="d-flex flex-wrap" style="margin-bottom:5px">
                @foreach ($channels as $channel)
                    <a href="http://www.youtube.com/channel/{{ $channel['snippet']['resourceId']['channelId'] }}">
                        <div style="width:60px">
                            <img class="rounded-circle img-fluid icon" src="{{ $channel['snippet']['thumbnails']['default']['url'] }}" />
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>