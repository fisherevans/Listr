// ACTIONS ######################
function toggleProfileDropdown() {
	$("#profile-dropdown").toggleClass("show");
	$("#profile").toggleClass("show");
	if ($("#profile-dropdown").hasClass("show"))
		$("#profile-dropdown").css("height", $("#profile-dropdown-wrapper").height() + "px");
	else
		$("#profile-dropdown").css("height", "0px");
}

function updateProfileAction() {
	if (user.first_name != $("#profile-first-name").val()
		 || user.last_name != $("#profile-last-name").val()
		 || user.email != $("#profile-email").val()
		 || "" != $("#profile-password-new1").val()) {
		$("#profile-update").val("Loading...");
		var action = "updateNoPassword";
		if ($("#profile-password-new1").val() != "") {
			action = "update";
			if ($("#profile-password-new1").val() != $("#profile-password-new2").val()) {
				$("#profile-update-notification").text("New passwords do not match!").css("color", "#a00")
                    .fadeTo(fadeTime, 1).delay(fadeTime * 6).fadeTo(fadeTime, 0);
				return;
			}
		}
		callAPI("users/" + action, {
			"username" : user.username,
			"first_name" : $("#profile-first-name").val(),
			"last_name" : $("#profile-last-name").val(),
			"email" : $("#profile-email").val(),
			"password" : $("#profile-password").val(),
			"passwordNew" : $("#profile-password-new1").val()
		},
			function (response) {
			loadUser();
			clearProfileForm();
			$("#profile-update-notification").text(response.response).css("color", "#0a0")
                .fadeTo(fadeTime, 1).delay(fadeTime * 6).fadeTo(fadeTime, 0);
		},
			function (response) {
			$("#profile-update-notification").text(response.response).css("color", "#a00")
                .fadeTo(fadeTime, 1).delay(fadeTime * 6).fadeTo(fadeTime, 0);
		});
		$("#profile-update").val("Update");
	} else {
		$("#profile-update-notification").text("No new information").css("color", "#a00")
            .fadeTo(fadeTime, 1).delay(fadeTime * 6).fadeTo(fadeTime, 0);
	}
}

function resetProfileInfoAction() {
    clearProfileForm();
}

function addFriendAction() {
	if ("" != $("#profile-friend-input").val()) {
		$("#profile-friend-button").val("Loading...");
		callAPI("users/friend", {
			"friend" : $("#profile-friend-input").val()
		},
			function (friend) {
			$("#profile-friend-notification").text("Friend request sent.").css("color", "#0a0")
                .fadeTo(fadeTime, 1).delay(fadeTime * 6).fadeTo(fadeTime, 0);
			$("#profile-friend-input").val("");
            setFriend(friend);
			addFriendDisplay(friend, true);
			sortFriendDisplay();
		},
			function (response) {
			$("#profile-friend-notification").text(response.response).css("color", "#a00")
			.fadeTo(fadeTime, 1).delay(fadeTime * 6).fadeTo(fadeTime, 0);
		});
		//$("#profile-friend-button").val("Add Friend");
	} else {
		$("#profile-friend-notification").text("Please enter a username.").css("color", "#a00")
		.fadeTo(fadeTime, 1).delay(fadeTime * 6).fadeTo(fadeTime, 0);
	}
}

function removeFriendAction(event) {
    var user = $(event.target).closest(".page-list-row").data("user");
    var doAction = window.confirm("Are you sure you want to unfriend " + user + "?");
    if(!doAction)
        return;
    callAPI("users/unfriend", {
        "friend" : user
    },
        function (response) {
        $("#profile-friend-notification").text("Friend removed.").css("color", "#0a0")
            .fadeTo(fadeTime, 1).delay(fadeTime * 6).fadeTo(fadeTime, 0);
        removeFriendDisplay(friends[user]);
        delete friends[user];
        sortFriendDisplay();
        countNotifications();
    },
        function (response) {
        $("#profile-friend-notification").text(response.response).css("color", "#a00")
            .fadeTo(fadeTime, 1).delay(fadeTime * 6).fadeTo(fadeTime, 0);
    });
}

function confirmFriendAction(event) {
    var user = $(event.target).closest(".page-list-row").data("user");
    callAPI("users/confirmFriend", {
        "friend" : user
    },
        function (friend) {
        $("#profile-friend-notification").text("Friend request confirmed.").css("color", "#0a0")
            .fadeTo(fadeTime, 1).delay(fadeTime * 6).fadeTo(fadeTime, 0);
        setFriend(friend);
        updateFriendDisplay(friend);
        countNotifications();
    },
        function (response) {
        $("#profile-friend-notification").text(response.response).css("color", "#a00")
            .fadeTo(fadeTime, 1).delay(fadeTime * 6).fadeTo(fadeTime, 0);
    });
}

// METHODS ######################
function loadUser() {
	user = {};
	callAPI("users/self", {},
		function (response) {
		user = response;
		$("#profile-name").text(user.first_name + " " + user.last_name);
	},
		function (response) {
		error("Failed to load userProfile!");
	});
}

function loadFriends() {
	friends = {};
	callAPI("users/friends", {},
		function (response) {
            response.forEach(setFriend);
        },
		function (response) {
            error("Failed to load fiends!");
        }
    );
}

function setFriend(friend) {
    friends[friend.friend] = friend;
}

function showProfile() {
	$(".page").removeClass("visible");
	$("#user-profile").delay(fadeTime).queue(function () {
        loadFriends();
		$(".page").addClass("no-display");
		$(this).removeClass("no-display");
		$(this).dequeue();
		clearProfileForm();
		loadFriendList();
	}).delay(0).queue(function () {
		$(this).addClass("visible");
		$(this).dequeue();
	});
}

function clearProfileForm() {
	$("#profile-first-name").val(user.first_name);
	$("#profile-last-name").val(user.last_name);
	$("#profile-email").val(user.email);
	$("#profile-password, #profile-password-new1, #profile-password-new2").val("");
}

function loadFriendList() {
	$("#profile-friends-list").html("");
	$("#profile-friends-pending-list").html("");
	$("#profile-friends-waiting-list").html("");
	forEachAssoc(friends, addFriendDisplayNoDelay);
	sortFriendDisplay();
}

function updateFriendDisplay(friend) {
    var elId = "#friend-row-" + friend.friend;
    if($(elId).length != 0) {
        removeFriendDisplay(friend);
    }
    $("html").delay(0).queue(function() {
        addFriendDisplay(friend, true);
        sortFriendDisplay();
        $(this).dequeue();
    });
}

function addFriendDisplayNoDelay(friend) {
	addFriendDisplay(friend, false);
}

function addFriendDisplay(friend, delayShow) {
    if (friend.state == ACCEPTED)
        $("#profile-friends-list").append(getFriendHTML(friend));
    else if (friend.state == PENDING)
        $("#profile-friends-pending-list").append(getFriendPendingHTML(friend));
    else if (friend.state == WAITING)
        $("#profile-friends-waiting-list").append(getFriendWaitingHTML(friend));
    if(delayShow == undefined || delayShow)
        $("html").delay(0);
    $("html").queue(function() {
        $("#friend-row-" + friend.friend).addClass("show");
        $(this).dequeue();
    });
}

function removeFriendDisplay(friend) {
    var elId = "#friend-row-" + friend.friend;
    $("html").queue(function() {
        $("#friend-row-" + friend.friend).removeClass("show")
        $(this).dequeue();
    }).delay(fadeTime).queue(function() {
        $("#friend-row-" + friend.friend).remove();
        $(this).dequeue();
    });
}

function sortFriendDisplay() {
	sortUsingNestedText($("#profile-friends-list"), ".page-list-row", ".page-list-row-label");
	sortUsingNestedText($("#profile-friends-pending-list"), ".page-list-row", ".page-list-row-label");
	sortUsingNestedText($("#profile-friends-waiting-list"), ".page-list-row", ".page-list-row-label");

    $("html").delay(10).queue(function() {
		if($("#profile-friends-list .page-list-row.show").length == 0)
			$("#profile-friends-list-label").addClass("hide");
		else
			$("#profile-friends-list-label").removeClass("hide");

		if($("#profile-friends-pending-list .page-list-row.show").length == 0)
			$("#profile-friends-pending-list-label").addClass("hide");
		else
			$("#profile-friends-pending-list-label").removeClass("hide");

		if($("#profile-friends-waiting-list .page-list-row.show").length == 0)
			$("#profile-friends-waiting-list-label").addClass("hide");
		else
			$("#profile-friends-waiting-list-label").removeClass("hide");
        $(this).dequeue();
    });
}

function logout() {
	callAPIAsync("users/logout", true, {},
		function (list) {
		info("Logged out");
	},
		function (response) {
		error("Failed to logout!");
	});
	$.cookie("loginError", "Logged out.", {
		path : '/'
	});
	$('html').queue(function () {
		hideAllPages();
		$(this).dequeue();
	}).delay(fadeTime).queue(function () {
		$("#lists").fadeTo(fadeTime, 0);
		$(this).dequeue();
	}).delay(fadeTime).queue(function () {
		$("#banner").fadeTo(fadeTime, 0);
		$(this).dequeue();
	}).delay(fadeTime).queue(function () {
		window.location.href = "/login";
		$(this).dequeue();
	})
}
