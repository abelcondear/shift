var Confirm = function() {};

Confirm.prototype.UpdateUI = function() {
	var parameters = utl.toJsonParameters(location.href);
	parameters["action"] = Client.GET_SHIFT;	
	
	var callback = function(data) {
		var rows = data.split("\r\n");
		var row = [];
		var subitem = rows[0].split(",");
		
		subitem.forEach(function(value) {
			var subvalue = value.replace(/\"/g, "");
			row.push(subvalue);
		});
		
		var ul = document.getElementsByTagName("ul");
		var selectedIndex;
		
		for (var x = 0; x < ul.length; x ++) {
			if (ul[x].className == "data") {
				selectedIndex = x;
				break;
			}
		}
		
		var label = ul[selectedIndex].getElementsByTagName("label");
		
		label[0].innerHTML = "Consultorio: " + 
			((row[fd.gender_intern] == "M") ? "Dr. ":"Dra. ") + 
			row[fd.name_intern]	+ " " + row[fd.surname_intern];
		label[1].innerHTML = "Paciente: " + 
			((row[fd.gender_extern] == "M") ? "Sr. ":"Sra. ") +
			row[fd.name_extern] + " " + row[fd.surname_extern];
		label[2].innerHTML = utl.formatDate(row[fd.weekday]) + " " + 
			row[fd.time_start].substr(0, 5) + " Hs.";
		label[3].innerHTML = row[fd.speciality_name];
		label[4].innerHTML = row[fd.location];
		label[5].innerHTML = "$ " + row[fd.price] + ".00";		
	};
		
	cli.post(Client.API_SEARCH, parameters, callback);	
};

Confirm.prototype.confirm = function() {
	var parameters = utl.toJsonParameters(location.href);
	parameters["action"] = Client.CONFIRM_SHIFT;
	
	var callback = function(data) {
		var rows = data.split("\r\n");
		var row = [];
		var subitem = rows[0].split(",");
		
		subitem.forEach(function(value) {
			var subvalue = value.replace(/\"/g, "");
			row.push(subvalue);
		});
		
		if (
			row[fd.result_inserted] == Client.RESULT_SUCCESS &&
			row[fd.result_attendee] == Client.RESULT_SUCCESS &&
			row[fd.result_organizer] == Client.RESULT_SUCCESS &&
			row[fd.result_administrator] == Client.RESULT_SUCCESS
		) 
		{
			cli.go(Client.PAGE_DONE);
			
		} else {
			utl.messageBox(
				"Lo siento, pero el turno no ha podido ser confirmado.\n" + 
				"Vuelva a intentar más tarde."
			);
		}
	};
		
	cli.post(Client.API_CONFIRM, parameters, callback);
};

Confirm.prototype.back = function() {
	location.href = "./";
};

window.onload = function(){
	var buttons = document.getElementsByTagName("button");
	var btn_confirm = buttons[0];
	var btn_back = buttons[1];
	
	ob_page.UpdateUI();
	
	btn_confirm.onclick = function() {
		ob_page.confirm();
	};
		
	btn_back.onclick = function() {
		ob_page.back();
	};	
};

var ob_page = new Confirm();
var utl = new Utils();
var cli = new Client();
var fd = new EnumFields(EnumPages.Confirm);