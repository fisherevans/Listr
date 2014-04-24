<?php
    $pdo;
    
    function sqlConnect() {
        global $pdo;
        $pdo = new PDO('mysql:host=localhost;dbname=fisherev_listr;charset=utf8',
                'fisherev_listr', 'listr1234%');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
    }
    
    function invokeStmt($stmt, $params) {
        $ref    = new ReflectionClass('mysqli_stmt'); 
        $method = $ref->getMethod("bind_param"); 
        $method->invokeArgs($stmt,$params); 
    }
    
    function executeStmt($stmt) {
        if(!$stmt -> execute()) {
            response(501, "MySQL Error: " . $stmt->error);
        }
    }
    
    function sqlRun() {
        global $pdo;
        $params = func_get_args();
        $sql = array_shift($params);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    }
    
    function sqlInsert() {
        global $pdo;
        $params = func_get_args();
        $sql = array_shift($params);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $pdo->lastInsertId();
    }
    
    function sqlCount() {
        global $pdo;
        $params = func_get_args();
        $sql = array_shift($params);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }
    
    function sqlQuery() {
        global $pdo;
        $params = func_get_args();
        $sql = array_shift($params);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    function sqlUnique() {
        global $pdo;
        $params = func_get_args();
        $sql = array_shift($params);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // ############# USER ###############
    
    function sqlAddSessionID($username, $sessionID, $date) {
        sqlRun("INSERT into sessions (username, session_id, last_ping) VALUES (?, ?, ?)",
                $username, $sessionID, $date);
    }
    
    function sqlRemoveSessionID($username, $sessionID) {
        sqlRun("DELETE from sessions where username=? and session_id=?",
                $username, $sessionID);
    }
    
    function sqlIsValidSessionID($username, $sessionID) {
        return sqlCount("select * from sessions where username=? AND session_id=?", 
                $username, $sessionID) > 0;
    }
    
    function sqlUpdateSessionID($date, $username, $sessionID) {
        sqlRun("UPDATE sessions set last_ping=? where username=? AND session_id=?", 
                $date, $username, $sessionID);
    }
    
    function sqlGetUser($username) {
        return sqlUnique("SELECT * from users where username=?",
                $username);
    }
    
    function sqlGetUserSelf($username) {
        return sqlUnique("SELECT username, email, first_name, last_name, (SELECT count(*) from email_change WHERE user=users.username) as email_change from users where username=?",
                $username);
    }
    
    function sqlIsValidUser($username) {
        return sqlCount("SELECT * from users where username=? AND varified=1",
                $username) > 0;
    }
    
    function sqlIsValidated($username) {
        return sqlCount("SELECT * from users where username=? AND varified=true", 
                $username) > 0;
    }
    
    function sqlRegister($username, $hash, $email, $first_name, $last_name, $unique_code) {
        sqlRun("INSERT into users (username, password_hash, email, first_name, last_name, varification_code) VALUES (?, ?, ?, ?, ?, ?)",
                $username, $hash, $email, $first_name, $last_name, $unique_code);
    }
    
    function sqlUpdateUser($username, $hash, $first_name, $last_name) {
        sqlRun("UPDATE users SET password_hash=?, first_name=?, last_name=? WHERE username=?",
                $hash, $first_name, $last_name, $username);
    }
    
    function sqlUpdateUserNoPassword($username, $first_name, $last_name) {
        sqlRun("UPDATE users SET first_name=?, last_name=? WHERE username=?",
                $first_name, $last_name, $username);
    }
    
    function sqlChangeEmail($username, $email, $code) {
        if(sqlCount("SELECT * from email_change where user=?", $username) > 0) {
            sqlRun("UPDATE email_change SET email=?, code=? WHERE user=?",
                    $email, $code, $username);
        } else {
            sqlRun("INSERT into email_change (user, email, code) VALUES (?, ?, ?)",
                    $username, $email, $code);
        }
    }
    
    function sqlValidEmailVarification($username, $code) {
        return sqlCount("SELECT * from email_change where user=? AND code=?", 
                $username, $code) > 0;
    }
    
    function sqlVarifyEmail($username) {
        $email = sqlUnique("SELECT * from email_change WHERE user=?", $username)['email'];
        sqlRun("UPDATE users set email=? where username=?",
                $email, $username);
        sqlRun("DELETE from email_change where user=?",
                $username);
    }
    
    function sqlValidVarification($username, $code) {
        return sqlCount("SELECT * from users where username=? AND varification_code=?", 
                $username, $code) > 0;
    }
    
    function sqlVarifyUser($username) {
        sqlRun("UPDATE users set varified=true where username=?",
                $username);
    }
    
    function sqlFriend($friended, $username) {
        if(sqlCount("SELECT * from friends where friender=? AND friended=?", $username, $friended) == 0) {
            sqlRun("INSERT into friends (friended, friender) VALUES (?, ?)",
                    $friended, $username);
        }
    }
    
    function sqlConfrimFriend($friended, $friender) {
        sqlRun("UPDATE friends SET accepted=true WHERE friended=? AND friender=?",
                $friended, $friender);
    }
    
    function sqlValidConfrimFriend($friended, $friender) {
        return sqlCount("SELECT * from friends where friended=? AND friender=? AND accepted=false", 
                $friended, $friender) > 0;
    }
    
    function sqlValidFriend($user, $friend) {
        return sqlCount("SELECT * from friends where friended=? AND friender=?", 
                $user, $friend) > 0 ||
                sqlCount("SELECT * from friends where friender=? AND friended=?", 
                $user, $friend) > 0 ;
    }
    
    function sqlValidFriendRequest($user, $friend) {
        return sqlCount("SELECT * from friends where friended=? AND friender=?", 
                $user, $friend) > 0 ;
    }
                        
    function sqlGetFriends($username) {
        return sqlQuery("SELECT friends.friended AS friend, " .
                        "(friends.accepted + (friends.accepted=0)*2) AS state, " .
                        "if(accepted=0,'',users.first_name) as first_name, " .
                        "if(accepted=0,'',users.last_name) as last_name " .
                        "FROM friends " .
                        "JOIN users on(friends.friended=users.username) " .
                        "WHERE friends.friender=? " .
                        "UNION " .
                        "SELECT friends.friender AS friend, " .
                        "(friends.accepted + (friends.accepted=0)*3) AS state, " .
                        "users.first_name as first_name, " .
                        "users.last_name as last_name " .
                        "FROM friends " .
                        "JOIN users on(friends.friender=users.username) " .
                        "WHERE friends.friended=?;",
                        $username, $username);
    }
    
    function sqlGetFriend($username, $friend) {
        return sqlUnique("SELECT friends.friended AS friend, " .
                        "(friends.accepted + (friends.accepted=0)*2) AS state, " .
                        "if(accepted=0,'',users.first_name) as first_name, " .
                        "if(accepted=0,'',users.last_name) as last_name " .
                        "FROM friends " .
                        "JOIN users on(friends.friended=users.username) " .
                        "WHERE friends.friender=? AND friends.friended=? " .
                        "UNION " .
                        "SELECT friends.friender AS friend, " .
                        "(friends.accepted + (friends.accepted=0)*3) AS state, " .
                        "users.first_name as first_name, " .
                        "users.last_name as last_name " .
                        "FROM friends " .
                        "JOIN users on(friends.friender=users.username) " .
                        "WHERE friends.friended=? AND friends.friender=?;",
                        $username, $friend, $username, $friend);
    }
    
    // (accepted + ((accepted=0)*2)) as state
    
    function sqlUnFriend($friend, $username) { 
        sqlRun("DELETE from friends where friended=? and friender=?",
                $friend, $username);
        sqlRun("DELETE from friends where friender=? and friended=?",
                $friend, $username);
    }
    
    function sqlAreFriends($user1, $user2) {
        return sqlCount("SELECT friended as friend FROM friends WHERE friender=? AND friended=? AND accepted=1 " .
                        "UNION " .
                        "SELECT friender as friend FROM friends WHERE friended=? AND friender=? AND accepted=1",
                        $user1, $user2, $user1, $user2) > 0;
    }
    
    // ############# LISTS ###############
    
    function sqlAddList($name, $description, $username) {
        return sqlInsert("INSERT into lists (name, description, owner, last_updater) VALUES (?, ?, ?, ?)",
                $name, $description, $username, $username);
    }
    
    function sqlGetList($id) {
        return sqlUnique("SELECT * from lists WHERE id=?",
                $id);
    }
    
    function sqlGetUserList($username, $id) {
        return sqlUnique("SELECT lists.*, (favorites.user is not null) as favorited, if(lists.owner=?,1,shared.accepted) as share_status " .
                         "from lists LEFT OUTER JOIN shared ON(lists.id=shared.list_id AND ?=shared.user) LEFT OUTER JOIN favorites on (lists.id=favorites.list_id) " .
                         "and (?=favorites.user) WHERE id=?",
                $username, $username, $username, $id);
    }
    
    function sqlGetLists($username) {
        return sqlQuery("SELECT DISTINCT (lists.owner=?) as is_owner, lists.*, (favorites.user is not null) as favorited, if(lists.owner=?,1,shared.accepted) as share_status " .
                        "FROM   lists " .
                        "LEFT OUTER JOIN shared ON(lists.id=shared.list_id) " .
                        "LEFT OUTER JOIN favorites on (lists.id=favorites.list_id) and (?=favorites.user) " .
                        "WHERE  lists.owner=? OR shared.user=? " .
                        "ORDER BY favorited DESC, is_owner DESC, lists.name ASC",
                        $username, $username, $username, $username, $username);
    }
    
    function sqlGetArchivedStateLists($username, $state) {
        return sqlQuery("SELECT DISTINCT (lists.owner=?) as is_owner, lists.* " .
                         "FROM   lists " .
                         "LEFT OUTER JOIN shared ON(lists.id=shared.list_id) " .
                         "WHERE  (lists.owner=? OR shared.user=?) AND lists.archived=? " .
                         "ORDER BY is_owner DESC, lists.name ASC",
                $username, $username, $username, $state);
    }
    
    function sqlIsListSharedWith($id, $username) {
        return sqlCount("select * from shared where list_id=? AND user=?",
                $id, $username) > 0;
    }
    
    function sqlIsValidList($id) {
        return sqlCount("id from lists where id=?",
                $id) > 0;
    }
    
    function sqlListSharedWith($id) {
        return sqlQuery("select * from shared where list_id=?",
                $id);
    }
    
    function sqlShareList($shared, $id) {
        sqlRun("INSERT INTO shared (user, list_id) VALUE (?, ?)",
                $shared, $id);
    }
    
    function sqlUnshareList($shared, $id) {
        sqlRun("DELETE FROM shared WHERE user=? AND list_id=?",
                $shared, $id);
    }
    
    function sqlAcceptShareList($shared, $id) {
        sqlRun("UPDATE shared SET accepted=1 WHERE list_id=? AND user=?",
                $id, $shared);
    }
    
    function sqlUpdateList($name, $description, $id, $username) {
        sqlRun("UPDATE lists SET name=?, description=? WHERE id=? AND owner=?",
                $name, $description, $id, $username);
    }
    
    function sqlRemoveList($id) {
        sqlRun("DELETE from shared where list_id=?",
                $id);
        sqlRun("DELETE from favorites where list_id=?",
                $id);
        sqlRun("DELETE from items where list_id=?",
                $id);
        sqlRun("DELETE from lists where id=?",
                $id);
    }
    
    function sqlGiveList($id, $username, $owner) {
        sqlRun("UPDATE lists SET owner=? WHERE id=? AND owner=?",
                $username, $id, $owner);
        sqlRun("INSERT INTO shared (list_id, user) VALUES (?, ?)",
                $id, $owner);
        sqlRun("DELETE FROM shared WHERE list_id=? AND user=?",
                $id, $username);
    }
    
    function sqlUnlinkShares($owner, $sharee) {
        sqlRun("DELETE shared " .
               "FROM shared " .
               "INNER JOIN lists ON(shared.list_id=lists.id) " .
               "WHERE lists.owner=? " .
               "AND shared.user=?;", 
                $owner, $sharee);
    }
    
    function sqlArchiveStateList($state, $list_id) {
        sqlRun("UPDATE lists SET archived=? WHERE id=? ",
                $state, $list_id);
    }
    
    function sqlFavoriteList($list_id, $user) {
        sqlRun("INSERT INTO favorites (list_id, user) VALUE (?, ?)",
                $list_id, $user);
    }
    
    function sqlUnFavoriteList($list_id, $user) {
        sqlRun("DELETE FROM favorites WHERE list_id=? AND user=?",
                $list_id, $user);
    }
    
    function sqlUserUpdateList($time, $username, $list_id) {
        sqlRun("UPDATE lists SET last_updated=?, last_updater=? WHERE id=?",
            $time, $username, $list_id);
    }
    
    function sqlGetSharedList($list_id) {
        return sqlQuery("select * from shared where list_id=?", $list_id);
    }
    
    function sqlIsListShared($list_id, $user) {
        return sqlCount("select * from shared where list_id=? AND user=?", $list_id, $user) > 0;
    }
    
    // ############# ITEMS ###############
    
    function sqlGetListFromItem($item_id) {
        return sqlUnique("SELECT list_id FROM items WHERE id=?",
                $item_id)['list_id'];
    }
    
    function sqlGetItem($item_id) {
        return sqlUnique("SELECT *, name as value FROM items WHERE id=?",
                $item_id);
    }
    
    function sqlRemoveItem($item_id) {
        return sqlRun("DELETE FROM items WHERE id=?",
                $item_id);
    }
    
    function sqlGetItems($list_id) {
        return sqlQuery("SELECT *, name as value FROM items WHERE list_id=?",
                $list_id);
    }
    
    function sqlGetItemsState($list_id, $state) {
        return sqlQuery("SELECT *, name as value FROM items WHERE list_id=? AND state=?",
                $list_id, $state);
    }
    
    function sqlIsValidItem($item_id) {
        return sqlCount("select id from items where id=?",
                $item_id) > 0;
    }
    
    function sqlIsValidListItem($list_id, $item_id) {
        return sqlCount("select id from items where id=? && list_id=?",
                $item_id, $list_id) > 0;
    }
    
    function sqlAddItem($list_id, $name) {
        return sqlInsert("INSERT INTO items (list_id, name) VALUES (?, ?)",
                $list_id, $name);
    }
    
    function sqlSetItemState($state, $item_id) {
        sqlRun("UPDATE items SET state=? WHERE id=? ",
                $state, $item_id);
    }
    
    function sqlIsNameTaken($list_id, $name) {
        return sqlCount("select id from items where list_id=? and name=?",
                $list_id, $name) > 0;
    }
    
    function sqlItemByName($list_id, $name) {
        return sqlUnique("select *, name as value from items where list_id=? and name=?",
                $list_id, $name);
    }

    // NOTIFICATIONS
    function sqlGetNotifications($user) {
        return sqlQuery("SELECT * FROM notifications WHERE user=?",$user);
    }

    function sqlRemoveNotification($id) {
        sqlRun("DELETE FROM notifications WHERE id=?", $id);
    }

    function sqlRemoveAllNotifications($user) {
        sqlRun("DELETE FROM notifications WHERE user=?", $user);
    }

    function sqlIsNotificationOwner($user, $id) {
        return sqlCount("SELECT id FROM notifications WHERE user=? AND id=?", $user, $id) > 0;
    }

    function sqlAddNotification($user, $type, $data, $message) {
        sqlRun("INSERT INTO notifications (user, type, data, message) VALUES (?, ?, ?, ?)", $user, $type, $data, $message);
    }

    function sqlGetNotificationById($id) {
        return sqlUnique("SELECT * FROM notifications WHERE id=?", $id);
    }

    function sqlGetNotification($user, $type, $data) {
        return sqlUnique("SELECT * FROM notifications WHERE user=? AND type=? AND data=?", $user, $type, $data);
    }

    function sqlIsNotification($user, $type, $data) {
        return sqlCount("SELECT id FROM notifications WHERE user=? AND type=? AND data=?", $user, $type, $data) > 0;
    }

    function sqlUpdateNotification($id, $message) {
        sqlRun("UPDATE notifications (SET data=?, message=?, date=now() WHERE id=?", $message, $id);
    }
?>