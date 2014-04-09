var fadeTime = 500;
var registerUsername = "";
var registerPassword = "";

$(document).ready(function() {
    if($.cookie("loginError") != undefined) {
        showError("login", $.cookie("loginError"));
        $.removeCookie("loginError");
    }
    $('#login-window').fadeIn(fadeTime);
    $(window).resize(function() {
        if($(window).width() <= 425)
            $("#back").addClass("dark");
        else
            $("#back").removeClass("dark");
    });
});

function switchWindow(windowName) {
    $('.login-box').fadeOut(fadeTime);
    $('#' + windowName + '-window').delay(fadeTime).queue(function() {
        $(this).fadeIn(fadeTime);
        $(this).dequeue();
    });
}

function showError(form, msg) {
    $("#" + form + "-password").select();
    $("#" + form + "-error").fadeOut(fadeTime);
    $("#" + form + "-error").queue(function() {
        $(this).html(msg);
        $(this).dequeue();
    });
    $("#" + form + "-error").fadeIn(fadeTime);
}

function login() {
    startLoading("login");
    var data = formToJson("login-form"); 
    callAPI("users/login", data,
        function(response) {
            stopLoading("login");
            var destination = $.cookie("destination");
            if(destination == "" || destination === undefined)
                destination = "/";
            $.removeCookie("destination");
            leave(destination);
        },
        function(response) {
            stopLoading("login");
            showError("login", response.response);
        }
    );
}

function register() {
    startLoading("register");
    var data = formToJson("register-form");
    var errors = "";
    if(!isValidUsername(data.username))
        errors += "Usernames must be alphanumeric and between 2 and 24 characters long.<br>";
    if(!isValidPassword(data.password))
        errors += "Passwords must be at least 6 characters long.<br>";
    if(data.password_confirm != data.password)
        errors += "Passwords do not match.<br>";
    if(!isValidEmail(data.email))
        errors += "Please use a valid email.<br>";
    if(!isValidName(data.first_name) || !isValidName(data.last_name))
        errors += "Names may only use letters, spaces and periods.<br>";
        
    if(errors.length > 0) {
        showError("register", errors);
        stopLoading("register");
    } else {
        registerUsername = data.username;
        registerPassword = data.password;
        callAPI("users/register", data,
            function(response) {
                debug("registered!");
                stopLoading("register");
                switchWindow("validate");
            },
            function(response) {
                stopLoading("register");
                showError("register", response.response);
            }
        );
    }
}

function validate() {
    startLoading("validate");
    var data = formToJson("validate-form");
    data.username = registerUsername;
    data.password = registerPassword;
    callAPI("users/validate", data,
        function(response) {
            debug("validated!");
            stopLoading("validate");
            loginAfterValidate(data);
        },
        function(response) {
            stopLoading("validate");
            showError("validate", response.response);
        }
    );
}

function loginAfterValidate(data) {
    callAPI("users/login", data,
        function(response) {
            leave("/");
        },
        function(response) {
            showError("validate", response.response);
        }
    );
}

function leave(dest) {
    $('.login-box').fadeOut(fadeTime);
    $('html').delay(fadeTime).queue(function() {
        window.location = dest;
        $(this).dequeue();
    });
}

function startLoading(windowName) {
    var name = "#" + windowName + "-submit";
    debug("startLoading " + name);
    $(name).addClass('loading');
    $(name).val('Loading...');
    $(name).attr('disabled','disabled');
}

function stopLoading(windowName) {
    var name = "#" + windowName + "-submit";
    debug("stopLoading " + name);
    $(name).removeClass('loading');
    $(name).val($(name).data('value'));
    $(name).removeAttr('disabled');
}

function isValidUsername(username) {
    return username.search(/^[a-zA-Z0-9]+$/) >= 0 && username.length >= 2 && username.length <= 24;
}

function isValidName(name) {
    return name.search(/^[a-zA-Z \.-]+$/) >= 0 && name.length >= 1 && name.length <= 45;
}

function isValidEmail(email) {
    return email.search(/^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/) >= 0;
}

function isValidPassword(password) {
    return password.length >= 6 && password.length <= 255;
}

