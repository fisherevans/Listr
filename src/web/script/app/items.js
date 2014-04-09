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
function archiveItem(id) {

}

function removeItem(id) {

}