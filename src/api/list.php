<?php
    class ListFunctions {
        function add() {
            global $username;
            $name = getJson("name");
            $description = getJson("description");
            requireValidListName($name);
            $list_id = sqlAddList($name, $description, $username);
            $list = sqlGetUserList($username, $list_id);
            $list['shared'] = sqlGetSharedList($list['id']);
            okGetResponse($list);
        }
        
        function edit() {
            global $username;
            $list_id = getJson("list_id");
            $name = getJson("name");
            $description = getJson("description");
            requirePermission(isListOwner($list_id));
            requireValidListName($name);
            $oldList = sqlGetList($list_id);
            sqlUpdateList($name, $description, $list_id, $username);
            userUpdateList($list_id);
            $newList = sqlGetUserList($username, $list_id);
            if($newList['name'] != $oldList['name'])
                addListRenamedNotification($oldList, $newList);
            okGetResponse($newList);
        }
        
        function archive() {
            global $username;
            $list_id = getJson("list_id");
            requirePermission(isListOwner($list_id));
            addListArchivedNotification($list_id);
            sqlArchiveStateList(true, $list_id);
            userUpdateList($list_id);
            $list = sqlGetUserList($username, $list_id);
            $list['shared'] = sqlGetSharedList($list['id']);
            addListArchivedNotification($list_id);
            okGetResponse($list);
        }
        
        function restore() {
            global $username;
            $list_id = getJson("list_id");
            requirePermission(isListOwner($list_id));
            addListRestoredNotification($list_id);
            sqlArchiveStateList(false, $list_id);
            userUpdateList($list_id);
            $list = sqlGetUserList($username, $list_id);
            $list['shared'] = sqlGetSharedList($list['id']);
            addListRestoredNotification($list_id);
            okGetResponse($list);
        }
        
        function favorite() {
            global $username;
            $list_id = getJson("list_id");
            requirePermission(canUseList($list_id));
            sqlFavoriteList($list_id, $username);
            $list = sqlGetUserList($username, $list_id);
            $list['shared'] = sqlGetSharedList($list['id']);
            okGetResponse($list);
        }
        
        function unfavorite() {
            global $username;
            $list_id = getJson("list_id");
            requirePermission(canUseList($list_id));
            sqlUnFavoriteList($list_id, $username);
            $list = sqlGetUserList($username, $list_id);
            $list['shared'] = sqlGetSharedList($list['id']);
            okGetResponse($list);
        }
    
        function remove() {
            $list_id = getJson("list_id");
            requirePermission(isListOwner($list_id));
            addListRemovedNotification($list_id);
            sqlRemoveList($list_id);
            addListRemovedNotification($list_id);
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
            $list = sqlGetUserList($username, $list_id);
            $list['shared'] = sqlGetSharedList($list['id']);
            okGetResponse($list);
        }
        
        function share() {
            global $username;
            $list_id = getJson("list_id");
            $shared = getJson("username");
            requirePermission(isListOwner($list_id));
            requireValidUsername($shared);
            requireFriends($username, $shared);
            if(sqlIsListShared($list_id, $shared))
                response(400, "List already shared with " . $shared);
            else {
                sqlShareList($shared, $list_id);
                okResponse("List shared.");
            }
        }
        
        function acceptShare() {
            global $username;
            $list_id = getJson("list_id");
            $shared = getJson("username");
            requireValidUsername($shared);
            sqlAcceptShareList($shared, $list_id);
            $list = sqlGetUserList($shared, $list_id);
            $list['shared'] = sqlGetSharedList($list['id']);
            okGetResponse($list);
        }
    
        function unshare() {
            global $username;
            $list_id = getJson("list_id");
            $unshared = getJson("username");
            requirePermission(isListOwner($list_id) || ($unshared == $username && isListSharee($list_id)));
            requireValidUsername($unshared);
            addListUnsharedNotification($list_id, $unshared);
            sqlUnshareList($unshared, $list_id);
            addListUnsharedNotification($list_id, $unshared);
            okResponse("List unshared.");
        }
        
        function get() {
            global $username;
            $list_id = getJson("list_id");
            requirePermission(isListOwner($list_id) || isListSharee($list_id));
            $list = sqlGetUserList($username, $list_id);
            $list['shared'] = sqlGetSharedList($list['id']);
            okGetResponse($list);
        }
        
        function getAll() {
            global $username;
            $lists = sqlGetLists($username);
            for($i = 0;$i < count($lists);$i++) {
                $lists[$i]['shared'] = sqlGetSharedList($lists[$i]['id']);
            }
            okGetResponse($lists);
        }
        
        function getAllArchived() {
            global $username;
            $lists = sqlGetArchivedStateLists($username, true);
            for($i = 0;$i < count($lists);$i++) {
                $lists[$i]['shared'] = sqlGetSharedList($list['id']);
            }
            okGetResponse($lists);
        }
        
        function getAllUnArchived() {
            global $username;
            $lists = sqlGetArchivedStateLists($username, false);
            for($i = 0;$i < count($lists);$i++) {
                $lists[$i]['shared'] = sqlGetSharedList($list['id']);
            }
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