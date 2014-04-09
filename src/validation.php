<?php
    function sanitize($text) {
        return trim(preg_replace("/[^ a-zA-Z0-9!\\$&\\(\\)-+\\|\\[\\]:'\",.\\/\\?]/", '', $text));
    }
    
    function requireLength($text, $field, $minLength, $maxLength) {
        $length = strlen($text);
        if($length < $minLength || $length > $maxLength) {
            response(400, "$field must be between $minLength and $maxLength character(s) long.");
        }
    }
    
    function requireValidUser($username) {
        if(!sqlIsValidUser($username))
            response(400, "$username is not a valid user.");
    }
    
    function requireValidCredentials($username, $password) {
        if(!password_verify($password, sqlGetUser($username)['password_hash']))
            response(400, "Either the user $username doesn't exist or the supplied password is incorrect.");
    }
    
    function requireAuth() {
        if(!$authorized)
            response(403, "You must be logged in to complete this action.");
    }
    
    function requireValidList($list_id) {
        if(!sqlIsValidList($list_id))
            response(400, "Invalid list ID.");
    }
    
    function requireAvailableUsername($username) {
        if(sqlIsValidUser($username))
            response(400, "Username is already taken.");
        if(!preg_match("/^[a-zA-Z0-9]+$/", $username))
            response(400, "Usernames must be alphanumeric.");
    }
    
    function requireValidPassword($password) {
        if(strlen($password) < 6)
            response(400, "Passwords must be at least 6 characters long.");
    }
    
    function requireValidEmail($email) {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
            response(400, "Please use a valid email.");
    }
    
    function requireValidName($name) {
        if(strlen($name) < 1 || strlen($name) > 45 || !preg_match("/^[a-zA-Z -\.]+$/", $name))
            response(400, "Names may only contain letters, -'s, _'s and spaces.");
    }
    
    function requireValidItemName($name) {
        if(strlen($name) < 1 || strlen($name) > 45)
            response(400, "Item names must be between 1 and 45 characters.");
    }
    
    function requireValidListItem($list_id, $item_id) {
        if(!sqlIsValidListItem($list_id, $item_id))
            response(400, "Invalid item ID.");
    }
?>