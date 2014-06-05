<?php
    class NotificationFunctions {
        function getAll() {
            global $username;
            okGetResponse(sqlGetNotifications($username));
        }

        function clear() {
            global $username;
            $id = getJson("id");
            requirePermission(sqlIsNotificationOwner($username, $id));
            sqlRemoveNotification($id);
            okResponse("Notification cleared.");
        }

        function clearAll() {
            global $username;
            sqlRemoveAllNotifications($username);
            okResponse("All notifications cleared.");
        }
    }
    
    /*
        0   List Renamed
        1   List Access Lost ****
        2   List Archived
        3   List Removed
        4   List Restored
        5   Unfriended
        6   Friended

        user    varchar(24)
        type    int(11)
        key     varchar(45)
        id      int(11)
        data    varchar(45)
        message varchar(255)
        date    timestamp
    */

    function addListRenamedNotification($oldList, $newList) {
        global $username;
        foreach(sqlGetSharedList($newList['id']) as $shared) {
            sendNotification($shared['user'], 0, $newList['id'], $username . " renamed " . $oldList['name'] . " to " . $newList['name']);
        }
    }

    function addListUnsharedNotification($list_id, $unshared) {
        global $username;
        $list = sqlGetList($list_id);
        $message = $username . " has unshared " . $list['name'] . " with you";
        $rec = $unshared;
        if($unshared == $username) {
            $message = $username . " has unfollowed " . $list['name'];
            $rec = $list['owner'];
        }
        sendNotification($rec, 1, $list_id, $message);
    }

    function addListArchivedNotification($list_id) {
        global $username;
        $list = sqlGetList($list_id);
        foreach(sqlGetSharedList($list['id']) as $shared) {
            sendNotification($shared['user'], 1, $list_id, $username . " has archived " . $list['name']);
        }
    }

    function addListRemovedNotification($list_id) {
        global $username;
        $list = sqlGetList($list_id);
        foreach(sqlGetSharedList($list['id']) as $shared) {
            sendNotification($shared['user'], 1, $list_id, $username . " has removed " . $list['name']);
        }
    }

    function addListRestoredNotification($list_id) {
        global $username;
        $list = sqlGetList($list_id);
        foreach(sqlGetSharedList($list['id']) as $shared) {
            sendNotification($shared['user'], 1, $list_id, $username . " has restored " . $list['name']);
        }
    }

    function addFriendedNotification($friendUsername) {
        global $username;
        $message = $username . " has accepted your friend request.";
        sendNotification($friendUsername, 6, $username, $message);
    }

    function addUnfriendedNotification($friend) {
        global $username;
        $message = "";
        if($friend['state'] == 1) { // Accepted
            $message = $username . " has unfriended you.";
        } else if($friend['state'] == 2) { // Waiting
            $message = $username . " has revoked their friend request.";
        } else if($friend['state'] == 3) { // Pending
            $message = $username . " has denied your freind request.";
        }
        sendNotification($friend['friend'], 5, $username, $message);
    }

    function sendNotification($user, $type, $data, $message) {
        if(sqlIsNotification($user, $type, $data))
                sqlRemoveNotification(sqlGetNotification($user, $type, $data)['id']);
        sqlAddNotification($user, $type, $data, $message);

    }
?>