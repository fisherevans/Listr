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

function setList(list) {
    list.last_updated_date = new Date(list.last_updated.replace(" ", "T"));
    list.last_updated_date.setHours(list.last_updated_date.getHours()+4);
    lists[list.id] = list;
}

function hideAllPages() {
    selectList(NOLIST);
    $(".page").removeClass("visible");
    $("html").delay(fadeTime).queue(function() {
        $(".page").addClass("no-display");
        $(this).dequeue();
    });
}

function selectList(id) {
    currentList = id;
    $(".lists-row:not(#lists-" + id + ")").removeClass("current").delay(fadeTime).queue(function() {
        $("#lists-" + id).addClass("current"); 
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

function showListSettings() {
    $(".content-window").removeClass("visible");
    $("#settings").delay(fadeTime).queue(function() {
        $(".content-window").addClass("no-display");
        $(this).removeClass("no-display");
        $(this).dequeue();
    }).delay(0).queue(function() {
        $("#settings-name").val(lists[currentList].name);
        $("#settings-description").val(lists[currentList].description);
        $(this).addClass("visible");
        $(this).dequeue();
    });
}

function toggleFavoriteList(id) {
    var action = lists[id].favorited == 1 ? "un" : "";
    callAPI("lists/" + action + "favorite", {"list_id":id},
        function(list) { lists[id] = list; },
        function(response) { error("Failed to favorite list!"); }
    );
    var icon = lists[id].favorited == 1 ? "2" : "";
    debug(icon);
    $("#lists-" + id + " .lists-favorite").removeClass("icon-star icon-star2").addClass("icon-star" + icon);
}

function refreshItems() {
    $(".content-window").removeClass("visible");
    $("#list").delay(fadeTime).queue(function() {
        $(".content-window").addClass("no-display");
        $(this).removeClass("no-display");
        items = {};
        callAPI("lists/items/getAll", {"list_id":currentList},
            function(response) { response.forEach(setItem); },
            function(response) { error("Failed to load items!"); }
        );
        $("#list-checked-panel, #list-unchecked-panel").html("");
        forEachAssoc(items, addItemToDisplayNoDelay);
        sortItemsDisplay();
        registerNewItemAutocomplete();
        $("#list-description .text").text(lists[currentList].description);
        $("#list-description .text").prepend("<b>" + $("<div/>").text(lists[currentList].name).html() + ": </b>");
        $(this).addClass("visible");
        $(this).dequeue();
    });
}

function setItem(item) {
    item.value = item.name;
    items[item.id] = item;
}

function addItemToDisplayNoDelay(item) {
    addItemToDisplay(item, false);
}

function addItemToDisplay(item, delayShow) {
    if(item.state != ARCHIVED) {
        var panel = item.state == CHECKED ? "" : "un";
        debug("adding item " + item.id + " to #list-" + panel + "checked-panel");
        $("#list-" + panel + "checked-panel").append(getItemHTML(item));
        if(delayShow == undefined || delayShow)
            $("#item-" + item.id).delay(0);
        $("#item-" + item.id).queue(function() {
            $(this).addClass("show");
            $(this).dequeue();
        });
    }
}

function registerNewItemAutocomplete() {
    $('#add-item-input').val("");
    $('#add-item-input').autocomplete({
        lookup: items,
        lookupFunction: function(item) { return item.state != UNCHECKED; },
        showDelete: true,
        containerClass: "autocomplete-suggestions item-input"
    });
}

function toggleProfileDropdown() {
    $("#profile-dropdown").toggleClass("show");
    $("#profile").toggleClass("show");
    if($("#profile-dropdown").hasClass("show"))
        $("#profile-dropdown").css("height", $("#profile-dropdown-wrapper").height() + "px");
    else
        $("#profile-dropdown").css("height", "0px");
}

function toggleItem(id) {
    var item = items[id];
    var state = item.state == ARCHIVED || item.state == CHECKED ?
            UNCHECKED : CHECKED;
    setItemState(id, state);
}

function setItemState(id, state) {
    var elId = "#item-" + id;
    if($(elId).length != 0) {
        $("html").queue(function() {
            $(elId).removeClass("show")
            $(this).dequeue();
        }).delay(fadeTime).queue(function() {
            $(elId).remove();
            $(this).dequeue();
        });
    }
    $("html").queue(function() {
        callAPI("lists/items/setState", {"item_id":id, "state":state}, 
            function(item) { setItem(item); },
            function(response) { error("Failed to add item!"); }
        );
        addItemToDisplay(items[id], true);
        sortItemsDisplay();
        updateCurrentList();
        $(this).dequeue();
    });
}

function addItem() {
    var itemName = $("#add-item-input").val();
    callAPI("lists/items/add", {"list_id":lists[currentList].id, "item_name":itemName}, 
        function(item) {
            setItem(item);
            if($("#item-" + item.id).length == 0) {
                addItemToDisplay(item, true);
                sortItemsDisplay();
            } else
                setItemState(item.id, UNCHECKED);
            updateCurrentList();
        },
        function(response) { error("Failed to add item"); }
    );
    $("#add-item-input").autocomplete("hide");
    $("#add-item-input").val("");
    $('#add-item-input').focus();
}

function addList() {
    var listName = $("#add-list-input").val();
    $("#add-list-input").val("");
    callAPI("lists/add", {"name":listName, "description":"A newly made list."}, 
        function(list) {
            $("#add-item-input").val("");
            setList(list);
            addListDisplay(list);
            sortListsDisplay();
            selectList(list.id);
            gotoListItems(list.id);
        },
        function(response) { error("Failed to add item!"); }
    );
}

function findItemByName(name) {
    for (var id in items)
        if (items.hasOwnProperty(id))
            if(items[id].name == name)
                return id;
    return -1;
}

function removeItem(id) {
    $("#item-" + id).removeClass("show").delay(fadeTime).queue(function() {
        $(this).remove();
        $(this).dequeue();
    });
    delete items[id];
    callAPI("lists/items/remove", {"item_id":id},
        function(response) { debug("Item removed!"); updateCurrentList(); },
        function(response) { error("Failed to archive item!"); }
    );
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

function sortItemsDisplay() {
    sortUsingNestedText($("#list-checked-panel"), ".list-item", ".list-item-name");
    sortUsingNestedText($("#list-unchecked-panel"), ".list-item", ".list-item-name");
}


function updateCurrentList() {
    callAPIAsync("lists/get", true, {"list_id":lists[currentList].id},
        function(list) {
            setList(list);
            updateListTimeDisplays();
        },
        function(response) { error("Failed to update list!"); }
    );
}

function updateListTimeDisplays() {
    $("#lists .time").each(function() {
        list = lists[$(this).data("listid")];
        $(this).text(timeAgo(list.last_updated_date));
    });
}

function gotoProfile() {
    selectList(NOLIST);
    showProfile();
    nextPage("/profile", "Profile & Friends"); 
}

function showProfile() {
    $(".content-window").removeClass("visible");
    $("#user-profile").delay(fadeTime).queue(function() {
        $(".content-window").addClass("no-display");
        $(this).removeClass("no-display");
        $("#profile-first-name").val(user.first_name);
        $("#profile-last-name").val(user.last_name);
        $("#profile-email").val(user.email);
        $("#profile-password").val("");
        $(this).dequeue();
    }).delay(0).queue(function() {
        $(this).addClass("visible");
        $(this).dequeue();
    });
}

function gotoListManagement() {
    selectList(NOLIST);
    showListManagement();
    nextPage("/lists", "Profile & Friends"); 
}

function showListManagement() {
    $(".content-window").removeClass("visible");
    $("#list-management").delay(fadeTime).queue(function() {
        $(".content-window").addClass("no-display");
        $(this).removeClass("no-display");
        $(this).dequeue();
    }).delay(0).queue(function() {
        $(this).addClass("visible");
        $(this).dequeue();
    });
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