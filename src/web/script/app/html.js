function getListHTML(list) {
    var HTML = "";
    HTML += "<div data-listid='" + list.id + "' id='lists-" + list.id + "' class='lists-row fadeColor'>";
    if(list.owner != user.username) {
        HTML += "    <i class='icon-users lists-shared lists-action fadeColor'></i>";
    } else {
        HTML += "    <i class='icon-cogs lists-settings lists-action fadeColor'></i>";
    }
    HTML += "    <i class='icon-star" + (list.favorited == 1 ? "2" : "") + " lists-favorite fadeColor'></i>";
    HTML += "    <div class='lists-name'>" + list.name + "</div>";
    HTML += "    <div class='lists-desc'>";
    HTML += "        Last Updated <span class='time' data-listid='" + list.id + "'>" + timeAgo(list.last_updated_date) + "</span> ago by " + list.last_updater + ".";
    if(list.owner != user.username) {
        HTML += "        Owned by " + list.owner + ".";

    } else {

    }
    HTML += "    </div>";
    HTML += "</div>";
    return HTML;
}

function getItemHTML(item) {
    var HTML = "";
    HTML += "<div class='list-item' id='item-" + item.id + "' data-itemid='" + item.id + "'>";
    HTML += "    <div class='icon-check list-item-check'></div>";
    HTML += "    <div class='list-item-name'>" + item.name + "</div>";
    HTML += "    <div class='icon-close list-item-x'></div>";
    HTML += "</div>";
    return HTML;
}

function getFriendPendingHTML(friend) {
    var HTML = "";
    HTML += "<div id='friend-row-" + friend.friend + "' class='page-list-row' data-user='" + friend.friend + "'>";
    HTML += "    <div class='page-list-row-label'>" + friend.friend + "</div>";
    HTML += "    <div class='page-list-row-note'>(" + friend.first_name + " " + friend.last_name + ")</div>";
    HTML += "    <div class='page-list-row-note'>(Pending)</div>";
    HTML += "    <div class='page-list-row-actions'>";
    HTML += "        <div class='page-list-row-action confirmFriend blue fadeColor'>Accept</div>";
    HTML += "        <div class='page-list-row-action removeFriend orange fadeColor'>Decline</div>";
    HTML += "    </div>";
    HTML += "</div>";
    return HTML;
}

function getFriendHTML(friend) {
    var HTML = "";
    HTML += "<div id='friend-row-" + friend.friend + "' class='page-list-row' data-user='" + friend.friend + "'>";
    HTML += "    <div class='page-list-row-label'>" + friend.friend + "</div>";
    HTML += "    <div class='page-list-row-note'>(" + friend.first_name + " " + friend.last_name + ")</div>";
    HTML += "    <div class='page-list-row-actions'>";
    HTML += "        <div class='page-list-row-action removeFriend red fadeColor'>Unfriend</div>";
    HTML += "    </div>";
    HTML += "</div>";
    return HTML;
}

function getFriendWaitingHTML(friend) {
    var HTML = "";
    HTML += "<div id='friend-row-" + friend.friend + "' class='page-list-row' data-user='" + friend.friend + "'>";
    HTML += "    <div class='page-list-row-label'>" + friend.friend + "</div>";
    HTML += "    <div class='page-list-row-note'>(Waiting on User)</div>";
    HTML += "    <div class='page-list-row-actions'>";
    HTML += "        <div class='page-list-row-action removeFriend red fadeColor'>Revoke</div>";
    HTML += "    </div>";
    HTML += "</div>";
    return HTML;
}


function getSharedHTML(shared) {
    var HTML = "";
    HTML += "<div id='shared-row-" + shared.user + "' class='page-list-row' data-user='" + shared.user + "'>";
    HTML += "    <div class='page-list-row-label'>" + shared.user + "</div>";
    if(shared.accepted == 0)
        HTML += "    <div class='page-list-row-note'>(Pending)</div>";
    HTML += "    <div class='page-list-row-actions'>";
    HTML += "        <div class='page-list-row-action unshare red fadeColor'>Un-Share</div>";
    HTML += "    </div>";
    HTML += "</div>";
    return HTML;
}

function getFriendsOptions() {
    var HTML = "<option value=''>Select a friend...</option>";
    forEachAssoc(friends, function(friend) {
        if(friend.state == ACCEPTED)
            HTML += "<option value='" + friend.friend + "'>" + friend.friend + " (" + friend.first_name + ")</option>";
    });
    return HTML;
}

function getListSettingsRowHTML(list) {
    var HTML = "";
    HTML += "<div id='list-mgmt-row-" + list.id + "' class='page-list-row' data-id='" + list.id + "'>";
    HTML += "    <div class='page-list-row-label";
    if(list.archived == 1)
        HTML += " grey";
    HTML += "'>" + list.name + "</div>";
    HTML += "    <div class='page-list-row-note'>";
    if(list.owner != user.username)
        HTML += "(Owned by " + list.owner + ") ";
    if(list.archived == 1)
        HTML += "Archived";
    HTML += "    </div>"
    HTML += "    <div class='page-list-row-actions'>";
    if(list.owner == user.username) {
        if(list.archived == 0) {
            HTML += "        <div class='page-list-row-action view blue fadeColor'>View</div>";
            HTML += "        <div class='page-list-row-action archive orange fadeColor'>Archive</div>";
        } else {
            HTML += "        <div class='page-list-row-action restore blue fadeColor'>Restore</div>";
            HTML += "        <div class='page-list-row-action delete red bold fadeColor'>Delete</div>";
        }
    } else {
        if(list.share_status == 1) {
            HTML += "        <div class='page-list-row-action ignore orange fadeColor'>Remove</div>";
        } else {
            HTML += "        <div class='page-list-row-action acceptShare blue fadeColor'>Accept</div>";
            HTML += "        <div class='page-list-row-action ignore red fadeColor'>Ignore</div>";
        }
    }
    HTML += "    </div>";
    HTML += "</div>";
    return HTML;
}

function getShareNotificationRowHTML(list) {
    var HTML = "";
    HTML += "<div id='notification-share-row-" + list.id + "' class='page-list-row' data-id='" + list.id + "'>";
    HTML += "    <div class='page-list-row-label'>" + list.name + "</div>";
    HTML += "    <div class='page-list-row-note'>(Owned by " + list.owner + ")</div>"
    HTML += "    <div class='page-list-row-actions'>";
    HTML += "        <div class='page-list-row-action acceptShareNot blue fadeColor'>Accept</div>";
    HTML += "        <div class='page-list-row-action ignoreNot red fadeColor'>Ignore</div>";
    HTML += "    </div>";
    HTML += "</div>";
    return HTML;
}

function getFriendNotificationRowHTML(friend) {
    var HTML = "";
    HTML += "<div id='notification-friend-row-" + friend.friend + "' class='page-list-row' data-user='" + friend.friend + "'>";
    HTML += "    <div class='page-list-row-label'>" + friend.friend + "</div>";
    HTML += "    <div class='page-list-row-note'>(" + friend.first_name + " " + friend.last_name + ")</div>";
    HTML += "    <div class='page-list-row-note'>(Pending)</div>";
    HTML += "    <div class='page-list-row-actions'>";
    HTML += "        <div class='page-list-row-action confirmFriendNot blue fadeColor'>Accept</div>";
    HTML += "        <div class='page-list-row-action removeFriendNot orange fadeColor'>Decline</div>";
    HTML += "    </div>";
    HTML += "</div>";
    return HTML;
}

function getMiscNotificationRowHTML(not) {
    var HTML = "";
    HTML += "<div id='notification-misc-row-" + not.id + "' class='page-list-row' data-id='" + not.id + "'>";
    HTML += "    <div class='page-list-row-label'>" + not.message + "</div>";
    HTML += "    <div class='page-list-row-note'>(" + timeAgo(not.last_updated_date) + " ago)</div>";
    HTML += "    <div class='page-list-row-actions'>";
    HTML += "        <div class='page-list-row-action okayNot blue fadeColor'>Got It!</div>";
    HTML += "    </div>";
    HTML += "</div>";
    return HTML;
}