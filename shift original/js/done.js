var Done = function() {};

Done.prototype.back = function() {
	location.href = "./";
};

window.onload = function(){
	var buttons = document.getElementsByTagName("button");
	var btn_back = buttons[0];

	btn_back.onclick = function() {
		ob_page.back();
	};	
};

var ob_page = new Done();
