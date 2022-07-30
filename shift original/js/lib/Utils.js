var Utils = function() { };

Utils.prototype.getRadioButtonValue = function(name) {
	var items = document.getElementsByTagName("input");
	var selected = "";
	
	for (var x = 0; x < items.length; x ++) {
		if (items[x].name == name) {
			if (items[x].checked) {
				selected = items[x].value;
				break;
			}
		}
	}
	
	return selected;
};

Utils.prototype.toJsonParameters = function(url) {
	var url_params = url.substr(url.indexOf("?") + 1);
	var params = url_params.split("&");
	var data = {};
	
	params.forEach(function(item) {
		subitem = item.split("=");
		data[subitem[0]] = subitem[1];
	});

	return data;
};

Utils.prototype.formatDate = function(date) {
	var d = date.split("-");
	return d[2] + "-" + d[1] + "-" + d[0];
};

Utils.prototype.messageBox = function(message) {
	window.alert(message);
};
