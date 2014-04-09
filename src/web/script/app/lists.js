// ACTIONS ########################
function listClickAction(event) {
    var id = $(event.target).closest(".lists-row").data("listid");
    if($(event.target).hasClass("lists-settings")) gotoListSettings(id);
    else if($(event.target).hasClass("lists-favorite")) toggleFavoriteList(id);
    else gotoListItems(id);
}

function addListAction(event) {
    if(event.keyCode == 13)
        addList();
}

// METHODS ########################

function refreshLists() {
    $("html").delay(fadeTime).queue(function() {
        $(".lists-row").removeClass("show");
        loadLists();
        $(this).dequeue();
    }).queue(function() {
        forEachAssoc(lists, addListDisplay);
        sortListsDisplay();
        selectList(currentList);
        $(this).dequeue();
    });
}

function addListDisplay(list) {
    if(list.archived != 1) {
        $("#lists-height-panel").append(getListHTML(list));
        if(list.id == currentList) $("#lists-" + list.id).addClass("current");
        sortListsDisplay();
        updateListTimeDisplays();
        $("#lists-" + list.id).delay(0).queue(function() {
            $(this).addClass("show");
            $(this).dequeue();
        });
    }
}

function removeListDisplay(list) {
    $("#lists-" + list.id).removeClass("show");
    $('html').delay(fadeTime).queue(function() {
        $("#lists-" + list.id).remove();
        $(this).dequeue();
    });
}

function updateListDisplay(list) {
    removeListDisplay(list);
    $('html').delay(fadeTime).queue(function() {
        addListDisplay(list);
        $(this).dequeue();
    });
}

function sortListsDisplay() {
    sortUsingNestedText($("#lists-height-panel"), ".lists-row", ".lists-name");
}

function selectList(id) {
    currentList = id;
    $(".lists-row:not(#lists-" + id + ")").removeClass("current").delay(fadeTime).queue(function() {
        $("#lists-" + id).addClass("current");
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