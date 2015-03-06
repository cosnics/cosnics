$(function ()
{
	var colours = ['#ff0000', '#f2ef00', '#00ff00', '#00ffff', '#0000ff', '#ff00ff', '#0080ff', '#ff0080', '#00ff80', '#ff8000', '#8000ff'],
		currentPolygon = null,
		positions = [];
	
	/********************************
	 * Functionality to draw hotspots
	 ********************************/

	function redrawPolygon(question_id)
	{
		$('.polygon_fill_' + currentPolygon, $('#hotspot_image_' + question_id)).remove();
		$('.polygon_line_' + currentPolygon, $('#hotspot_image_' + question_id)).remove();

		$('#hotspot_image_' + question_id).fillPolygon(positions[currentPolygon].X, positions[currentPolygon].Y, {clss: 'polygon_fill_' + currentPolygon, color: colours[currentPolygon], alpha: 0.5});
		$('#hotspot_image_' + question_id).drawPolygon(positions[currentPolygon].X, positions[currentPolygon].Y, {clss: 'polygon_line_' + currentPolygon, color: colours[currentPolygon], stroke: 1, alpha: 0.9});
	}
	
	function resetPolygonObject(question_id, id)
	{
		currentPolygon = id;
		
		positions[currentPolygon] = {};
		positions[currentPolygon].X = [];
		positions[currentPolygon].Y = [];
		
		$('.polygon_fill_' + currentPolygon, $('#hotspot_image_' + question_id)).remove();
		$('.polygon_line_' + currentPolygon, $('#hotspot_image_' + question_id)).remove();
	}
	
	function initializePolygons()
	{
		$('input[name*="coordinates_"]').each(function (i)
		{
			var name = $(this).attr('name');
			var name_split = name.split('_');
			var question_id = name_split[1];
			var id = name_split[2];
			
			var fieldValue = $(this).val();
			
			if (fieldValue !== '')
			{
				fieldValue = unserialize(fieldValue);
				
				currentPolygon = id;
				resetPolygonObject(question_id, id);
				
				$.each(fieldValue, function (index, item)
				{
					positions[id].X.push(item[0]);
					positions[id].Y.push(item[1]);
				});
				
				redrawPolygon(question_id);
			}
		});
	}
	
	$(document).ready(function ()
	{
		// Initialize possible existing polygons
		initializePolygons();
	});
	
});