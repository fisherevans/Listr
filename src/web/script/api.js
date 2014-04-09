function formToJson(formName) {
    var form = $("#" + formName);
    var ser = form.serializeArray()
    var json = {};
    for(var i in ser) {
        json[ser[i].name] = ser[i].value;
    }
    return json;
}

function forEachAssoc(obj, func) {
    for(var id in obj) {
        if (obj.hasOwnProperty(id))
            func(obj[id]);
    }
}

function callAPI(action, data, sucFunction, errFunction) {
	return callAPIAsync(action, false, data, sucFunction, errFunction);
}

function callAPIAsync(action, asynch, data, sucFunction, errFunction) {
    var json = JSON.stringify(data);
    action = "/api/" + action;
	info("API CALL: " + action);
	debug("Passing: " + json);
	var response = null;
	$.ajax({
        type: "POST",
        async: asynch,
        url: action,
        contentType: 'application/json',
        data: json,
        success: function (sucResponse) {
        	debug("Received: " + sucResponse);
        	sucFunction(jQuery.parseJSON(sucResponse));
        },
        error: function (errResponse) {
        	debug("Received: " + errResponse.responseText);
        	errFunction(jQuery.parseJSON(errResponse.responseText));
        }
    });
	return response;
}

function timeAgo(time){
    var units = [
        //{ name: "second", limit: 60, in_seconds: 1 },
        { name: "minute", limit: 3600, in_seconds: 60 },
        { name: "hour", limit: 86400, in_seconds: 3600  },
        { name: "day", limit: 604800, in_seconds: 86400 },
        { name: "week", limit: 2629743, in_seconds: 604800  },
        { name: "month", limit: 31556926, in_seconds: 2629743 },
        { name: "year", limit: null, in_seconds: 31556926 }
    ];
    var diff = (new Date() - time) / 1000;

    var i = 0;
    while (unit = units[i++]) {
        if (diff < unit.limit || !unit.limit) {
            var diff =  Math.ceil(diff / unit.in_seconds);
            return diff + " " + unit.name + (diff>1 ? "s" : "");
        }
    };
}