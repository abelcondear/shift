var Index = function() {};

Index.prototype.confirm= function(form_data) {
	cli.go(Client.PAGE_SIGNIN, form_data);
};

Index.prototype.search= function(form_data) {
	var ob_this = this;

	cli.post(Client.API_SEARCH, form_data, function(data) {
		var title = document.getElementsByClassName("title");
		var noperson = document.getElementsByClassName("no-person");
		
		var person = document.getElementsByClassName("person");
		var ol = person[0].getElementsByTagName("ol");
		
		for (var x = 0; x < ol.length; x ++) {
			person[0].removeChild(ol[x]);
		}
		
		var index = new Index();		
		var rows = data.split("\r\n");
		var dataset = [];
		
		rows.forEach(function(item) {
			if (!item.length) return;
			
			var subitem = item.split(",");
			var row = [];
			
			subitem.forEach(function(value) {
				var subvalue = value.replace(/\"/g, "");
				row.push(subvalue);
			});
						
			dataset.push(row);			
		});
			
		var ol, ul, li, img, label;
		
		if (dataset.length) {
			console.log('len[if]'+dataset.length);
			title[0].className = title[0].className.replace(/hidden/g, "");
			person[0].className = person[0].className.replace(/hidden/g, "");
			
			if (noperson[0].className.indexOf("hidden") == -1) {
				noperson[0].className += " hidden";
			}			
			
			for (var x = 0; x < dataset.length - 1; x ++) {
				ol = document.createElement("ol");
				
				ul = document.createElement("ul");
				ul.className = "photo";
				li = document.createElement("li");
				img = document.createElement("img");
				img.className = (dataset[x][fd.gender] == 'M') ? "man":"woman";
				li.appendChild(img);
				ul.appendChild(li);
				
				ol.appendChild(ul);
				
				ul = document.createElement("ul");
				ul.className = "data";
				ul.onclick = ob_this.confirm.bind(null, {
					"id_intern": dataset[x][fd.id_intern]
				,	"weekday": dataset[x][fd.weekday]
				,	"time_start": dataset[x][fd.time_start]
				});
				li = document.createElement("li");		
				label = document.createElement("label");
				label.innerHTML = dataset[x][fd.professional_gender] + " " + dataset[x][fd.name] + 
				" " + dataset[x][fd.surname];
				li.appendChild(label);		
				ul.appendChild(li);		
				li = document.createElement("li");
				label = document.createElement("label");
				label.innerHTML = dataset[x][fd.speciality_name];
				li.appendChild(label);		
				ul.appendChild(li);
				li = document.createElement("li");
				label = document.createElement("label");
				label.innerHTML = utl.formatDate(dataset[x][fd.weekday]) + " " + dataset[x][fd.time_start].substr(0, 5) + " Hs.";
				li.appendChild(label);		
				ul.appendChild(li);		
				li = document.createElement("li");
				label = document.createElement("label");
				label.innerHTML = dataset[x][fd.location];
				li.appendChild(label);		
				ul.appendChild(li);
				li = document.createElement("li");
				label = document.createElement("label");
				label.innerHTML = "$ " + dataset[x][fd.price] + ".00";
				li.appendChild(label);		
				ul.appendChild(li);
				
				ol.appendChild(ul);
				
				ul = document.createElement("ul");
				ul.className = "button";
				li = document.createElement("li");
				img = document.createElement("img");
				img.src = "image/gray_arrow.png";		
				li.appendChild(img);
				ul.appendChild(li);
				
				ol.appendChild(ul);
				person[0].appendChild(ol);
			}
		} else {
			console.log('len[else]:'+dataset.length);
			if (title[0].className.indexOf("hidden") == -1) {
				title[0].className += " hidden";
			}
						
			if (person[0].className.indexOf("hidden") == -1) {
				person[0].className += " hidden";
			}
									
			noperson[0].className = noperson[0].className.replace(/hidden/g, "");
		}	
	});	
};

Index.prototype.InitializeControls = function() {
	document.getElementById("txt_date").value = default_date;
	document.getElementById("txt_date").className = default_classname;
};

Index.prototype.ControlFocus = function() {
	document.getElementById("txt_date").onfocus = function(){
		if (this.value.length == 0 || this.value == default_date) {
			this.value = "";
			this.className = "";
		}	
	};	
};

Index.prototype.ControlBlur = function() {	
	document.getElementById("txt_date").onblur = function(){
		if (this.value.length == 0 || this.value == default_date) {
			this.value = default_date;
			this.className = default_classname;
		}
	};	
};

window.onload = function() {		
	var buttons = document.getElementsByTagName("button");
	var btn_search = buttons[0];

	ob_page.InitializeControls();
	ob_page.ControlFocus();
	ob_page.ControlBlur();
	
	btn_search.onclick = function() {		
		var cmb_professional = document.getElementById("cmb_professional");
		var cmb_range_a = document.getElementById("cmb_range_a");
		var cmb_range_b = document.getElementById("cmb_range_b");
		var opt_dayname = utl.getRadioButtonValue("opt_dayname");
		var txt_date = document.getElementById("txt_date");		

		var parameters = {
			"action": Client.SEARCH_SHIFT
		,	"cmb_professional": (!cmb_professional.selectedIndex) ? 
				"":cmb_professional.options[cmb_professional.selectedIndex].text
		,	"cmb_range_a": (cmb_range_a.selectedIndex == cmb_range_b.selectedIndex) ? 
				"":cmb_range_a.options[cmb_range_a.selectedIndex].text
		,	"cmb_range_b": (cmb_range_a.selectedIndex == cmb_range_b.selectedIndex) ? 
				"":cmb_range_b.options[cmb_range_b.selectedIndex].text
		,	"opt_dayname": utl.getRadioButtonValue("opt_dayname")
		,	"txt_date": (txt_date.value == default_date) ? "":txt_date.value
		};	
			
		ob_page.search(parameters);
	};
};

var ob_page = new Index();
var utl = new Utils();
var cli = new Client();
var fd = new EnumFields(EnumPages.Index);

var default_date = "12/04/2020";
var default_classname = "default-value";