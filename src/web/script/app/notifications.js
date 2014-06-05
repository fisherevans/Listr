function notAcceptAction(event) {
    var list_id = $(event.target).closest(".page-list-row").data("id");
    callAPI("lists/acceptShare", { "list_id":list_id, "username":user.username },
        function(list) {
            setList(list);
            removeNotification("#notification-share-row-" + list.id);
            addListDisplay(list);
            sortNotifications();
            $("#notification-notification").text("List share accepted.").css("color", "#0a0")
                .fadeTo(fadeTime, 1).delay(fadeTime * 6).fadeTo(fadeTime, 0);
            countNotifications();
        },
        function(response) {
            $("#notification-notification").text(response.response).css("color", "#a00")
                .fadeTo(fadeTime, 1).delay(fadeTime * 6).fadeTo(fadeTime, 0);
        }
    );
}

function notIgnoreAction(event) {
    var doAction = window.confirm("Are you sure you want to ignore this list?");
    if(!doAction)
        return;
    var list_id = $(event.target).closest(".page-list-row").data("id");
    callAPI("lists/unshare", { "list_id":list_id, "username":user.username },
        function(response) {
            var list = lists[list_id];
            delete lists[list_id];
            removeNotification("#notification-share-row-" + list.id);
            sortNotifications();
            $("#notification-notification").text("List share ignored!").css("color", "#0a0")
                .fadeTo(fadeTime, 1).delay(fadeTime * 6).fadeTo(fadeTime, 0);
            countNotifications();
        },
        function(response) {
            $("#notification-notification").text(response.response).css("color", "#a00")
                .fadeTo(fadeTime, 1).delay(fadeTime * 6).fadeTo(fadeTime, 0);
        }
    );
}

function notConfirmAction(event) {
    var user = $(event.target).closest(".page-list-row").data("user");
    callAPI("users/confirmFriend", { "friend" : user },
        function (friend) {
            setFriend(friend);
            removeNotification("#notification-friend-row-" + friends[user].friend);
            sortNotifications();
            $("#notification-notification").text("Friend added!").css("color", "#0a0")
                .fadeTo(fadeTime, 1).delay(fadeTime * 6).fadeTo(fadeTime, 0);
            countNotifications();
        },
        function (response) {
            $("#notification-notification").text(response.response).css("color", "#a00")
                .fadeTo(fadeTime, 1).delay(fadeTime * 6).fadeTo(fadeTime, 0);
        }
    );
}

function notRemoveAction(event) {
    var user = $(event.target).closest(".page-list-row").data("user");
    var doAction = window.confirm("Are you sure you want to ignore " + user + "'s friend request?");
    if(!doAction)
        return;
    callAPI("users/unfriend", { "friend" : user },
        function (response) {
            removeNotification("#notification-friend-row-" + friends[user].friend);
            delete friends[user];
            sortNotifications();
            $("#notification-notification").text("Friend request denied.").css("color", "#0a0")
                .fadeTo(fadeTime, 1).delay(fadeTime * 6).fadeTo(fadeTime, 0);
            countNotifications();
        },
        function (response) {
            $("#notification-notification").text(response.response).css("color", "#a00")
                .fadeTo(fadeTime, 1).delay(fadeTime * 6).fadeTo(fadeTime, 0);
        }
    );
}

function notOkayAction(event) {
    var id = $(event.target).closest(".page-list-row").data("id");
    callAPI("notifications/clear", { "id" : id },
        function (response) {
            removeNotification("#notification-misc-row-" + id);
            delete notifications[id];
            sortNotifications();
            $("#notification-notification").text("Sounds good!").css("color", "#0a0")
                .fadeTo(fadeTime, 1).delay(fadeTime * 6).fadeTo(fadeTime, 0);
            countNotifications();
        },
        function (response) {
            $("#notification-notification").text(response.response).css("color", "#a00")
                .fadeTo(fadeTime, 1).delay(fadeTime * 6).fadeTo(fadeTime, 0);
        }
    );
}

// METHODS

function countNotifications() {
    var count = 0;
    forEachAssoc(friends, function(friend) {
        if(friend.state == PENDING)
            count++;
    });
    forEachAssoc(lists, function(list) {
        if(list.owner != user.username && list.share_status != 1)
            count++;
    });
    forEachAssoc(notifications, function(notification) {
        count++;
    });
    $("#notifications-button").text(count);
    if(count > 0)
        $("#notifications-button").addClass("new");
    else
        $("#notifications-button").removeClass("new");
}

function gotoNotifications() {
    countNotifications();
    selectList(NOLIST);
    nextPage("/notifications", "Notifications");
    showNotifications();
}

function showNotifications() {
    $(".page").removeClass("visible");
    $("#notifications").delay(fadeTime).queue(function() {
        $(".page").addClass("no-display");
        $(this).removeClass("no-display");
        $("#notifications .page-list").html("");
        forEachAssoc(lists, addShareNotification);
        forEachAssoc(friends, addFriendNotification);
        forEachAssoc(notifications, addNotification);
        $(this).dequeue();
    }).delay(0).queue(function() {
        $(this).addClass("visible");
        sortNotifications();
        $(this).dequeue();
    });
}

function addShareNotification(list) {
    if(list.owner != user.username && list.share_status != 1) {
        $("#notifications-shared-lists-list").append(getShareNotificationRowHTML(list));
        $("#notification-share-row-" + list.id).addClass("show");
    }
}

function addFriendNotification(friend) {
    if(friend.state == PENDING) {
        $("#notifications-friends-list").append(getFriendNotificationRowHTML(friend));
        $("#notification-friend-row-" + friend.friend).addClass("show");
    }
}

function addNotification(notification) {
    $("#notifications-misc-list").append(getMiscNotificationRowHTML(notification));
    $("#notification-misc-row-" + notification.id).addClass("show");
}

function removeNotification(id) {
    $("html").queue(function() {
        $(id).removeClass("show");
        $(this).dequeue();
    }).delay(fadeTime).queue(function() {
        $(id).remove();
        $(this).dequeue();
    });
}

function sortNotifications() {
    $("html").delay(10).queue(function() {
        sortUsingNestedText($("#notifications-shared-lists-list"), ".page-list-row", ".page-list-row-label");
        sortUsingNestedText($("#notifications-friends-list"), ".page-list-row", ".page-list-row-label");
        sortUsingNestedText($("#notifications-misc-list"), ".page-list-row", ".page-list-row-label");

        var count = 0;

        if($("#notifications-shared-lists-list .page-list-row.show").length == 0)
            $("#notifications-shared-lists-label").addClass("hide");
        else {
            $("#notifications-shared-lists-label").removeClass("hide");
            count++;
        }

        if($("#notifications-friends-list .page-list-row.show").length == 0)
            $("#notifications-friends-label").addClass("hide");
        else {
            $("#notifications-friends-label").removeClass("hide");
            count++;
        }

        if($("#notifications-misc-list .page-list-row.show").length == 0)
            $("#notifications-misc-label").addClass("hide");
        else {
            $("#notifications-misc-label").removeClass("hide");
            count++;
        }

        if(count > 0)
            $("#notifications-none-label").addClass("hide");
        else
            $("#notifications-none-label").removeClass("hide");

        $(this).dequeue();
    });
}