<?php
    class APIRouter {
        function lists() {
            $path = func_get_args();
            $action = array_shift($path);
            callMethod('ListFunctions', $action, $path);
        }
        
        function users() {
            $path = func_get_args();
            $action = array_shift($path);
            callMethod('UserFunctions', $action, $path);
        }
        
        function notifications() {
            $path = func_get_args();
            $action = array_shift($path);
            callMethod('NotificationFunctions', $action, $path);
        }
    }
?>