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
function loadLists() {
    lists = {};
    callAPI("lists/getAll", {},
        function(response) { response.forEach(setList); },
        function(response) { error("Failed to load lists!"); }
    );
}

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