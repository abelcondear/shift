var Client = function() { };

Client.API_URL = {
	"save": "./api/save/"
,	"confirm": "./api/confirm/"
,	"search": "./api/search/"
,	"attach": "./api/attach/"
,	"cancel": "./api/cancel/"
};

Client.PAGE_URL = {
	"index": "./"
,	"signin": "./signin.html"
,	"signup": "./signup.html"
,	"confirm": "./confirm.html"
,	"done": "./done.html"
,	"cancel": "./cancel.html"
,	"canceldone": "./canceldone.html"
};

Client.API_SAVE = "save";
Client.API_CONFIRM = "confirm";
Client.API_SEARCH = "search";
Client.API_ATTACH = "attach";
Client.API_CANCEL = "cancel";

Client.PAGE_INDEX = "index";
Client.PAGE_SIGNIN = "signin";
Client.PAGE_SIGNUP = "signup";
Client.PAGE_CONFIRM = "confirm";
Client.PAGE_DONE = "done";
Client.PAGE_CANCEL = "cancel";
Client.PAGE_CANCEL_DONE = "canceldone";

Client.SEARCH_SHIFT = "search:shift";
Client.SEARCH_EXTERN = "search:extern";
Client.SAVE_EXTERN = "save:extern";
Client.GET_SHIFT = "get:shift";
Client.CONFIRM_SHIFT = "confirm:shift";
Client.CANCEL_SHIFT = "cancel:shift";

Client.RESULT_OK = "0";
Client.RESULT_NOT_OK = "-1";

Client.RESULT_SUCCESS = "1";
Client.RESULT_FAILURE = "-1";

Client.prototype.post = function(api_name, parameters, callback) {
	var ob_this = this;
	var request = new AjaxRequest();
	var key = null;
	var parameters_encoded = new Array();
	
	request.create();
		
	for (key in parameters) {
			parameters_encoded.push(
				key + "=" + window.encodeURI(String(parameters[key]))
			);			
	}
	
	request.post(
		AjaxRequest.POST_METHOD
	,	Client.API_URL[api_name] + "#" + Math.random(5)
	,	parameters_encoded.join("&")
	,	callback
	);
};

Client.prototype.go = function(url_name, parameters) {
	var ob_this = this;
	var parameters_encoded = new Array();
	var key = null;

	for (key in parameters) {
		parameters_encoded.push(
			key + "=" + window.encodeURI(String(parameters[key]))
		);			
	}

	if (parameters_encoded.length) {
		location.href = Client.PAGE_URL[url_name] + "?" + parameters_encoded.join("&");	
	} else {
		location.href = Client.PAGE_URL[url_name];
	}
}
