var fadeTime = 250;

var UNCHECKED = 1;
var CHECKED = 2;
var ARCHIVED = 3;

var ACCEPTED = 1;
var WAITING = 2;
var PENDING = 3;

var NOLIST = -1;

var RENAMED = 0;
var LOST_ACCESS = 1;
var REMOVED = 2;
var UNFRIENDED = 3;

var currentList = NOLIST;
var user = {}, friends = {}, lists = {}, items = {}, notifications = {};

$(document).ready(function() {
    $("#need-js").remove();
    $(window).bind('popstate', pageListener);
    
    // Listeners for ITEMS page
    $("#add-item-input").keydown(addItemAction);
    $("#add-list-input").keydown(addListAction);
    $("body").on('click', ".list-item", itemClickAction);
    $("body").on('click', ".autocomplete-suggestions.item-input div", removeAutocompletItemAction);
    
    // Listeners for LISTS pane
    $("body").on('click', ".lists-row", listClickAction);
    
    // Listeners for LIST SETTINGS page
    $("body").on('click', ".page-list-row-actions .unshare", unshareListAction);
    
    // Listeners for PROFILE page
    $("body").on('click', ".page-list-row-actions .confirmFriend", confirmFriendAction);
    $("body").on('click', ".page-list-row-actions .removeFriend", removeFriendAction);

    // Listeners for LIST MGMT page
    $("body").on('click', ".page-list-row-actions .view", mgmtViewAction);
    $("body").on('click', ".page-list-row-actions .restore", mgmtRestoreAction);
    $("body").on('click', ".page-list-row-actions .archive", mgmtArchiveAction);
    $("body").on('click', ".page-list-row-actions .delete", mgmtDeleteAction);
    $("body").on('click', ".page-list-row-actions .ignore", mgmtIgnoreAction);
    $("body").on('click', ".page-list-row-actions .acceptShare", mgmtAcceptShareAction);

    // Notifications
    $("body").on('click', ".page-list-row-actions .acceptShareNot", notAcceptAction);
    $("body").on('click', ".page-list-row-actions .ignoreNot", notIgnoreAction);
    $("body").on('click', ".page-list-row-actions .confirmFriendNot", notConfirmAction);
    $("body").on('click', ".page-list-row-actions .removeFriendNot", notRemoveAction);
    $("body").on('click', ".page-list-row-actions .okayNot", notOkayAction);
    
    setInterval(updateListTimeDisplays, 1000*60);

    $("html").delay(fadeTime).queue(function() {
        $('#loading').fadeTo(fadeTime, 1);
        $(this).dequeue();
    }).delay(fadeTime).queue(function() {
        loadUser();
        loadFriends();
        loadNotifications();
        $('#loading').fadeTo(fadeTime, 0);
        $(this).dequeue();
    }).delay(fadeTime).queue(function() {
        $('#banner').fadeTo(fadeTime, 1);
        $(this).dequeue();
    }).delay(fadeTime).queue(function() {
        $('#lists').fadeTo(fadeTime, 1);
        $(this).dequeue();
    }).delay(fadeTime).queue(function() {
        refreshLists();
        $("html").queue(function() {
            var path = window.location.pathname.substr(1).split("/")
            var action = path[0];
            var actionId = path[1];
            if(action == 'list') gotoListItems(actionId);
            else if(action == 'settings') gotoListSettings(actionId);
            else if(action == 'lists') gotoListManagement();
            else if(action == 'profile') gotoProfile();
            else gotoNotifications();
            $(this).dequeue();
        });
        $(this).dequeue();
    });
});

function hideAllPages() {
    selectList(NOLIST);
    $(".page").removeClass("visible");
    $("html").delay(fadeTime).queue(function() {
        $(".page").addClass("no-display");
        $(this).dequeue();
    });
}

function gotoListSettings(id) {
    if(lists[id] == undefined || lists[id]['owner'] != user.username || lists[id]['archived'] == 1) {
        gotoNotFound();
        return;
    }
    selectList(id);
    showListSettings();
    setSettingsURL(lists[id]);
    countNotifications();
}

function gotoListItems(id) {
    if(lists[id] == undefined) {
        gotoNotFound();
        return;
    }
    selectList(id);
    refreshItems();
    setListURL(lists[id]);
    countNotifications();
}

function gotoProfile() {
    selectList(NOLIST);
    showProfile();
    nextPage("/profile", "Profile & Friends");
    countNotifications();
}

function loadLists() {
    lists = {};
    callAPI("lists/getAll", {},
        function(response) { response.forEach(setList); countNotifications(); },
        function(response) { error("Failed to load lists!"); }
    );
}

function setList(list) {
    list.last_updated_date = new Date(list.last_updated.replace(" ", "T"));
    list.last_updated_date.setHours(list.last_updated_date.getHours()+4);
    lists[list.id] = list;
}

function loadItems() {
    items = {};
    callAPI("lists/items/getAll", {"list_id":currentList},
        function(response) { response.forEach(setItem); countNotifications(); },
        function(response) { error("Failed to load items!"); }
    );
}

function loadNotifications() {
    notifications = {}
    callAPI("notifications/getAll", {},
        function(response) { response.forEach(setNotification); },
        function(response) { error("Failed to load notifications!"); }
    );
}

function setItem(item) {
    item.value = item.name;
    items[item.id] = item;
}

function setNotification(notification) {
    notification.last_updated_date = new Date(notification.date.replace(" ", "T"));
    notification.last_updated_date.setHours(notification.last_updated_date.getHours()+4);
    notifications[notification.id] = notification;
}

function gotoNotFound() {
    document.title = "Not Found | Listr";
    $(".page").removeClass("visible");
    $("#notfound").delay(fadeTime).queue(function() {
        $(".page").addClass("no-display");
        $(this).removeClass("no-display");
        $(this).addClass("visible");
        $(this).dequeue();
    });
}

function setSettingsURL(list) {
    nextPage("/settings/" + list.id + "/" + getURLName(list.name), list.name + " (Settings)");
}

function setListURL(list) {
    nextPage("/list/" + list.id + "/" + getURLName(list.name), list.name);
}

function getURLName(text) {
    return text.replace(/[^a-zA-Z0-9 -]/g, '').replace(/ +/g, '-').toLowerCase();
}

function sortUsingNestedText(parent, childSelector, keySelector) {
    var items = parent.children(childSelector).sort(function(a, b) {
        var vA = $(keySelector, a).text().toLowerCase();
        var vB = $(keySelector, b).text().toLowerCase();
        return (vA < vB) ? -1 : (vA > vB) ? 1 : 0;
    });
    parent.append(items);
}

function nextPage(path, title) {
    document.title = title + " | Listr";
    history.pushState({listr:true}, title , path);
}

function pageListener(event) {

}