<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TokensController extends Controller
{
    protected $data = [];
    protected $user;
    protected $token;
    protected $client;
    
    public function index()
    {
        // ログイン前トップページ
        //if (! \Auth::check()) {
            return view('welcome', $this->data);
        /*}
        
        // ログイン後
        $this->set_parameters();
        
        if (isset($this->token['access_token'])) {
            $this->client->setAccessToken($this->token['access_token']);
            
            $this->refresh();
            $this->api();
            // ログイン後トップページ
            return view('welcome', $this->data);
        }
        
        $this->auth_code();
        echo ' エラー：認証に失敗';*/
    }
    
    
    public function set_parameters()
    {
        $this->user = \Auth::user();
        $this->token = $this->user->token;
        
        require_once __DIR__.'/../../../vendor/autoload.php';
        
        $this->client = new \Google_Client();
        $this->client->setAuthConfigFile(__DIR__.'/../../../client_secret_46891901420-ub4dbh00et1mpbsukkiptrrg6eoti8qr.apps.googleusercontent.com.json');
        $this->client->addScope('https://www.googleapis.com/auth/youtube');
        $this->client->setRedirectUri('https://' . $_SERVER['HTTP_HOST'] . '/');
        $this->client->setAccessType('offline');
        // リフレッシュトークンが無い場合のみ強制的にOAuth認証画面に遷移
        if (! isset($this->token['refresh_token_exists'])) {
            $this->client->setApprovalPrompt('force');
        }
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
    
    
    public function api()
    {
        // APIよりデータ取得
        $youtube = new \Google_Service_YouTube($this->client);
        
        $channels = $this->get_channels($youtube);
        $videos = $this->get_videos($youtube, $channels);
        
        $this->data = [
            'youtube' => $youtube,
            'channels' => $channels,
            'videos' => $videos,
        ];
        
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
    
    
    public function get_channels($youtube)
    {
        // 登録チャンネルの取得
        try {
            $subsResponse = $youtube->subscriptions->listSubscriptions('snippet', array(
                'mine' =>'true',   
            ));
        } catch (Google_Service_Exception $e) {
            $htmlBody = sprintf('<p>A service error occurred: <code>%s</code></p>',
            htmlspecialchars($e->getMessage()));
        } catch (Google_Exception $e) {
            $htmlBody = sprintf('<p>An client error occurred: <code>%s</code></p>',
            htmlspecialchars($e->getMessage()));
        }
        foreach ($subsResponse['items'] as $subsResult) {
            $channels[] = $subsResult;
        }
        return $channels;
    }
    
    
    public function get_videos($youtube, $channels)
    {
        // 登録チャンネルの動画取得
        foreach ($channels as $channel) {
            $params['channelId'] = $channel['snippet']['resourceId']['channelId'];
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
                $videos[] = $search_result;
            }
        }
        
        $videos = $this->sort_by_publishedAt(SORT_DESC, $videos);
        
        return $videos;
    }
    
    
    public function sort_by_publishedAt($sort_order, $array) //投稿時間順に並べる
    {
        foreach ($array as $key => $value) {
            $standard_key_array[$key] = $value['snippet']['publishedAt'];
        }
    
        array_multisort($standard_key_array, $sort_order, $array);
    
        return $array;
    }
}
