<div id='need-js'>
    This site requires a modern web browser and java-script.
</div>
<div id='loading'> 
    Loading...
</div>
<div id='banner'>
    <div id='logo'>Listr</div>
    <div onClick='javascript:toggleProfileDropdown()' id='profile' class='fadeColor'>
        <div id='profile-name' class='fadeColor'>Fisher Evans</div>
        <!--<img id='profile-avatar' src='/web/img/avatar.png'></img>-->
        <div class='icon-user2 fadeColor' id='profile-icon'></div>
        <img id='profile-arrow' src='/web/img/arrow.png'></img>
        <div id='profile-dropdown' class='fadeHeight'>
            <div id='profile-dropdown-wrapper'>
                <div onClick='javascript:gotoProfile();' class='profile-dropdown-link fadeColor'>Profile & Friends</div>
                <div onClick='javascript:gotoListManagement();' class='profile-dropdown-link fadeColor'>List Management</div>
                <div onClick='javascript:logout();' class='profile-dropdown-link fadeColor'>Logout</div>
            </div>
        </div>
    </div>
    <div onClick='javascript:gotoNotifications()' id='notifications-button' class='fadeColor'>0</div>
    <div id='notifications-label' class='fadeColor'>
        Notifications
    </div>
</div>
<div id='content-wrapper'>
    <div id='lists'>
        <div id='add-list'>
            <div onClick='javascript:addList();' class='fadeColor icon-plus' id='add-list-button'></div>
            <div id='add-list-resize'>
                <input class='fadeColor' type='text' placeholder='New List...' id='add-list-input' />
            </div>
        </div>
        <div id='lists-panel' class='fadeHeight'>
            <div id='lists-height-panel'></div>
        </div>
        <a id='archived-lists-link' class='fadeColor' href="javascript:gotoListManagement();">Manage Lists</a>
    </div>
    <div id='list' class='page no-display'>
        <div id='list-description'>
            <div class='text'></div>
            <div id='edit-description' class='icon-pencil fadeColor' onClick="javascript:editListAction();"></div>
        </div>
        <div id='add-item'>
            <div onClick='javascript:addItem();' class='fadeColor icon-plus' id='add-item-button'></div>
            <div id='add-item-resize'>
                <input class='fadeColor' type='text' placeholder='New Item...' id='add-item-input' />
            </div>
        </div>
        <div id='list-unchecked-panel'> </div>
        <div class='page-hr'></div>
        <div id='list-checked-panel'> </div>
    </div>
    <div id='settings' class='page no-display'>
        <h1 class='page-header'>List Settings</h1>
        <div class='page-label'>Name</div>
        <input class='page-input fadeColor' id='settings-name' type='text' value='' />
        <div class='page-label'>Description</div>
        <input class='page-input fadeColor' id='settings-description' type='text' value='' />
        <div class='page-notification' id='settings-edit-notification'></div>
        <div class='page-button-wrapper'>
            <div class='page-button blue fadeColor' id='settings-submit' onClick="javascript:updateListAction();">Update</div>
            <div class='page-button orange fadeColor' id='settings-cancel' onClick="javascript:resetListSettingsAction();">Reset</div>
        </div>
        <div class='page-hr'></div>
        <div class='page-label'>Shared With</div>
        <div id='shared-with-list' class='page-list'></div>
        <div class='page-notification' id='settings-share-notification'></div>
        <div class='page-button-wrapper'>
            <select class='page-input half-size fadeColor' id='share-select'></select>
            <div class='page-button blue fadeColor' id='share-button' onClick="javascript:shareListAction();">Share List</div>
        </div>
        <div class='page-hr'></div>
        <div class='page-label'>Other Actions</div>
        <div class='page-button-wrapper'>
            <div class='page-button orange fadeColor' id='settings-archive' onClick="javascript:archiveListAction();">Archive</div>
        </div>
    </div>
    <div id='user-profile' class='page no-display'>
        <h1 class='page-header'>User Profile & Friends</h1>
        <div class='page-label'>Name</div>
        <div class='page-button-wrapper'>
            <input class='page-input half-size fadeColor' type='text' id='profile-first-name' value='' />
            <input class='page-input half-size fadeColor' type='text' id='profile-last-name' value='' />
        </div>
        <div class='page-label'>Email</div>
        <input class='page-input fadeColor' type='text' id='profile-email' value='' />
        <div class='page-label'>Current Password</div>
        <input class='page-input fadeColor' type='password' id='profile-password' value='' />
        <div class='page-label'>New Password (Optional)</div>
        <div class='page-button-wrapper'>
            <input class='page-input half-size fadeColor' type='password' id='profile-password-new1' value='' />
            <input class='page-input half-size fadeColor' type='password' id='profile-password-new2' value='' />
        </div>
        <div class='page-notification' id='profile-update-notification'></div>
        <div class='page-button-wrapper'>
            <div class='page-button half-size blue fadeColor' id='profile-update' onClick="javascript:updateProfileAction();">Update</div>
            <div class='page-button half-size orange fadeColor' id='profile-reset' onClick="javascript:resetProfileInfoAction();">Reset</div>
        </div>
        <div class='page-hr'></div>
        <div class='page-label'>Friend Management</div>
        <div class='page-button-wrapper'>
            <input class='page-input half-size fadeColor' type='text' id='profile-friend-input' value='' placeholder='Username' />
            <div class='page-button blue fadeColor' id='profile-friend-button' onClick="javascript:addFriendAction();">Add Friend</div>
        </div>
        <div class='page-notification' id='profile-friend-notification'></div>
        <div id='profile-friends-pending-list-label' class='page-label hide'>Friend Requests to You</div>
        <div id='profile-friends-pending-list' class='page-list'></div>
        <div id='profile-friends-list-label' class='page-label hide'>Current Friends</div>
        <div id='profile-friends-list' class='page-list'></div>
        <div id='profile-friends-waiting-list-label' class='page-label hide'>Friend Requests from You</div>
        <div id='profile-friends-waiting-list' class='page-list'></div> 
    </div>
    <div id='list-management' class='page no-display'>
        <h1 class='page-header'>List Management</h1>
        <div class='page-notification' id='list-mgmt-notification'></div>
        <div id='list-mgmt-none-label' class='page-label hide'>No lists to manage...</div>
        <div id='list-mgmt-yours-list-label' class='page-label hide'>Your Lists</div>
        <div id='list-mgmt-yours-list' class='page-list'></div>
        <div id='list-mgmt-friends-list-label' class='page-label hide'>Friends' Lists</div>
        <div id='list-mgmt-friends-list' class='page-list'></div>
    </div>
    <div id='notifications' class='page no-display'>
        <h1 class='page-header'>Notifications</h1>
        <div class='page-notification' id='notification-notification'></div>
        <div id='notifications-none-label' class='page-label hide'>Nothing's new...</div>
        <div id='notifications-friends-label' class='page-label hide'>Friend Requests</div>
        <div id='notifications-friends-list' class='page-list'></div>
        <div id='notifications-shared-lists-label' class='page-label hide'>List Share Requests</div>
        <div id='notifications-shared-lists-list' class='page-list'></div>
        <div id='notifications-misc-label' class='page-label hide'>Other Notifications</div>
        <div id='notifications-misc-list' class='page-list'></div>
    </div>
</div>
<script src="/web/script/app.js<?php echo $devSuffix; ?>"></script>
<script src="/web/script/app/html.js<?php echo $devSuffix; ?>"></script>
<script src="/web/script/app/lists.js<?php echo $devSuffix; ?>"></script>
<script src="/web/script/app/list-settings.js<?php echo $devSuffix; ?>"></script>
<script src="/web/script/app/list-mgmt.js<?php echo $devSuffix; ?>"></script>
<script src="/web/script/app/items.js<?php echo $devSuffix; ?>"></script>
<script src="/web/script/app/profile.js<?php echo $devSuffix; ?>"></script>
<script src="/web/script/app/notifications.js<?php echo $devSuffix; ?>"></script>