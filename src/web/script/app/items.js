// ACTIONS ########################
function itemClickAction(event) {
    var id = $(event.target).closest(".list-item").data("itemid");
    if($(event.target).hasClass("list-item-x")) setItemState(id, ARCHIVED);
    else toggleItem(id);
}

function addItemAction(event) {
    if(event.keyCode == 13)
        addItem();
}

function removeAutocompletItemAction(event) {
    if($(this).hasClass("delete")) {
        removeItem($(this).data('id'));
        $('#add-item-input').val($('#add-item-input').autocomplete().getLastTypedValue());
        $('#add-item-input').focus();
    }
}

function editListAction(event) {
    gotoListSettings(currentList);
}

// METHODS ###################

function refreshItems() {
    $(".page").removeClass("visible");
    $("#list").delay(fadeTime).queue(function() {
        $(".page").addClass("no-display");
        $(this).removeClass("no-display");
        loadItems();
        $("#list-checked-panel, #list-unchecked-panel").html("");
        forEachAssoc(items, addItemDisplayNoDelay);
        sortItemsDisplay();
        registerNewItemAutocomplete();
        $("#list-description .text").text(lists[currentList].description);
        $("#list-description .text").prepend("<b>" + $("<div/>").text(lists[currentList].name).html() + ": </b>");
        if(lists[currentList].owner == user.username) {
            $("#edit-description").removeClass("hidden");
        } else {
            $("#edit-description").addClass("hidden");
            $("#list-description .text").prepend("This list is owned by " + lists[currentList].owner + ".<br />");
        }
        $(this).addClass("visible");
        $(this).dequeue();
    });
}

function addItemDisplayNoDelay(item) {
    addItemDisplay(item, false);
}

function addItemDisplay(item, delayShow) {
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
        addItemDisplay(items[id], true);
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
                addItemDisplay(item, true);
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

function sortItemsDisplay() {
    sortUsingNestedText($("#list-checked-panel"), ".list-item", ".list-item-name");
    sortUsingNestedText($("#list-unchecked-panel"), ".list-item", ".list-item-name");
}