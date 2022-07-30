var SignIn = function() {};

SignIn.prototype.search_extern = function() {	
	var parameters = {
		"action": Client.SEARCH_EXTERN
	,	"txt_cardid": document.getElementById("txt_cardid").value
	};	
	var previous_parameters = location.href.substr(location.href.indexOf("?") + 1);
		
	cli.post(Client.API_SEARCH, parameters, function(data) {
		var rows = data.split("\r\n");
		var row = [];
		var subitem = rows[0].split(",");
		
		subitem.forEach(function(value) {
			var subvalue = value.replace(/\"/g, "");
			row.push(subvalue);
		});		
		
		var id_extern = Number(row[fd.id_extern]);
		var params = previous_parameters.split("&");
		var data = {};
		
		params.forEach(function(item) {
			subitem = item.split("=");
			data[subitem[0]] = subitem[1];
		});
			
		if (id_extern == Client.RESULT_FAILURE) {
			cli.go(Client.PAGE_SIGNUP, data);
		} else {
			data["id_extern"] = id_extern;				
			cli.go(Client.PAGE_CONFIRM, data);			
		}
	});		
};

SignIn.prototype.back = function() {
	location.href = "./";
};

window.onload = function() {
	var buttons = document.getElementsByTagName("button");	
	var btn_take = buttons[0];
	var btn_back = buttons[1];

	btn_take.onclick = function() {		
		ob_page.search_extern();
	};
		
	btn_back.onclick = function() {
		ob_page.back();
	};	
};

var ob_page = new SignIn();	
var cli = new Client();
var fd = new EnumFields(EnumPages.SignIn);