/*
 * global $, document, renderFckEditor, getPath, getTranslation, getTheme,
 * setMemory, doAjaxPost, serialize, unserialize
 */

$(function()
{
    var colours = [ '#ff0000', '#f2ef00', '#00ff00', '#00ffff', '#0000ff', '#ff00ff', '#0080ff', '#ff0080', '#00ff80', '#ff8000', '#8000ff' ], offset, currentPolygon = null, positions = [], skippedOptions = 0;

    /***************************************************************************
     * Functionality to draw hotspots
     **************************************************************************/

    function redrawPolygon()
    {
        $('.polygon_fill_' + currentPolygon, $('#hotspot_image')).remove();
        $('.polygon_line_' + currentPolygon, $('#hotspot_image')).remove();

        $('#hotspot_image').fillPolygon(positions[currentPolygon].X, positions[currentPolygon].Y, {
            clss : 'polygon_fill_' + currentPolygon,
            color : colours[currentPolygon],
            alpha : 0.5
        });
        $('#hotspot_image').drawPolygon(positions[currentPolygon].X, positions[currentPolygon].Y, {
            clss : 'polygon_line_' + currentPolygon,
            color : colours[currentPolygon],
            stroke : 1,
            alpha : 0.9
        });
    }

    function resetPolygonObject(id)
    {
        currentPolygon = id;

        positions[currentPolygon] = {};
        positions[currentPolygon].X = [];
        positions[currentPolygon].Y = [];

        $('.polygon_fill_' + currentPolygon, $('#hotspot_image')).remove();
        $('.polygon_line_' + currentPolygon, $('#hotspot_image')).remove();
    }

    function initializePolygons()
    {
        $('div.coordinates').each(function(i)
        {
            var fieldIds = $(this).attr('id').split('_'), id = fieldIds[1], fieldValue = $(this).html();

            if (fieldValue !== '')
            {
                fieldValue = unserialize(fieldValue);

                currentPolygon = id;
                resetPolygonObject(id);

                $.each(fieldValue, function(index, item)
                {
                    positions[id].X.push(item[0]);
                    positions[id].Y.push(item[1]);
                });

                redrawPolygon();
            }
        });
    }

    $(document).ready(function()
    {
        // Initialize possible existing polygons
        // Possible when going back to a previous page or the likes.
        initializePolygons();
    });

});