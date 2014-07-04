<?php

namespace APIBase;

class Links {

    private $links;

    public function __construct() {
        $this->links = array();
    }

    function Add($href, $rel, $method) {
        $link = new Link();
        $link->Href = $href;
        $link->Rel = $rel;
        $link->Method = $method;
        array_push($this->links, $link);
    }

    function Get() {
        return $this->links;
    }

}

class Link {

    public $Href;
    public $Rel;
    public $Method;

}
