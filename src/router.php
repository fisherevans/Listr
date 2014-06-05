<?php
function route($path) {
    global $post;
    switch (array_shift($path)) {
        case "login":
            setLoginContent();
            return;
        case "validate":
            $username = array_shift($path);
            $code = array_shift($path);
            if(validateUser($username, $code)) {
                session_unset();
                session_destroy();
                setcookie("verified", $_SERVER['REQUEST_URI'], time()+3600*24*265, "/");
                redirect("/login");
            } else
                redirect("/login");
            return;
        case "validateEmail":
            $username = array_shift($path);
            $code = array_shift($path);
            doValidateEmail($username, $code);
            redirect("/profile");
            return;
        case "style":
            setStyleContent();
            return;
        case "api":
            if ($post) {
                $action = array_shift($path);
                callMethod('APIRouter', $action, $path);
            } else
                setAPIContent();
            return;
        default:
            setAppContent();
            return;
    }
}
?>