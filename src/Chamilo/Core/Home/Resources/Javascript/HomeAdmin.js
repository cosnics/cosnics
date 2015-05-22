function recalculate (row, colsAmount)
{
	var res = getColumnWidths(row, colsAmount);
	var total = res.length - 1;
	
	for (var i=0; i < res.length; i++)
	{
		if (res[i] > 0)
		{
			total += parseInt(res[i]);
		}
	}
	
	var left = 100 - total;	
	var tagName = 'row'+row+'width';
	var elm = document.getElementById(tagName);
	
	elm.value = left;
	if (left < 0)
	{
		elm.style.color = "#FF0000";
	}
	else if(left == 0)
	{
		elm.style.color = "#008000";
	}
	else
	{
		elm.style.color = "#000000";
	}
	
}

function getColumnWidths(row, colsAmount)
{
	var res = new Array();
	for (var col = 1; col <= colsAmount; col++)
	{ 
		var tagName = 'row'+row+'[column'+col+'][width]';
		
		var allElements = document.getElementsByName(tagName);
					
		res[res.length] = allElements[0].value;
	}
	return res;
}