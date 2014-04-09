function updateProfile() {

}

function resetProfileInfo() {

}

function addFriend() {

}

function removeFriend(user) {

}

function acceptFriend(user) {

}

function declineFriend(user) {

}

// METHODS ######################
function loadUser() {
    callAPI("users/self", {},
        function(response) { user = response; },
        function(response) { error("Failed to load userProfile!"); }
    );
}