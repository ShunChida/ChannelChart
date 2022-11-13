@php    
    $video_published_at = new \DateTime($video['video']['snippet']['publishedAt']);
    $interval = $now->diff($video_published_at);
    
    $its_channel_id = $video['channel_id'];
    $its_channel = $channels->find($its_channel_id);
    $its_channel_icon = $its_channel['channel']['snippet']['thumbnails']['default']['url'];
@endphp
<div class="content col-6 col-sm-4 col-md-3">
    <a href="http://www.youtube.com/watch?v={{ $video['video']['id']['videoId'] }}" class="thumbnail" target="_blank">
        
        <div class="container" style="padding:1px;">
            <div class="row" style="width:100%;margin:0;">
                
                <div class="col-12 col-sm-12 col-md-12" style="padding:0 0 5px 0;text-align: center;">
                    <img class="rounded" src="{{ $video['video']['snippet']['thumbnails']['medium']['url'] }}" style="box-shadow: 0 0 5px 5px black inset;">
                </div>
                
                <div class="container">
                    <div class="row">
                        
                        <div class="col-3 col-sm-3 col-md-3" style="padding:0;">
                            <div class="row no-gutters justify-content-between" style="height: 60px;overflow: hidden;">
                                <div style="width:60px;text-align: center; margin-left:5px">
                                    <img class="rounded-circle border border-4 img-fluid icon" src="{{ $its_channel_icon }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-9 col-sm-9 col-md-9" style="padding:0;">
                            <div class="row no-gutters">
                                <div class="title">
                                    {{ $video['video']['snippet']['title'] }}
                                </div>
                            </div>
                        </div>
                    
                    </div>
                </div>
                <div class="container">
                    <div class="row">
                        
                        <div class="col-8 col-sm-8 col-md-8 text-left text-truncate" style="line-height:30px; padding:0 0 0 5px;">
                            {{ $video['video']['snippet']['channelTitle'] }}
                        </div>
                        
                        <div class="col text-right" style="line-height:30px;margin-right:5px; padding:0;">
                            {{ $video_published_at->format('H:i') }}
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>