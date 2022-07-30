<?php
namespace Enumeration;

class Environment {
	const development = "development";
	const preproduction = "pre-production";
	const production = "production";
}

class Action {
	const search_extern = "search:extern";
	const search_shift = "search:shift";
	const get_shift = "get:shift";
	const save_extern = "save:extern";
	const confirm_shift = "confirm:shift";
	const cancel_shift = "cancel:shift";
}

class Page {
	const admin_list_intern = "list_intern";
	const admin_list_extern = "list_extern";
	const admin_list_speciality = "list_speciality";
	const admin_list_usertype = "list_usertype";
	const admin_list_shift_intern = "list_shift_intern";
	const admin_list_shift_extern = "list_shift_extern";	
}

class Path {
	const app_name = "shift/";
	
	const app_core_class = "shift/core/class/";
	const app_core_template_ics = "shift/core/template/ics/";
	const app_core_template_mail = "shift/core/template/mail/";
	
	const app_user_api_attach = "shift/api/attach/";
	const app_user_api_confirm = "shift/api/confirm/";
	const app_user_api_save = "shift/api/save/";
	const app_user_api_search = "shift/api/search/";
	const app_user_api_cancel = "shift/api/cancel/";
	
	const app_user_image = "shift/image/";
	
	const app_admin_api_add = "shift/admin/api/add/";
	const app_admin_api_remove = "shift/admin/api/remove/";
	const app_admin_api_save = "shift/admin/api/save/";
	const app_admin_api_search = "shift/admin/api/search/";
	
	const app_admin_image = "shift/admin/image/";
}

class Result {
	const success = "success";
	const failed = "failed";
	
	const ok = "1";
	const not_ok = "-1";
}

class LabelDate {
	const weekday = Array(
		0 => "Domingo"
	,	1 => "Lunes"
	,	2 => "Martes"
	,	3 => "Miércoles"
	,	4 => "Jueves"
	,	5 => "Viernes"
	,	6 => "Sábado"
	);	

	const mysql_weekday = Array(
		0 => 6 #Domingo
	,	1 => 0 #Lunes
	,	2 => 1 #Martes
	,	3 => 2 #Miércoles
	,	4 => 3 #Jueves
	,	5 => 4 #Viernes
	,	6 => 5 #Sábado
	);	
	
	const month = Array(
		1 => "Enero"
	,	2 => "Febrero"
	,	3 => "Marzo"
	,	4 => "Abril"
	,	5 => "Mayo"
	,	6 => "Junio"
	,	7 => "Julio"
	,	8 => "Agosto"
	,	9 => "Septiembre"
	,	10 => "Octubre"
	,	11 => "Noviembre"
	,	12 => "Diciembre"
	);
}

class Genre {
	const D_Male = "Dr.";
	const D_Female = "Dra.";
	
	const P_Male = "Sr.";
	const P_Female = "Sra.";
}
