var start_date = new Date();
var stop_date = new Date();
	
var retrieve_start_date = function()
{
	var name = $(this).attr("name");
	var value = $(this).attr("value");
	var type = name.substring(name.length - 2, name.length - 1);
	parse_type_to_date(type, start_date, value);
}

var set_stop_date = function()
{
	var name = $(this).attr("name");
	var value = $(this).attr("value");
	var type = name.substring(name.length - 2, name.length - 1);

	parse_date_to_type(type, stop_date, $(this));
}

var parse_type_to_date = function(type, date_object, value)
{
	if(type == 'd')
	{
		date_object.setDate(value);
		return;	
	}
	
	if(type == 'F')
	{
		date_object.setMonth(value - 1);
		return;	
	}
	
	if(type == 'Y')
	{
		date_object.setFullYear(value);
		return;	
	}
	
	if(type == 'H')
	{
		date_object.setHours(value);
		return;	
	}
	
	if(type == 'i')
	{
		date_object.setMinutes(value);
		return;	
	}
	
}

var parse_date_to_type = function(type, date_object, object)
{
	if(type == 'd')
	{
		object.attr("value",date_object.getDate());
		return;	
	}
	
	if(type == 'F')
	{
		object.attr("value",date_object.getMonth() + 1);
		return;	
	}
	
	if(type == 'Y')
	{
		object.attr("value",date_object.getFullYear());
		return;	
	}
	
	if(type == 'H')
	{
		object.attr("value",date_object.getHours());
		return;	
	}
	
	if(type == 'i')
	{
		object.attr("value",date_object.getMinutes());
		return;	
	}
	
}