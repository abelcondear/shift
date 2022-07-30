var SignUp = function() {};

SignUp.prototype.save = function() {				
	var inputs = document.getElementsByTagName("input");
	var parameters = {
		"action": Client.SAVE_EXTERN
	,	"opt_gender": utl.getRadioButtonValue("opt_gender")
	};
	
	var item;	
	for (var x = 0; x < inputs.length; x ++) {
		item = inputs[x];
		if (item.type == "text") {
			parameters[item.id] = item.value;
		}
	}
	
	var callback = function(data) {
		var rows = data.split("\r\n");
		var row = [];
		var subitem = rows[0].split(",");
		
		subitem.forEach(function(value) {
			var subvalue = value.replace(/\"/g, "");
			row.push(subvalue);
		});
		
		if (Number(row[fd.id_extern])) {
			utl.messageBox(
				"Los datos han sido grabados exitosamente.\n" + 
				"Gracias."
			);
			var parameters = utl.toJsonParameters(location.href);
			parameters["id_extern"] = row[fd.id_extern];
			cli.go(Client.PAGE_CONFIRM, parameters);			
		} else {
			utl.messageBox(
				"Ha ocurrido un error al grabar los datos.\n" +
				"Vuelva a intentar más tarde."
			);
		}
	};

	cli.post(Client.API_SAVE, parameters, callback);
};

SignUp.prototype.back = function() {
	location.href = "./";
};

window.onload = function(){		
	var buttons = document.getElementsByTagName("button");
	var btn_save = buttons[0];
	var btn_back = buttons[1];
	
	btn_save.onclick = function() {
		ob_page.save();
	};
	
	btn_back.onclick = function() {
		ob_page.back();
	};
};

var ob_page = new SignUp();
var utl = new Utils();
var cli = new Client();
var fd = new EnumFields(EnumPages.SignUp);