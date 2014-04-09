var fadeTime = 250;

var UNCHECKED = 1;
var CHECKED = 2;
var ARCHIVED = 3;

var NOLIST = -1;

var currentList = NOLIST;
var user = {}, friends = {}, lists = {}, items = {};

$(document).ready(function() {
    $("#need-js").remove();
    $(window).bind('popstate', pageListener);
    
    // Listeners for ITEMS page
    $("#add-item-input").keydown(addItemAction);
    $("#add-list-input").keydown(addListAction);
    $("body").on('click', ".list-item", itemClickAction);
    $("#edit-description").on('click', editListAction);
    $("body").on('click', ".autocomplete-suggestions.item-input div", removeAutocompletItemAction);
    
    // Listeners for LISTS pane
    $("body").on('click', ".lists-row", listClickAction);
    
    // Listeners for LIST SETTINGS page
    $('body').on('click', "#settings-submit", updateListAction);
    $('body').on('click', "#settings-archive", archiveListAction);
    $('body').on('click', "#settings-cancel", resetListSettingsAction);
    
    // Listeners for PROFILE page
    // Listeners for LIST MGMT page
    
    setInterval(updateListTimeDisplays, 1000*60);

    $("html").delay(fadeTime).queue(function() {
        $('#loading').fadeTo(fadeTime, 1);
        $(this).dequeue();
    }).delay(fadeTime).queue(function() {
        loadUser();
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
    selectList(id);
    showListSettings();
    setSettingsURL(lists[id]);
}

function gotoListItems(id) {
    selectList(id);
    refreshItems();
    setListURL(lists[id]);
}

function gotoProfile() {
    selectList(NOLIST);
    showProfile();
    nextPage("/profile", "Profile & Friends");
}

function gotoListManagement() {
    selectList(NOLIST);
    showListManagement();
    nextPage("/lists", "Profile & Friends");
}

function loadLists() {
    lists = {};
    callAPI("lists/getAll", {},
        function(response) { response.forEach(setList); },
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
        function(response) { response.forEach(setItem); },
        function(response) { error("Failed to load items!"); }
    );
}

function setItem(item) {
    item.value = item.name;
    items[item.id] = item;
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

function logout() {
    callAPIAsync("users/logout", true, {},
        function(list) { info("Logged out"); },
        function(response) { error("Failed to logout!"); }
    );
    $.cookie("loginError", "Logged out.", { path: '/' });
    $('html').queue(function() {
        hideAllPages();
        $(this).dequeue();
    }).delay(fadeTime).queue(function() {
        $("#lists").fadeTo(fadeTime, 0);
        $(this).dequeue();
    }).delay(fadeTime).queue(function() {
        $("#banner").fadeTo(fadeTime, 0);
        $(this).dequeue();
    }).delay(fadeTime).queue(function() {
        window.location.href = "/login";
        $(this).dequeue();
    })
}