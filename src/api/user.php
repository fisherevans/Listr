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
        
        function update() {
            global $json;
            $username = getJson("username");
            $password = getRawJson("password");
            $passwordNew = getRawJson("passwordNew");
            $email = getRawJson("email");
            $first_name = getJson("first_name");
            $last_name = getJson("last_name");
            
            requireValidPassword($passwordNew);
            requireValidEmail($email);
            requireValidName($first_name);
            requireValidName($last_name);
            
            $okMessage = "User updated.";
            
            $user = sqlGetUser($username);
            
            if(!password_verify($password, $user['password_hash']))
                response(403, "Current password is invalid.");
            
            if($email != $user['email']) {
                $better_token = md5(rand());
                $rem = strlen($better_token)-4;
                $code = strtoupper(substr($better_token, 0, -$rem));
                
                $codeUrl = "http://listr.fisherevans.com/validateEmail/$username/$code";
                
                $message = "<h1>Listr Email Change</h1>";
                $message .= "<p>Hello again, $first_name! Please use the link below to update your email.</p>";
                $message .= "<a href='$codeUrl'>$codeUrl</a>";
                $message .= "<p>Have a nice day!</p>";
                if(sendEmail($email, $first_name." ".$last_name, 'Listr Email Change', $message, $message) != 1) {
                    response(501, "Failed to send email. User not updated.");
                }
                sqlChangeEmail($username, $email, $code);
                $okMessage .= " Check your email to confirm new email.";
            }
            
            sqlUpdateUser($username, password_hash($passwordNew, PASSWORD_DEFAULT), $first_name, $last_name);
            
            okResponse($okMessage);
        }
        
        function updateNoPassword() {
            global $json;
            $username = getJson("username");
            $password = getRawJson("password");
            $email = getRawJson("email");
            $first_name = getJson("first_name");
            $last_name = getJson("last_name");
            
            requireValidEmail($email);
            requireValidName($first_name);
            requireValidName($last_name);
            
            $okMessage = "User updated.";
            
            $user = sqlGetUser($username);
            
            if(!password_verify($password, $user['password_hash']))
                response(403, "Current password is invalid.");
            
            if($email != $user['email']) {
                $better_token = md5(rand());
                $rem = strlen($better_token)-4;
                $code = strtoupper(substr($better_token, 0, -$rem));
                
                $codeUrl = "http://listr.fisherevans.com/validateEmail/$username/$code";
                
                $message = "<h1>Listr Email Change</h1>";
                $message .= "<p>Hello again, $first_name! Please use the link below to update your email.</p>";
                $message .= "<a href='$codeUrl'>$codeUrl</a>";
                $message .= "<p>Have a nice day!</p>";
                if(sendEmail($email, $first_name." ".$last_name, 'Listr Email Change', $message, $message) != 1) {
                    response(501, "Failed to send email. User not updated.");
                }
                sqlChangeEmail($username, $email, $code);
                $okMessage .= " Check your email to confirm new email.";
            }
            
            sqlUpdateUserNoPassword($username, $first_name, $last_name);
            
            okResponse($okMessage);
        }
        
        function validateEmail() {
            $username = getJson("username");
            $code = getJson("code");
            if(validateEmail($username, $code)) {
                okResponse("Email varified.");
            } else response(400, "Invalid code or user does not exist.");
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
            $friended = getJson("friend");
            if(sqlIsValidUser($friended) && $friended != $username) {
                sqlFriend($friended, $username);
                okGetResponse(sqlGetFriend($username, $friended));
            } else response(400, "Invalid User.");
        }
        
        function friends() {
            global $username;
            return okGetResponse(sqlGetFriends($username));
        }
        
        function confirmFriend() {
            global $username;
            $friend = getJson("friend");
            if(sqlValidFriendRequest($username, $friend)) {
                sqlConfrimFriend($username, $friend);
                addFriendedNotification($friend);
                okGetResponse(sqlGetFriend($username, $friend));
            } else response(400, "Invalid friend request.");
        }
        
        function unfriend() {
            global $username;
            $friend = getJson("friend");
            if(sqlValidFriend($username, $friend)) {
                $friendObj = sqlGetFriend($username, $friend);
                sqlUnFriend($friend, $username);
                sqlUnlinkShares($friend, $username);
                sqlUnlinkShares($username, $friend);
                addUnfriendedNotification($friendObj);
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
    
    function doValidateEmail($username, $code) {
        if(sqlValidEmailVarification($username, $code)) {
            sqlVarifyEmail($username);
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