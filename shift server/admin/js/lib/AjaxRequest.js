var AjaxRequest = function() { };

AjaxRequest.POST_METHOD = "POST";
AjaxRequest.GET_METHOD = "GET";

AjaxRequest.prototype.create = function() {
	var ob_this = this;
	
	if (window.XMLHttpRequest) {
		//code for modern browsers
		ob_this.xhttp = new XMLHttpRequest();
	 } else {
		//code for old Internet Explorer browsers
		ob_this.xhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
};

AjaxRequest.prototype.post = function(method, url, parameters, callback) {
	var ob_this = this;
	
	ob_this.xhttp.open(method, url, true);
	ob_this.xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	ob_this.xhttp.send(parameters);
	
	ob_this.xhttp.onreadystatechange = function() {
		if (ob_this.xhttp.readyState == 4 && ob_this.xhttp.status == 200) {
			callback(ob_this.xhttp.responseText);
		}	
	}	
};
