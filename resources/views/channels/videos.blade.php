<div class="container">
    <div class="row">
        <div class="col-12 col-sm-12 col-md-12">
                <p class="line">今日</p>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-12 col-sm-12 col-md-12">
            <div class="row">
                @php
                    $now = new \DateTime();
                    $day_group = 0;
                @endphp
                
                @foreach ($videos as $video)
                
                    @php
                        $video_published_at = new \DateTime($video['video']['snippet']['publishedAt']);
                        $interval = $now->diff($video_published_at);
                        $interval_day = (int)$interval->format('%D');
                    @endphp
                    
                    @if ($day_group > $interval_day)
                        @break
                    
                    @elseif ($day_group !== $interval_day)
                        @php
                            $day_group = $interval_day;
                        @endphp
                        @include('channels.line')
                    @endif
                    
                    @include('channels.video')
                    
                @endforeach
            </div>
        </div>
    </div>
</div>