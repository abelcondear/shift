var List = function() { };

List.prototype.initialUpdate = function(nameList) {
	var ob_this = this;
	var request = new AjaxRequest();
	
	request.create();
	ob_this.collectData = {};
	
	request.post(
		AjaxRequest.POST_METHOD
	,	"./api/search/"
	,	"name=" + nameList + "&current_page=1"
	,	function(data) {
			var list = document.getElementById("list");
			var ol = list.getElementsByTagName("ol")[0];
			var ul = null, li = null;
		
			var rows = data.split("\r\n");
			var numline = 0;
			
			ob_this.collectData.rows = [];	
				
			rows.forEach(function(item) {
				numline ++;

				if (numline == 1) {
					ob_this.collectData.pageName = item;
				}
								
				if (numline == 2) {
					ob_this.collectData.currentPage = new Number(item);
				}
				
				if (numline == 3) {
					ob_this.collectData.maxPage = new Number(item);
				}
				
				if (numline > 3 && item.length) {
					var subitem = item.split(",");
					var row = [];
					
					subitem.forEach(function(value) {
						var subvalue = value.replace(/\"/g, "");
						row.push(subvalue);
					});
					
					ob_this.collectData.rows.push(row);
				}
			});
			
			console.log(ob_this.collectData);
					
			ob_this.collectData.rows.forEach(function(row) {
				ul = document.createElement("ul");
				
				row.forEach(function(value, index) {
					if (index != 0) {
						li = document.createElement("li");		
						li.innerText = value;
						ul.appendChild(li);		
					}
				});
				
				ol.appendChild(ul);
			});					
		}
	);			
};
