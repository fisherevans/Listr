<?php
    class UserFunctions {
        function login() {
            $username = getJson("username");
            $password = getJson("password");
            if(isValidCredentials($username, $password)) {
                if(sqlIsValidated($username)) {
                    $sessionID = createSessionID($username);
                    $_SESSION['username'] = $username;
                    $_SESSION['sessionID'] = $sessionID;
                    sqlAddSessionID($username, $sessionID, date("Y-m-d H:i:s"));
                    okResponse("Logged in.");
                } else response(403, "Please varify your email.");
            } else response(403, "Invalid credentials.");
        }
        
        function ping() {
            if(isset($_SESSION['username']) && isset($_SESSION['sessionID'])
                    && sqlIsValidSessionID($_SESSION['username'], $_SESSION['sessionID'])) {
                sqlUpdateSessionID(date("Y-m-d H:i:s"), $_SESSION['username'], $_SESSION['sessionID']);
                okResponse("Pong.");
            } else response(400, "You are not currently logged in.");
        }
    
        function logout() {
            if(isset($_SESSION['username']) && isset($_SESSION['sessionID'])) {
                sqlRemoveSessionID($_SESSION['username'], $_SESSION['sessionID']);
                session_unset();
                session_destroy();
                okResponse("Logged out.");
            } else response(400, "You are not currently logged in.");
        }
        
        function register() {
            global $json;
            $username = getJson("username");
            $password = getRawJson("password");
            $email = getRawJson("email");
            $first_name = getJson("first_name");
            $last_name = getJson("last_name");
            
            requireAvailableUsername($username);
            requireValidPassword($password);
            requireValidEmail($email);
            requireValidName($first_name);
            requireValidName($last_name);
            
            $better_token = md5(rand());
            $rem = strlen($better_token)-4;
            $code = strtoupper(substr($better_token, 0, -$rem));
            
            $codeUrl = "http://listr.fisherevans.com/validate/$username/$code";
            
            $message = "<h1>Listr Registration</h1>";
            $message .= "<p>Welcome $first_name! Please use the below code to continue with registration.</p>";
            $message .= "<h4>$code</h4>";
            $message .= "<p>Alternatively you can use the following link.</p>";
            $message .= "<a href='$codeUrl'>$codeUrl</a>";
            $message .= "<p>Have a nice day!</p>";
            if(sendEmail($email, $first_name." ".$last_name, 'Listr Registration', $message, $message) != 1) {
                response(501, "Failed to send email. User not resgister");
            }
            
            sqlRegister($username, password_hash($password, PASSWORD_DEFAULT), $email, $first_name, $last_name, $code);
            
            okResponse("User registered, email sent.");
        }
        
        function validate() {
            $username = getJson("username");
            $code = getJson("code");
            if(validateUser($username, $code)) {
                okResponse("Email varified.");
            } else response(400, "Invalid code or user does not exist.");
        }
        
        function friend() {
            global $username;
            $friended = getJson("friended");
            if(sqlIsValidUser($friended) && $friended != $username) {
                sqlFriend($friended, $username);
                okResponse("Friend request sent.");
            } else response(400, "Invalid User.");
        }
        
        function friends() {
            global $username;
            return okGetResponse(sqlGetFriends($username));
        }
        
        function confirmFriend() {
            global $username;
            $friender = getJson("friender");
            if(sqlValidConfrimFriend($username, $friender)) {
                sqlConfrimFriend($username, $friender);
                okResponse("Friend request accepted.");
            } else response(400, "Invalid friend request.");
        }
        
        function unfriend() {
            global $username;
            $friend = getJson("friend");
            if(sqlValidFriend($friend)) {
                sqlUnFriend($friend, $username);
                sqlUnlinkShares($friend, $username);
                sqlUnlinkShares($username, $friend);
                okResponse("Friend removed.");
            } else response(400, "Invalid Friend.");
        }
        
        function self() {
            global $username;
            okGetResponse(sqlGetUserSelf($username));
        }
    }
    
    function validateUser($username, $code) {
        if(sqlValidVarification($username, $code)) {
            sqlVarifyUser($username);
            return true;
        } return false;
    }
    
    function isValidCredentials($username, $password) {
        if(sqlIsValidUser($username)) {
            $user = sqlGetUser($username);
            return password_verify($password, $user['password_hash']);
        } else return false;
    }
    
    function createSessionId($username) {   
        $data = $username
               .$_SERVER['REQUEST_TIME']
               .$_SERVER['HTTP_USER_AGENT']
               .$_SERVER['REMOTE_ADDR']
               .$_SERVER['REMOTE_PORT'];
        $hash = strtoupper(hash('ripemd128', md5($data)));
        return $hash;
    }
?>