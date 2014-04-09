// ACTIONS ############################
function updateListAction(event) {
    if(lists[currentList].name != $("#settings-name").val()
            || lists[currentList].description != $("#settings-description").val()) {
        $(this).val("Loading...");
        callAPI("lists/edit", {
                "list_id":currentList,
                "name":$("#settings-name").val(),
                "description":$("#settings-description").val()
            },
            function(list) {
                setList(list);
                updateListDisplay(list);
                $("#settings-edit-notification").text("List updated!").css("color", "#0a0")
                        .fadeTo(fadeTime, 1).delay(fadeTime).fadeTo(fadeTime, 0);
            },
            function(response) {
                $("#settings-edit-notification").text(response.response).css("color", "#a00")
                        .fadeTo(fadeTime, 1).delay(fadeTime*6).fadeTo(fadeTime, 0);
            }
        );
        $(this).val("Save");
    }
}

function resetListSettingsAction(event) {
    $("#settings-name").val(lists[currentList].name);
    $("#settings-description").val(lists[currentList].description);
}

function archiveListAction(event) {
    var doArchive = window.confirm("Are you sure you want to archive this list?");
    if(doArchive) {
        $(this).val("Loading...");
        callAPI("lists/archive", { "list_id":currentList },
            function(list) {
                setList(list);
                removeListDisplay(list);
                hideAllPages();
            },
            function(response) {
                $("#settings-other-notification").text("Failed to archive list!").css("color", "#a00")
                        .fadeTo(fadeTime, 1).delay(fadeTime*2).fadeTo(fadeTime, 0);
            }
        );
        $(this).val("Archive");
    }
}

// METHODS #############################
function updateList() {

}

function resetListInfo() {

}

function shareList() {

}

function archiveList() {

}

function restoreList() {

}

function deleteList() {

}

function giveList() {

}