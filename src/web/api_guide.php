<div id='api-wrapper'>
    <h1>API Methods</h1>
    <br>
    
    
    <h2>User Management</h2>
    <div class='api-command'>
        <h4>/api/user/login</h4>
        <p>Creates a web-session if valid credentials are given.</p>
        <p class='api-args'><b>Takes:</b> username, password</p>
    </div>
    <div class='api-command'>
        <h4>/api/user/ping</h4>
        <p>Keeps a session active and prevents it from timing out.</p>
    </div>
    <div class='api-command'>
        <h4>/api/user/logout</h4>
        <p>Destroys a web-session and the internal sessionID of the current session.</p>
    </div>
    <div class='api-command'>
        <h4>/api/user/register</h4>
        <p>Register's a new user.</p>
        <p class='api-args'><b>Takes:</b> username, password, email, first_name, last_name</p>
    </div>
    <div class='api-command'>
        <h4>/api/user/validate</h4>
        <p>Validates a new user based on their emailed code.</p>
        <p class='api-args'><b>Takes:</b> username, validation_code</p>
    </div>
    <div class='api-command'>
        <h4>/api/user/friend</h4>
        <p>Sends a friend request.</p>
        <p class='api-args'><b>Takes:</b> friended</p>
    </div>
    <div class='api-command'>
        <h4>/api/user/confirmFriend</h4>
        <p>Sends a friend request.</p>
        <p class='api-args'><b>Takes:</b> friender</p>
    </div>
    <div class='api-command'>
        <h4>/api/user/unfriend</h4>
        <p>Sends a friend request.</p>
        <p class='api-args'><b>Takes:</b> friend</p>
    </div>
    <div class='api-command'>
        <h4>/api/user/self</h4>
        <p>Gets information about ones self.</p>
    </div>
    
    
    <h2>List Management</h2>
    <div class='api-command'>
        <h4>/api/list/add</h4>
        <p>Creates a new list with the current user as the owner.</p>
        <p class='api-args'><b>Takes:</b> name, description</p>
    </div>
    <div class='api-command'>
        <h4>/api/list/edit</h4>
        <p>Updates a list's name or description</p>
        <p class='api-args'><b>Takes:</b> list_id, name, description</p>
    </div>
    <div class='api-command'>
        <h4>/api/list/archive</h4>
        <p>Archives a list.</p>
        <p class='api-args'><b>Takes:</b> list_id</p>
    </div>
    <div class='api-command'>
        <h4>/api/list/restore</h4>
        <p>Restores a list from an archived state.</p>
        <p class='api-args'><b>Takes:</b> list_id</p>
    </div>
    <div class='api-command'>
        <h4>/api/list/favorite</h4>
        <p>Favorites a list</p>
        <p class='api-args'><b>Takes:</b> list_id</p>
    </div>
    <div class='api-command'>
        <h4>/api/list/unfavorite</h4>
        <p>Un-favorites a list</p>
        <p class='api-args'><b>Takes:</b> list_id</p>
    </div>
    <div class='api-command'>
        <h4>/api/list/remove</h4>
        <p>Removes a list.</p>
        <p class='api-args'><b>Takes:</b> list_id</p>
    </div>
    <div class='api-command'>
        <h4>/api/list/give</h4>
        <p>Gives owner ship of a list to another user. Makes the current user a sharee.</p>
        <p class='api-args'><b>Takes:</b> list_id, username</p>
    </div>
    <div class='api-command'>
        <h4>/api/list/share</h4>
        <p>Shares the list with another user.</p>
        <p class='api-args'><b>Takes:</b> list_id, username</p>
    </div>
    <div class='api-command'>
        <h4>/api/list/unshare</h4>
        <p>Unshares a list with a current sharee.</p>
        <p class='api-args'><b>Takes:</b> list_id, username</p>
    </div>
    <div class='api-command'>
        <h4>/api/list/get</h4>
        <p>Returns information about a list. Must be list owner, or be a shree.</p>
        <p class='api-args'><b>Takes:</b> list_id</p>
    </div>
    <div class='api-command'>
        <h4>/api/list/getAll</h4>
        <p>Returns all lists the current user owns or is a sharee of that are not archived.</p>
    </div>
    <div class='api-command'>
        <h4>/api/list/getAllArchived</h4>
        <p>Returns all lists the current user owns that is archived.</p>
    </div>
    <div class='api-command'>
        <h4>/api/list/getAllUnArchived</h4>
        <p>Returns all lists the current user owns that is archived.</p>
    </div>
    
    
    <h2>Item Management</h2>
    <div class='api-command'>
        <h4>/api/list/item/add</h4>
        <p>Adds and item to a list.</p>
        <p class='api-args'><b>Takes:</b> list_id, name</p>
    </div>
    <div class='api-command'>
        <h4>/api/list/item/check</h4>
        <p>Sets item's state to checked.</p>
        <p class='api-args'><b>Takes:</b> item_id</p>
    </div>
    <div class='api-command'>
        <h4>/api/list/item/uncheck</h4>
        <p>Sets item's state to un-checked.</p>
        <p class='api-args'><b>Takes:</b> item_id</p>
    </div>
    <div class='api-command'>
        <h4>/api/list/item/archive</h4>
        <p>Archives and item.</p>
        <p class='api-args'><b>Takes:</b> item_id</p>
    </div>
    <div class='api-command'>
        <h4>/api/list/item/restore</h4>
        <p>Restores an item.</p>
        <p class='api-args'><b>Takes:</b> item_id</p>
    </div>
    <div class='api-command'>
        <h4>/api/list/item/remove</h4>
        <p>Removes an item from the list.</p>
        <p class='api-args'><b>Takes:</b> item_id</p>
    </div>
    <div class='api-command'>
        <h4>/api/list/item/get</h4>
        <p>Gets an item's name, list and states.</p>
        <p class='api-args'><b>Takes:</b> item_id</p>
    </div>
    <div class='api-command'>
        <h4>/api/list/item/getAll</h4>
        <p>Gets all items.</p>
        <p class='api-args'><b>Takes:</b> list_id</p>
    </div>
    <div class='api-command'>
        <h4>/api/list/item/getAllCheckState</h4>
        <p>Gets all items in a given state.</p>
        <p class='api-args'><b>Takes:</b> list_id, checked</p>
    </div>
    <div class='api-command'>
        <h4>/api/list/item/getAllArchiveState</h4>
        <p>Gets all items in a given state.</p>
        <p class='api-args'><b>Takes:</b> list_id, archived</p>
    </div>
</div>