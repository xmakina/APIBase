<?php

namespace APIBase\Middleware;

$headers = apache_request_headers();
$this->app->add(new \APIBase\Middleware\OAuth2Auth($headers, $this->user, $this->dbh));

class OAuth2Auth extends \Slim\Middleware {

    protected $headers = array();
    protected $user;
    protected $mysql;

    public function __construct($headers, $user, $mysql) {
        $this->headers = $headers;
        $this->user = $user;
        $this->mysql = $mysql;
    }

    public function call() {
        $this->user->isLoggedIn = false;

        if (isset($this->headers['Authorization'])) {
            $this->findUser($this->headers['Authorization']);
        }

        // this line is required for the userlication to proceed
        $this->next->call();
    }

    private function findUser($token) {
        // set user details
        $stmt = $this->mysql->prepare('SELECT id, name, email FROM Users WHERE access_token = ?');
                
        $stmt->bind_param('s', $token);

        $stmt->execute();

        $result = $stmt->get_result();
        $row = mysqli_fetch_object($result);
        
        if ($row != null) {
            $this->user->isLoggedIn = true;
            $this->user->Id = $row->id;
            $this->user->Name = $row->name;
            $this->user->Email = $row->email;
        }
    }

}
