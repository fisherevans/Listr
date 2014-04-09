// ACTIONS ######################
function toggleProfileDropdown() {
    $("#profile-dropdown").toggleClass("show");
    $("#profile").toggleClass("show");
    if($("#profile-dropdown").hasClass("show"))
        $("#profile-dropdown").css("height", $("#profile-dropdown-wrapper").height() + "px");
    else
        $("#profile-dropdown").css("height", "0px");
}

// METHODS ######################
function loadUser() {
    callAPI("users/self", {},
        function(response) { user = response; },
        function(response) { error("Failed to load userProfile!"); }
    );
}

function showProfile() {
    $(".page").removeClass("visible");
    $("#user-profile").delay(fadeTime).queue(function() {
        $(".page").addClass("no-display");
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