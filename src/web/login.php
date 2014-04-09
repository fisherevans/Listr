<div id='back'></div> 
<div id='login-window' class='login-box'>
    <h1>Listr</h1>
    <form id='login-form'>
        <div class='input-row'>
            <span class='input-label'>Username</span>
            <input class='input-text fadeColor' type='text' id='login-username' name='username' autofocus required />
        </div>
        <div class='input-row'>
            <span class='input-label'>Password</span>
            <input class='input-text fadeColor password' type='password' id='login-password' name='password' required />
        </div>
        <div class='input-row'>
            <input class='input-button fadeColor' id='login-submit' type='submit' onClick='login(); return false;' value='Login' data-value='Login' />
        </div>
        <div id='login-error' class='error'></div>
        <span class='link' onClick='javascript:switchWindow("register");'>Register</span>
    </form>
</div>

<div id='register-window' class='login-box'>
    <h1>Register</h1>
    <form id='register-form'>
        <div class='input-row'>
            <span class='input-label'>Username</span>
            <input class='input-text fadeColor' type='text' name='username' required />
        </div>
        <div class='input-row'>
            <span class='input-label'>Password</span>
            <input class='input-text fadeColor password' type='password' id='register-password' name='password' required />
        </div>
        <div class='input-row'>
            <span class='input-label'>Confirm</span>
            <input class='input-text fadeColor password' type='password' id='register-password_confirm' name='password_confirm' required />
        </div>
        <div class='input-row'>
            <span class='input-label'>Email</span>
            <input class='input-text fadeColor' type='text' id='register-email' name='email' required />
        </div>
        <div class='input-row'>
            <span class='input-label'>First Name</span>
            <input class='input-text fadeColor' type='text' id='register-first_name' name='first_name' required />
        </div>
        <div class='input-row'>
            <span class='input-label'>Last Name</span>
            <input class='input-text fadeColor' type='text' id='register-last_name' name='last_name' required />
        </div>
        <div class='input-row'>
            <input class='input-button fadeColor' id='register-submit' type='submit' onClick='register(); return false;' value='Register' data-value='Register' />
        </div>
        <div id='register-error' class='error'></div>
        <span class='link' onClick='javascript:switchWindow("login");'>Login</span>
    </form>
</div>

<div id='validate-window' class='login-box'>
    <h1>Validate</h1>
    <p>
        To complete the registration process please enter the validation code that was sent to your email.
    </p>
    <form id='validate-form'>
        <div class='input-row'>
            <span class='input-label'>Code</span>
            <input class='input-text fadeColor' type='text' id='validate-code' name='code' />
        </div>
        <div class='input-row'>
            <input class='input-button fadeColor' id='validate-submit' type='submit' onClick='validate(); return false;' value='Validate' data-value='Validate' />
        </div>
        <span class='link' onClick='javascript:switchWindow("login");'>Login</span>
    </form>
</div>

<script src="/web/script/login.js"></script>