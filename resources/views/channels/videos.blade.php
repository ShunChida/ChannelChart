<?php

$now = new \DateTime();
// 取得した動画のサムネイル表示
foreach ($videos as $video) {
    echo '<img src="' . $video['snippet']['thumbnails']['medium']['url']. '" />';
    $video_published_at = new \DateTime($video['snippet']['publishedAt']);
    $interval = $now->diff($video_published_at);
    echo '<br/>';
    echo  $interval->format('%D').'日前 ';
    echo $video['snippet']['title'].'<br/>';
    echo $video['snippet']['channelTitle'].'<br/>';
    echo $video_published_at->format('H:i').'<br/>';
} 
        
?>