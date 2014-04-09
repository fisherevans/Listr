<?php

    function customError($errno, $errstr) {
        echo "<textarea style='width:100%;height:100%;'>";
        echo "[$errno] $errstr\n\n";
        debug_print_backtrace();
        echo "</textarea>";
        exit;
    }
    set_error_handler("customError");
    
    function flash($name) {
        if(isset($_SESSION[$name])) {
            $flash = $_SESSION[$name];
            unset($_SESSION[$name]);
            return $flash;
        } else
            return null;
    }
    
    function msgFlash($name) {
        $msg = flash($name);
        if($msg == null || $msg == "")
            $msg = "&nbsp";
        return $msg;
    }
    
    function redirect($url) {
        header('Location: ' . $url);
        exit;
    }
    
    function getJson($id) {
        global $json;
        if(isset($json[$id])) {
            return sanitize($json[$id]);
        } else
            return "";
    }
    
    function getRawJson($id) {
        global $json;
        if(isset($json[$id])) {
            return $json[$id];
        } else
            return "";
    }
    
    function callMethod($class, $method, $path) {
        try {
            $ref = new ReflectionClass($class); 
            $method = $ref->getMethod($method); 
            $method->invokeArgs($ref->newInstance(), $path); 
        } catch(Exception $e) {
            invalidAPIResponse();
        }
    }
    
    function sendEmail($toEmail, $toName, $subject, $textBody, $htmlBody) {
        require_once 'lib/sm/swift_required.php';
        $message = Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom(array('noreply@fisherevans.com' => 'Listr (No-Reply)'))
            ->setTo(array($toEmail => $toName))
            ->setBody($textBody)
            ->addPart($htmlBody, 'text/html')
        ;
        $transporter = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
            ->setUsername("noreply@fisherevans.com")
            ->setPassword("3e4r%T%T")
        ;
        $mailer = Swift_Mailer::newInstance($transporter);
        $result = $mailer->send($message);
        return $result;
    }
    
    // 200 OK
    // 400 Bad Request
    // 401 Permissions
    // 403 Forbidden
    function response($code, $msg) {
        http_response_code($code);
        echo json_encode(array('code' => $code, 'response' => $msg));
        exit;
    }
    
    function okGetResponse($array) {
        http_response_code(200);
        echo json_encode($array);
        exit;
    }
    
    function invalidAPIResponse() {
        response(404, "Invalid API method");
    }
    
    function okIdResponse($msg, $id) {
        global $pdo;
        http_response_code(200);
        echo json_encode(array('response' => $msg, 'id' => $id));
        exit;
    }
    
    function okResponse($msg) {
        response(200, $msg);
    }
    
    function permissionResponse() {
        response(401, "You are not authorized!");
    }
    
    function invalidInputResponse() {
        response(400, "Invalid request.");
    }
    
    function requirePermission($bool) {
        if(!$bool) permissionResponse();
    }
    
    function requireValidInput($bool) {
        if(!$bool) invalidInputResponse();
    }
?>