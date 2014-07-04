<?php

namespace APIBase;

$home = new Home($this->app, $this->dbh, $this->user);

class Home {

    private $dbh;
    private $app;

    function __construct($app, $dbh, $user) {
        $this->app = $app;
        $this->dbh = $dbh;
        $this->user = $user;

        // Setup the routes
        $app->get('/', array($this, 'home'));
    }

    function home() {
        $linkList = new Links;
        $response = [];

        if ($this->user->isLoggedIn) {
            $linkList->Add('/doActivity', 'StartDoingWork', 'POST');
        } else {
            $response['message'] = "authorization not recognised";

            $linkList->Add('/access_token', 'login', 'POST');
            $linkList->Add('/user', 'register', 'POST');

            http_response_code(401);
        }

        $response['links'] = $linkList->Get();
        echo json_encode($response);
    }

}
