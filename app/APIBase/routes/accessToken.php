<?php

namespace APIBase;

$accessToken = new AccessToken($this->app, $this->dbh);

class AccessToken {

    private $dbh;
    private $app;

    function __construct($app, $dbh) {
        $this->dbh = $dbh;
        $this->app = $app;

        // Setup the routes
        $app->post('/access_token', array($this, 'post'));
    }

    function post() {
        $email = filter_input(INPUT_POST, 'email');
        if ($email === NULL) {
            APIBase::Fail('email expected');
        }

        $password = filter_input(INPUT_POST, 'password');
        if ($password === NULL) {
            APIBase::Fail('password expected');
        }

        $accessToken = AccessToken::GetAccessToken($this->dbh, $email, $password);

        $response['message'] = 'login successful';
        $response['access_token'] = $accessToken;

        $links = new \APIBase\Links();
        $links->Add('/', 'home', 'GET');

        $response['links'] = $links->Get();

        echo json_encode($response);
    }

    static function CreateToken($db, $id) {
        $stmt = $db->prepare('UPDATE Users SET access_token = ? WHERE id = ?');

        $token = GUID::GetGUID();

        $stmt->bind_param('ss', $token, $id);
        $stmt->execute();

        if ($stmt->affected_rows == 0) {
            return false;
        }

        return $token;
    }

    static function GetAccessToken($db, $email, $password) {
        $stmt = $db->prepare('SELECT id, password FROM Users WHERE email = ?');

        $stmt->bind_param('s', $email);

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result == false) {
            APIBase::Fail('login failed');
        }

        $row = mysqli_fetch_object($result);
        
        if (password_verify($password, $row->password) == false) {
            APIBase::Fail('login failed');
        }

        $accessToken = AccessToken::CreateToken($db, $row->id);
        if ($accessToken == false) {
            APIBase::Fail('login failed');
        }

        return $accessToken;
    }

}
