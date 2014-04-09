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
                $sessionID = createSessionID($username);
                $_SESSION['username'] = $username;
                $_SESSION['sessionID'] = $sessionID;
                sqlAddSessionID($username, $sessionID, date("Y-m-d H:i:s"));
                redirect("/");
            } else
                redirect("/login");
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