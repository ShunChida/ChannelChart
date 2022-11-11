<div class="container">						
<p>登録チャンネル</p>

<?php

foreach ($channels as $channel) {
    echo '<img class="icon" src="' . $channel['snippet']['thumbnails']['default']['url']. '" />';
}

?>
</div>