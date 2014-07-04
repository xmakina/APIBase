<?php

namespace APIBase;

\Slim\Slim::registerAutoloader();

class CurrentUser {

    public $Name;

}

class APIBase {

    public static function ProtectPage($user) {
        if ($user->isLoggedIn == false) {
            $response = [];
            $response['message'] = "No authorization header";

            $links = new \APIBase\Links();
            $links->Add('/access_token', 'login', 'POST');
            $links->Add('/user', 'register', 'POST');

            $response['links'] = $links->Get();

            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode($response);
            die();
        }
    }

    public static function Fail($message) {
        $response = [];
        $response['message'] = $message;

        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode($response);
        die();
    }

    public function __construct($dbHost, $dbName, $dbUser, $dbPass) {
        $this->dbHost = $dbHost;
        $this->dbName = $dbName;
        $this->dbUser = $dbUser;
        $this->dbPass = $dbPass;

        // create new Slim
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');

        $this->user = new CurrentUser();
    }

    public function enable() {
        // connect to the DB
        $this->dbConnect();

        // setup the routes
        require 'util/links.php';
        require 'util/OAuth2Auth.php';
        require 'util/PasswordHasher.php';
        require 'util/GUID.php';
        require 'routes/home.php';
        require 'routes/accessToken.php';
        require 'routes/user.php';

        // start Slim
        $this->app->run();
    }

    private function dbConnect() {
        $mysqli = mysqli_init();
        if (!$mysqli) {
            die('mysqli_init failed');
        }

        if (!$mysqli->options(MYSQLI_INIT_COMMAND, 'SET AUTOCOMMIT = 0')) {
            die('Setting MYSQLI_INIT_COMMAND failed');
        }

        if (!$mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5)) {
            die('Setting MYSQLI_OPT_CONNECT_TIMEOUT failed');
        }

        if (!$mysqli->real_connect($this->dbHost, $this->dbUser, $this->dbPass, $this->dbName)) {
            die('Connect Error (' . mysqli_connect_errno() . ') '
                    . mysqli_connect_error());
        }

        $this->dbh = $mysqli;
    }

}
