<?php
    class ListFunctions {
        function add() {
            global $username;
            $name = getJson("name");
            $description = getJson("description");
            requireValidName($name);
            $list_id = sqlAddList($name, $description, $username);
            okGetResponse(sqlGetList($list_id));
        }
        
        function edit() {
            global $username;
            $list_id = getJson("list_id");
            $name = getJson("name");
            $description = getJson("description");
            requirePermission(isListOwner($list_id));
            requireValidName($name);
            sqlUpdateList($name, $description, $list_id, $username);
            userUpdateList($list_id);
            okGetResponse(sqlGetUserList($username, $list_id));
        }
        
        function archive() {
            global $username;
            $list_id = getJson("list_id");
            requirePermission(isListOwner($list_id));
            sqlArchiveStateList(true, $list_id);
            userUpdateList($list_id);
            okGetResponse(sqlGetUserList($username, $list_id));
        }
        
        function restore() {
            global $username;
            $list_id = getJson("list_id");
            requirePermission(isListOwner($list_id));
            sqlArchiveStateList(false, $list_id);
            userUpdateList($list_id);
            okGetResponse(sqlGetUserList($username, $list_id));
        }
        
        function favorite() {
            global $username;
            $list_id = getJson("list_id");
            requirePermission(canUseList($list_id));
            sqlFavoriteList($list_id, $username);
            okGetResponse(sqlGetUserList($username, $list_id));
        }
        
        function unfavorite() {
            global $username;
            $list_id = getJson("list_id");
            requirePermission(canUseList($list_id));
            sqlUnFavoriteList($list_id, $username);
            okGetResponse(sqlGetUserList($username, $list_id));
        }
    
        function remove() {
            $list_id = getJson("list_id");
            requirePermission(isListOwner($list_id));
            sqlRemoveList($list_id);
            okResponse("List removed.");
        }
        
        function give() {
            global $username;
            $list_id = getJson("list_id");
            $newUser = getJson("username");
            requirePermission(isListOwner($list_id));
            requireValidUsername($newUser);
            sqlGiveList($list_id, $newUser, $owner);
            userUpdateList($list_id);
            okGetResponse(sqlGetUserList($username, $list_id));
        }
        
        function share() {
            $list_id = getJson("list_id");
            $shared = getJson("username");
            requirePermission(isListOwner($list_id));
            requireValidUsername($newUser);
            sqlShareList($shared, $list_id);
            okResponse("List shared.");
        }
    
        function unshare() {
            $list_id = getJson("list_id");
            $unshared = getJson("username");
            requirePermission(isListOwner($list_id));
            requireValidUsername($newUser);
            sqlUnshareList($unshared, $list_id);
            okResponse("List unshared.");
        }
        
        function get() {
            global $username;
            $list_id = getJson("list_id");
            requirePermission(isListOwner($list_id));
            $list = sqlGetUserList($username, $list_id);
            okGetResponse($list);
        }
        
        function getAll() {
            global $username;
            $lists = sqlGetLists($username);
            okGetResponse($lists);
        }
        
        function getAllArchived() {
            global $username;
            $lists = sqlGetArchivedStateLists($username, true);
            okGetResponse($lists);
        }
        
        function getAllUnArchived() {
            global $username;
            $lists = sqlGetArchivedStateLists($username, false);
            okGetResponse($lists);
        }
        
        function items() {
            $path = func_get_args();
            $action = array_shift($path);
            callMethod('ItemFunctions', $action, $path);
        }
    }
    
    // ######### GENERIC FUNCTIONS ##########
    
    function userUpdateList($list_id) {
        global $username;
        sqlUserUpdateList(date("Y-m-d H:i:s"), $username, $list_id);
    }
    
    function canUseList($id) {
        return isListSharee($id) || isListOwner($id);
    }
    
    function isListOwner($id) {
        global $username;
        return sqlGetList($id)['owner'] == $username;
    }
    
    function isListSharee($id) {
        global $username;
        return sqlIsListSharedWith($id, $username);
    }
?>