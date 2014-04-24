function mgmtArchiveAction(event) {
    var doAction = window.confirm("Are you sure you want to archive this list?");
    if(!doAction)
        return;
    var list_id = $(event.target).closest(".page-list-row").data("id");
    callAPI("lists/archive", { "list_id":list_id },
        function(list) {
            setList(list);
            updateListMgmtList(list);
            removeListDisplay(list);
            sortListMgmtList();
        },
        function(response) {
            error("Failed to archive list");
        }
    );
}

function mgmtDeleteAction(event) {
    var doAction = window.confirm("Are you sure you want to DELETE THIS LIST FOREVER?");
    if(!doAction)
        return;
    var list_id = $(event.target).closest(".page-list-row").data("id");
    callAPI("lists/remove", { "list_id":list_id },
        function(response) {
        	var list = lists[list_id];
        	delete lists[list_id];
            updateListMgmtList(list);
            removeListDisplay(list);
            sortListMgmtList();
        },
        function(response) {
            error("Failed to delete list");
        }
    );
}

function mgmtRestoreAction(event) {
    var list_id = $(event.target).closest(".page-list-row").data("id");
    callAPI("lists/restore", { "list_id":list_id },
        function(list) {
            setList(list);
            updateListMgmtList(list);
            addListDisplay(list);
            sortListMgmtList();
        },
        function(response) {
            error("Failed to restore list");
        }
    );
}

function mgmtViewAction(event) {
    var list_id = $(event.target).closest(".page-list-row").data("id");
    gotoListItems(list_id);
}

function mgmtIgnoreAction(event) {
    var doAction = window.confirm("Are you sure you want to ignore this list?");
    if(!doAction)
        return;
    var list_id = $(event.target).closest(".page-list-row").data("id");
    callAPI("lists/unshare", { "list_id":list_id, "username":user.username },
        function(response) {
        	var list = lists[list_id];
        	delete lists[list_id];
            removeListMgmtList(list);
            removeListDisplay(list);
            sortListMgmtList();
            countNotifications();
        },
        function(response) {
            error("Failed to unshare list");
        }
    );
}

function mgmtAcceptShareAction(event) {
    var list_id = $(event.target).closest(".page-list-row").data("id");
    callAPI("lists/acceptShare", { "list_id":list_id, "username":user.username },
        function(list) {
            setList(list);
            updateListMgmtList(list);
            addListDisplay(list);
            sortListMgmtList();
            countNotifications();
        },
        function(response) {
            error("Failed to unshare list");
        }
    );
}

function gotoListManagement() {
    selectList(NOLIST);
    nextPage("/lists", "Profile & Friends");
    $("#list-mgmt-yours-list, #list-mgmt-friends-list").html("");
    forEachAssoc(lists, addListMgmtList);
    showListManagement();
    sortListMgmtList();
}

function showListManagement() {
    $(".page").removeClass("visible");
    $("#list-management").delay(fadeTime).queue(function() {
        $(".page").addClass("no-display");
        $(this).removeClass("no-display");
        $(this).dequeue();
    }).delay(0).queue(function() {
        $(this).addClass("visible");
        $(this).dequeue();
    });
}

function addListMgmtList(list, showDelay) {
    $("html").queue(function() {
	    if(list.owner == user.username)
	    	$("#list-mgmt-yours-list").append(getListSettingsRowHTML(list));
	    else
	    	$("#list-mgmt-friends-list").append(getListSettingsRowHTML(list));
        $(this).dequeue();
    });
    if(showDelay == undefined || showDelay)
        $("html").delay(0);
    $("html").queue(function() {
        $("#list-mgmt-row-" + list.id).addClass("show");
        $(this).dequeue();
    });
}

function removeListMgmtList(list) {
    $("html").queue(function() {
        $("#list-mgmt-row-" + list.id).removeClass("show");
        $(this).dequeue();
    }).delay(fadeTime).queue(function() {
        $("#list-mgmt-row-" + list.id).remove();
        $(this).dequeue();
    });
}

function updateListMgmtList(list) {
    var elId = "#list-mgmt-row-" + list.id;
    if($(elId).length > 0)
        removeListMgmtList(list);
    $("html").delay(0).queue(function() {
        addListMgmtList(list, true);
        sortListMgmtList();
        $(this).dequeue();
    });
}

function sortListMgmtList() {
    $("html").delay(10).queue(function() {
		sortUsingNestedText($("#list-mgmt-yours-list"), ".page-list-row", ".page-list-row-label");
		sortUsingNestedText($("#list-mgmt-friends-list"), ".page-list-row", ".page-list-row-label");

        var count = 0;

		if($("#list-mgmt-yours-list .page-list-row.show").length == 0)
			$("#list-mgmt-yours-list-label").addClass("hide");
		else {
			$("#list-mgmt-yours-list-label").removeClass("hide");
            count++;
        }

		if($("#list-mgmt-friends-list .page-list-row.show").length == 0)
			$("#list-mgmt-friends-list-label").addClass("hide");
		else {
			$("#list-mgmt-friends-list-label").removeClass("hide");
            count++;
        }

        if(count > 0)
            $("#list-mgmt-none-label").addClass("hide");
        else
            $("#list-mgmt-none-label").removeClass("hide");

        $(this).dequeue();
    });
}