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
        <img id='profile-arrow' src='/web/img/arrow.png'></img>
        <div id='profile-dropdown' class='fadeHeight'>
            <div id='profile-dropdown-wrapper'>
                <div onClick='javascript:gotoProfile();' class='profile-dropdown-link fadeColor'>Profile & Friends</div>
                <div onClick='javascript:gotoListManagement();' class='profile-dropdown-link fadeColor'>List Management</div>
                <div onClick='javascript:logout();' class='profile-dropdown-link fadeColor'>Logout</div>
            </div>
        </div>
    </div>
    <div onClick='javascript:gotoNotifications()' id='notifications' class='fadeColor new'>3</div>
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
            <div id='edit-description' class='icon-pencil fadeColor'></div>
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
            <div class='page-button blue fadeColor' id='settings-submit'>Update</div>
            <div class='page-button orange fadeColor' id='settings-cancel'>Reset</div>
        </div>
        <div class='page-hr'></div>
        <div class='page-label'>Shared With</div>
        <div class='page-button-wrapper'>
            <select class='page-input half-size fadeColor' id='share-select'>
                <option>uvm6393</option>
                <option>testUSER432</option>
                <option>xXXtrikXXx</option>
                <option>FortFive5</option>
            </select>
            <div class='page-button blue fadeColor' id='share-button'>Share List</div>
        </div>
        <div id='friends-wrapper'>
            <div class='friend-row fadeColor'>
                <div class='friend-name'>uvm6393</div>
                <div class='friend-status'>(Request Pending)</div>
                <div class='friend-actions'>
                    <div class='friend-unshare fadeColor'>Revoke</div>
                </div>
            </div>
            <div class='friend-row fadeColor'>
                <div class='friend-name'>prplduckie</div>
                <div class='friend-actions'>
                    <div class='friend-unshare fadeColor'>Unshare</div>
                </div>
            </div>
        </div>
        <div class='page-hr'></div>
        <div class='page-label'>Other Actions</div>
        <div class='page-button-wrapper'>
            <select class='page-input half-size fadeColor' id='give-select'>
                <option>uvm6393</option>
                <option>testUSER432</option>
                <option>xXXtrikXXx</option>
                <option>FortFive5</option>
            </select>
            <div class='page-button redfadeColor' id='settings-give-button'>Give List</div>
        </div>
        <div class='page-button-wrapper'>
            <div class='page-button orange fadeColor' id='settings-archive'>Archive</div>
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
        <input class='page-input fadeColor' type='text' id='profile-password' value='' />
        <div class='page-label'>New Password</div>
        <div class='page-button-wrapper'>
            <input class='page-input half-size fadeColor' type='text' id='profile-password-new1' value='' />
            <input class='page-input half-size fadeColor' type='text' id='profile-password-new2' value='' />
        </div>
        <div class='page-notification' id='profile-update-notification'></div>
        <div class='page-button-wrapper'>
            <div class='page-button half-size blue fadeColor' id='profile-update'>Update</div>
            <div class='page-button half-size orange fadeColor' id='profile-reset'>Reset</div>
        </div>
        <div class='page-hr'></div>
        <div class='page-label'>Friend Management</div>
        <div class='page-button-wrapper'>
            <input class='page-input half-size fadeColor' type='text' id='profile-friend-input' value='' />
            <div class='page-button blue fadeColor' id='profile-friend-button'>Add Friend</div>
        </div>
        <div class='page-label'>Friend Requests to You</div>
        <div id='profile-friends-request-list' class='page-list'>
            <div class='page-list-row fadeColor'>
                <div class='page-list-row-label'>SharpShooter123</div>
                <div class='page-list-row-note'>(Action Required)</div>
                <div class='page-list-row-actions'>
                    <div class='page-list-row-action fadeColor'>Accept</div>
                    <div class='page-list-row-action fadeColor'>Decline</div>
                </div>
            </div>
        </div>
        <div class='page-label'>Current Friends</div>
        <div id='profile-friends-list' class='page-list'>
            <div class='page-list-row fadeColor'>
                <div class='page-list-row-label'>uvm6393</div>
                <div class='page-list-row-actions'>
                    <div class='page-list-row-action red fadeColor'>Unfriend</div>
                </div>
            </div>
            <div class='page-list-row fadeColor'>
                <div class='page-list-row-label'>prplduckie</div>
                <div class='page-list-row-actions'>
                    <div class='page-list-row-action red fadeColor'>Unfriend</div>
                </div>
            </div>
        </div>
        <div class='page-label'>Friend Requests from You</div>
        <div id='profile-friends-pending-list' class='page-list'>
            <div class='page-list-row fadeColor'>
                <div class='page-list-row-label'>LISTguyR</div>
                <div class='page-list-row-note'>(Request Pending)</div>
                <div class='page-list-row-actions'>
                    <div class='page-list-row-action red fadeColor'>Revoke</div>
                </div>
            </div>
            <div class='page-list-row fadeColor'>
                <div class='page-list-row-label'>some123otherXXX</div>
                <div class='page-list-row-note'>(Request Pending)</div>
                <div class='page-list-row-actions'>
                    <div class='page-list-row-action red fadeColor'>Revoke</div>
                </div>
            </div>
        </div>
    </div>
    <div id='list-management' class='page no-display'>
        <h1 class='page-header'>List Management</h1>
        <div class='page-label'>Active Lists You Own</div>
        <div class='page-list'>
            <div class='page-list-row fadeColor'>
                <div class='page-list-row-label'>List Name 1</div>
                <div class='page-list-row-actions'>
                    <div class='page-list-row-action blue fadeColor'>Settings</div>
                    <div class='page-list-row-action red fadeColor'>Archive</div>
                </div>
            </div>
            <div class='page-list-row fadeColor'>
                <div class='page-list-row-label'>List Name 2</div>
                <div class='page-list-row-actions'>
                    <div class='page-list-row-action blue fadeColor'>Settings</div>
                    <div class='page-list-row-action red fadeColor'>Archive</div>
                </div>
            </div>
        </div>
        <div class='page-label'>Archived Lists You Own</div>
        <div class='page-list'> 
        </div>
        <div class='page-label'>Lists Shared With You</div>
        <div class='page-list'>
            <div class='page-list-row fadeColor'>
                <div class='page-list-row-label'>List Name 4</div>
                <div class='page-list-row-actions'>
                    <div class='page-list-row-action orange fadeColor'>Ignore</div>
                </div>
            </div>
            <div class='page-list-row fadeColor'>
                <div class='page-list-row-label'>List Name 5</div>
                <div class='page-list-row-actions'>
                    <div class='page-list-row-action orange fadeColor'>Ignore</div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/web/script/app.js<?php echo $devSuffix; ?>"></script>
<script src="/web/script/app/html.js<?php echo $devSuffix; ?>"></script>
<script src="/web/script/app/lists.js<?php echo $devSuffix; ?>"></script>
<script src="/web/script/app/list-settings.js<?php echo $devSuffix; ?>"></script>
<script src="/web/script/app/items.js<?php echo $devSuffix; ?>"></script>
<script src="/web/script/app/profile.js<?php echo $devSuffix; ?>"></script>