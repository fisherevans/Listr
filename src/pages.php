<?php
    function setAppContent() {
        requireAuthorization();
        global $content, $title, $css;
        $css = array("app", "app/autocomplete", "app/banner", "app/items", "app/lists", "app/page");
        $title = "App | " . $title;
        $content = "app";
    }
    
    function setLoginContent() {
        global $content, $title, $css;
        $css = array("login");
        $title = "Login | " . $title;
        $content = "login";
    }
    
    function set404Content() {
        requireAuthorization();
        global $content, $title;
        $content = "404";
        $title = "404 | " . $title;
        http_response_code(400);
    }

    function setStyleContent() {
        global $content, $title;
        $title = "Style Guide | " . $title;
        $content = "style";
    }
    
    function setAPIContent() {
        global $content, $title, $css;
        $title = "API Directory | " . $title;
        $css = array("api");
        $content = "api_guide";
    }
    
    function requireAuthorization() {
        global $authorized;
        if(!$authorized) {
            setcookie("destination", $_SERVER['REQUEST_URI'], time()+3600*24*265, "/");
            redirect("/login");
        }
    }
?>