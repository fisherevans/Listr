// ACTIONS ############################
function updateListAction(event) {
    if(lists[currentList].name != $("#settings-name").val()
            || lists[currentList].description != $("#settings-description").val()) {
        $("#settings-submit").val("Loading...");
        callAPI("lists/edit", {
                "list_id":currentList,
                "name":$("#settings-name").val(),
                "description":$("#settings-description").val()
            },
            function(list) {
                setList(list);
                updateListDisplay(list);
                $("#settings-edit-notification").text("List updated!").css("color", "#0a0")
                        .fadeTo(fadeTime, 1).delay(fadeTime*6).fadeTo(fadeTime, 0);
            },
            function(response) {
                $("#settings-edit-notification").text(response.response).css("color", "#a00")
                        .fadeTo(fadeTime, 1).delay(fadeTime*6).fadeTo(fadeTime, 0);
            }
        );
        $("#settings-submit").val("Update");
    }
}

function resetListSettingsAction(event) {
    $("#settings-name").val(lists[currentList].name);
    $("#settings-description").val(lists[currentList].description);
}

function archiveListAction(event) {
    var doAction = window.confirm("Are you sure you want to archive this list?");
    if(!doAction)
        return;
    $(this).val("Loading...");
    $(this).val("Archive");
}

function shareListAction() {
    var user = $("#share-select").find(":selected").val();
    debug("Sharing " + user);
    if(user != "") {
        callAPI("lists/share", { "list_id":currentList, "username":user },
            function(response) {
                var userObj = {"user":user, "accepted":0};
                lists[currentList]['shared'].push(userObj);
                addListShared(userObj, true);
                $("#settings-share-notification").text(response.response).css("color", "#0a0")
                        .fadeTo(fadeTime, 1).delay(fadeTime*2).fadeTo(fadeTime, 0);
                sortListShared();
            },
            function(response) {
                $("#settings-share-notification").text(response.response).css("color", "#a00")
                        .fadeTo(fadeTime, 1).delay(fadeTime*2).fadeTo(fadeTime, 0);
            }
        );
    } else {
        $("#settings-share-notification").text("Please select a friend.").css("color", "#a00")
                .fadeTo(fadeTime, 1).delay(fadeTime*2).fadeTo(fadeTime, 0);
    }
}

function unshareListAction(event) {
    var user = $(event.target).closest(".page-list-row").data("user");
    var doAction = window.confirm("Are you sure you want to unshare this list with " + user + "?");
    if(!doAction)
        return;
    debug("Unsharing " + user);
    if(user != "") {
        callAPI("lists/unshare", { "list_id":currentList, "username":user },
            function(response) {
                lists[currentList]['shared'].splice(lists[currentList]['shared'].indexOf(user), 1 );
                removeListShared(user);
                $("#settings-share-notification").text(response.response).css("color", "#0a0")
                        .fadeTo(fadeTime, 1).delay(fadeTime*2).fadeTo(fadeTime, 0);
            },
            function(response) {
                $("#settings-share-notification").text(response.response).css("color", "#a00")
                        .fadeTo(fadeTime, 1).delay(fadeTime*2).fadeTo(fadeTime, 0);
            }
        );
    } else {
        $("#settings-share-notification").text("Please select a friend.").css("color", "#a00")
                .fadeTo(fadeTime, 1).delay(fadeTime*2).fadeTo(fadeTime, 0);
    }
}

// METHODS #############################

function showListSettings() {
    $(".page").removeClass("visible");
    $("#settings").delay(fadeTime).queue(function() {
        $(".page").addClass("no-display");
        $(this).removeClass("no-display");
        $("#settings-name").val(lists[currentList].name);
        $("#settings-description").val(lists[currentList].description);
        $("#share-select").html(getFriendsOptions());
        $("#shared-with-list").html("");
        if(lists[currentList]['shared'].length > 0)
            lists[currentList]['shared'].forEach(addListShared);
        sortListShared();
        $(this).dequeue();
    }).delay(0).queue(function() {
        $(this).addClass("visible");
        $(this).dequeue();
    });
}

function addListShared(shared, showDelay) {
    if(showDelay == undefined)
        showDelay = false;
    $("#shared-with-list").append(getSharedHTML(shared));
    if(showDelay) {
        $("#shared-row-" + shared.user).delay(0).queue(function() {
            $(this).addClass("show");
            $(this).dequeue();
        });
    } else
        $("#shared-row-" + shared.user).addClass("show");
}

function removeListShared(user) {
    $("#shared-row-" + user).queue(function() {
        $(this).removeClass("show");
        $(this).dequeue();
    }).delay(fadeTime).queue(function() {
        $(this).remove();
        $(this).dequeue();
    });
}

function sortListShared() {
    $("html").delay(10).queue(function() {
        sortUsingNestedText($("#shared-with-list"), ".page-list-row", ".page-list-row-label");
        $(this).dequeue();
    });
}