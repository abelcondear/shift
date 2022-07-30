var EnumFields = function(page_name) {
	var ob_this = this;
	
	if (page_name == EnumPages.Index) {
		ob_this.id_intern = 0;
		ob_this.name = 1;
		ob_this.surname = 2;
		ob_this.gender = 3;
		ob_this.professional_gender = 4;
		ob_this.price = 5;
		ob_this.location = 6;
		ob_this.speciality_name = 7;
		ob_this.weekday = 8;
		ob_this.time_start = 9;
	}

	if (page_name == EnumPages.SignIn) {
		ob_this.id_extern = 0;
	}	
	
	if (page_name == EnumPages.SignUp) {
		ob_this.id_extern = 0;
	}
	
	if (page_name == EnumPages.Confirm) {
		ob_this.id_intern = 0;
		ob_this.name_intern = 1;
		ob_this.surname_intern = 2;
		ob_this.gender_intern = 3;
		ob_this.email_intern = 4;
		ob_this.price = 5;
		ob_this.location = 6;
		ob_this.speciality_name = 7;
		ob_this.weekday = 8;
		ob_this.time_start = 9;
		ob_this.id_extern = 10;
		ob_this.name_extern = 11;
		ob_this.surname_extern = 12;
		ob_this.gender_extern = 13;
		ob_this.email_extern = 14;
		
		ob_this.result_inserted = 0;
		ob_this.result_attendee = 1;
		ob_this.result_organizer = 2;
		ob_this.result_administrator = 3;
	}
	
	if (page_name == EnumPages.Done) {
		//NULL
	}
	
	if (page_name == EnumPages.Cancel) {
		//NULL
	}

	if (page_name == EnumPages.CancelDone) {
		//NULL
	}	
};

var EnumPages = {};
EnumPages.Index = "index";
EnumPages.SignIn = "signin";
EnumPages.SignUp = "signup";
EnumPages.Confirm = "confirm";
EnumPages.Done = "done";
EnumPages.Cancel = "cancel";
EnumPages.CancelDone = "canceldone";

