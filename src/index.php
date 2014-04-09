<?php
    // Error Checking 
    error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors', 'On');
    /**/
    
    $title = "Listr";
    $css = array("app"); 
    $content = "404";
    $json = json_decode(file_get_contents('php://input'), true);
    
    $username = "";
    $sessionID = "";
    $authorized = false;
    
    $devSuffix = "?nocache=1";
    
    require_once('mysql.php');
    require_once('functions.php');
    require_once('pages.php');
    require_once('validation.php');
    require_once('functions.php');
    
    require_once('api/api.php');
    require_once('api/user.php');
    require_once('api/list.php');
    require_once('api/item.php');
    
    require_once('router.php');
    
    sqlConnect();
    date_default_timezone_set('America/New_York');
    
    if(!session_id())
        session_start();
    if(isset($_SESSION['username']) && isset($_SESSION['sessionID'])) {
        $username = $_SESSION['username'];
        $sessionID = $_SESSION['sessionID'];
        $authorized = sqlIsValidSessionID($username, $sessionID);
    }
    
    $post = $_SERVER['REQUEST_METHOD'] === 'POST';
    $urlArray = explode('?', $_SERVER['REQUEST_URI']);
    $urlArray[0] = preg_replace("/[^0-9a-zA-Z\-\/]/", "", preg_replace("/\/$/", "", preg_replace("/^\//", "", $urlArray[0])));
    $path = explode('/', $urlArray[0]);
    
    route($path);
    
    include("base.php");
?>