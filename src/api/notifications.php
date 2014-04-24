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
        1   List Access Lost
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
        
    }

    function addListUnsharedNotification($list_id, $unshared) {
        
    }

    function addListArchivedNotification($list_id) {
        
    }

    function addListRemovedNotification($list_id) {
        
    }

    function addListRestoredNotification($list_id) {
        
    }

    function addFriendedNotification($friendUsername) {
        global $username;
        $message = $username. " has accepted your friend request.";
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
        if(sqlIsNotification($user, $type, $data)) {
            sqlUpdateNotification(sqlGetNotification($user, $type, $data)['id'], $message);
        } else {
            sqlAddNotification($user, $type, $data, $message);
        }

    }
?>