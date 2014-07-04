<?php

namespace APIBase;

$user = new User($this->app, $this->dbh, $this->user);

class User {

    private $app;
    private $dbh;
    private $user;

    function __construct($app, $dbh, $user) {
        $this->dbh = $dbh;
        $this->app = $app;
        $this->user = $user;

        // Setup the routes
        $app->post('/user', array($this, 'post'));
        $app->put('/user', array($this, 'put'));
    }

    function post() {
        if ($this->user->isLoggedIn) {
            $response['message'] = 'You are already logged in!';
        } else {
            $email = filter_input(INPUT_POST, 'email');
            if ($email === NULL) {
                APIBase::Fail('email expected');
            }

            $password = filter_input(INPUT_POST, 'password');
            if ($password === NULL) {
                APIBase::Fail('password expected');
            }

            $response = User::CreateUser($this->dbh, $email, $password);
        }

        echo json_encode($response);
    }

    function put() {
        APIBase::ProtectPage($this->user);

        $response['message'] = "Updating " . $this->user->Name;

        echo json_encode($response);
    }

    private static function CreateUser($db, $email, $password) {
        $stmt = $db->prepare('INSERT INTO Users (email, password) VALUES (?, ?)');

        $hashedPassword = PasswordHasher::HashPassword($password);

        $stmt->bind_param('ss', $email, $hashedPassword);

        $result = $stmt->execute();

        if ($result == false) {
            APIBase::Fail('Unable to create user');
        }

        $response['message'] = 'User created';

        $links = new \APIBase\Links();
        $links->Add('/access_token', 'login', 'POST');

        $response['links'] = $links->Get();

        return $response;
    }

}
