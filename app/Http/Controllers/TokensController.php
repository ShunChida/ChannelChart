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
        if (! \Auth::check()) {
            return view('welcome', $this->data);
        }
        
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
            return redirect('/');
        }
    }
    
    
    public function api()
    {
        // APIよりデータ取得
        $youtube = new \Google_Service_YouTube($this->client);
        $this->data = [
            'youtube' => $youtube,
        ];
        
    }
    
    
    public function auth_code()
    {
        // 認可コードがあるか
        if (! isset($_GET['code'])) {
            $auth_url = $this->client->createAuthUrl();
            return redirect($auth_url);
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
        return redirect('/');
    }
    
}
