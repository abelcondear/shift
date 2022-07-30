var Cancel = function() {};

Cancel.prototype.confirm = function() {
	//TODO
};

window.onload = function(){
	var buttons = document.getElementsByTagName("button");
	var btn_confirm = buttons[0];

	btn_confirm.onclick = function() {
		ob_page.confirm();
	};	
};

var ob_page = new Cancel();
var utl = new Utils();
var cli = new Client();
var fd = new EnumFields(EnumPages.Cancel);
