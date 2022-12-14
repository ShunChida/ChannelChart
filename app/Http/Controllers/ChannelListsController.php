<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\ChannelList;
use App\Channel;

class ChannelListsController extends Controller
{
    protected $data = [];
    protected $user;
    protected $token;
    protected $client;
    
    public function index()
    {
        // ログイン前トップページ
        if (! \Auth::check()) {
            return view('welcome', $this->data);
        }
        
        // ログイン後
        $this->set_parameters();
        
        if (null !== $this->token) {
            $this->client->setAccessToken($this->token['access_token']);
            
            $this->refresh();
            $this->get_content();
            // ログイン後トップページ
            return view('welcome', $this->data);
        }
        
        $this->auth_code();
        echo ' エラー：認証に失敗';
    }
    
    public function show($id)
    {
        // リスト内のチャンネル取得
        $channel_list = ChannelList::findOrFail($id);
        
        $channels = $channel_list->channels()->get();
        
        $this->set_parameters();
        
        if (null !== $this->token) {
            $this->client->setAccessToken($this->token['access_token']);
            
            $this->refresh();
            $this->get_content($channels);
            // ログイン後トップページ
            $this->data['channel_list'] = $channel_list;
            return view('welcome', $this->data);
        }
        
        $this->auth_code();
        echo ' エラー：認証に失敗';
    }
    
    public function set_parameters()
    {
        $this->user = \Auth::user();
        $this->token = $this->user->token;
        
        require_once __DIR__.'/../../../vendor/autoload.php';
        
        $client_id = env('CLIENT_ID');
        $client_secret = env('CLIENT_SECRET');
        
        $this->client = new \Google_Client();
        $this->client->setAuthConfig([
            'client_id' => $client_id,
            'client_secret' => $client_secret,
        ]);
        $this->client->addScope('https://www.googleapis.com/auth/youtube');
        $this->client->setRedirectUri('https://' . $_SERVER['HTTP_HOST'] . '/');
        $this->client->setAccessType('offline');
        // リフレッシュトークンが無い場合のみ強制的にOAuth認証画面に遷移
        if (! isset($this->token['refresh_token_exists'])) {
            $this->client->setApprovalPrompt('force');
        }
        
        return true;
    }
    
    
    public function refresh()
    {
        // 期限切れの場合、リフレッシュトークンからアクセストークンを更新し
        // データベースも更新する
        if ($this->client->isAccessTokenExpired()) {
            $old_access_token = $this->client->getAccessToken();
            $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            $access_token = $this->client->getAccessToken();
            $access_token['refresh_token'] = $old_access_token['refresh_token'];
            
            $this->user->token()->update([
                'access_token' => $access_token,
            ]);
            return redirect('/')->send();
        }
    }
    
    
    public function get_content($channels = null)
    {
        if (null == $this->user->channels()->get()->first()) {
            // APIよりデータ取得
            $this->set_content();
        
        } elseif ($this->minutes_taken_after_update() >= 720) {
            // 前回から720分（半日）以上経っていればAPI使用
            $this->set_content();
        }
        
        if (null == $channels) {
            // トップページの場合
            $channels = $this->user->channels()->get();
        }
        
        $videos = null;
        
        foreach ($channels as $channel) {
            $videos_of_channel = $channel->videos()->get();
            foreach ($videos_of_channel as $video_of_channel) {
                $videos[] = $video_of_channel;
            }
        }
        
        $videos = $this->sort_by_publishedAt(SORT_DESC, $videos);
        
        $this->data = [
            'channels' => $channels,
            'videos' => $videos,
            'lists' => $this->user->channel_lists()->get(),
            'channel_list' => null,
        ];
        
        return true;
    }
    
    
    public function set_content()
    {
        $youtube = new \Google_Service_YouTube($this->client);
        
        $this->set_channels($youtube);
        $this->set_videos($youtube);
        
        return true;
    }
    
    
    public function minutes_taken_after_update() // APIを使用してから経った分数を取得
    {
        $now = strtotime(date("Y/m/d H:i:s"));
        $updated_at = strtotime($this->user->channels()->get()->first()['updated_at']);
        $diff = $now - $updated_at;
        $diff_m = $diff / 60;
        return $diff_m;
    }
    
    
    public function auth_code()
    {
        // 認証コードがあるか
        if (! isset($_GET['code'])) {
            $auth_url = $this->client->createAuthUrl();
            return \Redirect::to($auth_url)->send();
        }
        
        // アクセストークンとリフレッシュトークンを取得、データベースに保存
        $this->client->authenticate($_GET['code']);
        $access_token = $this->client->getAccessToken();
    
        if (null == $this->client->getRefreshToken()) {
            $refresh_token_exists = 0;
        } else {
            $refresh_token_exists = 1;
        }
        
        $this->user->token()->create([
            'user_id' => $this->user->id,
            'access_token' => $access_token,
            'refresh_token_exists' => $refresh_token_exists,
        ]);
        return redirect('/')->send();
    }
    
    
    public function set_channels($youtube)
    {
        //一度削除
        //$this->user->channels()->delete();
        
        // 登録チャンネルの取得
        try {
            $subsResponse = $youtube->subscriptions->listSubscriptions('snippet', array(
                'mine' =>'true',   
                'maxResults' => 50,
            ));
        } catch (Google_Service_Exception $e) {
            $htmlBody = sprintf('<p>A service error occurred: <code>%s</code></p>',
            htmlspecialchars($e->getMessage()));
        } catch (Google_Exception $e) {
            $htmlBody = sprintf('<p>An client error occurred: <code>%s</code></p>',
            htmlspecialchars($e->getMessage()));
        }
        
        // DBに保存しているチャンネルのIDを取得
        $old_channel_ids_from_youtube = [];
        foreach ($this->user->channels()->get() as $old_channel) {
            $old_channel_ids_from_youtube[] = $old_channel['channel_id_from_youtube'];
        }
        
        $new_channel_ids_from_youtube = $this->update_or_create_channels($subsResponse);
        
        $this->delete_channels($old_channel_ids_from_youtube, $new_channel_ids_from_youtube);
        
        return true;
    }
    
    
    public function update_or_create_channels($subsResponse)
    {
        // 登録チャンネルに変更があれば追加、既にあれば更新
        $new_channel_ids_from_youtube = [];
        
        foreach ($subsResponse['items'] as $subsResult) {
            $channel_id_from_youtube = $subsResult['snippet']['resourceId']['channelId'];
            
            $this->user->channels()->updateOrCreate(
                ['channel_id_from_youtube' => $channel_id_from_youtube],
                ['user_id' => $this->user->id,
                'channel' => $subsResult,
                'channel_id_from_youtube' => $channel_id_from_youtube,]
            );
            
            $new_channel_ids_from_youtube[] = $channel_id_from_youtube;
        }
        
        return $new_channel_ids_from_youtube;
    }
    
    
    public function delete_channels($old_channel_ids_from_youtube, $new_channel_ids_from_youtube)
    {
        // 登録解除されているチャンネルを削除
        $diff = array_diff($old_channel_ids_from_youtube, $new_channel_ids_from_youtube);
        
        foreach ($diff as $key => $delete_channel) {
            $channel = $this->user->channels()->where('channel_id_from_youtube', $delete_channel)->firstOrFail();
            $channel->delete();
        }
        
        return true;
    }
    
    
    public function set_videos($youtube)
    {
        // 登録チャンネルの動画取得
        
        $channels = $this->user->channels()->get();
        foreach ($channels as $channel) {
            $channel->videos()->delete();
        }
        
        foreach ($channels as $channel) {
            $params['channelId'] = $channel['channel']['snippet']['resourceId']['channelId'];
            $params['type'] = 'video';
            $params['maxResults'] = 10;
            $params['order'] = 'date'; 
            
            try {
                $searchResponse = $youtube->search->listSearch('snippet', $params);
            } catch (Google_Service_Exception $e) {
                $htmlBody = sprintf('<p>A service error occurred: <code>%s</code></p>',
                htmlspecialchars($e->getMessage()));
            } catch (Google_Exception $e) {
                $htmlBody = sprintf('<p>An client error occurred: <code>%s</code></p>',
                htmlspecialchars($e->getMessage()));
            }
            
            foreach ($searchResponse['items'] as $search_result) {
                
                $channel->videos()->create([
                    'channel_id' => $channel->id,
                    'video' => $search_result,
                ]);
            }
        }
        
        return true;
    }
    
    
    public function sort_by_publishedAt($sort_order, $array) //投稿時間順に並べる
    {
        if (null !== $array) {
            foreach ($array as $key => $value) {
                $standard_key_array[$key] = $value['video']['snippet']['publishedAt'];
            }
        
            array_multisort($standard_key_array, $sort_order, $array);
        
            return $array;
        }
    }
}
