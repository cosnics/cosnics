$(
    function () {
        var colours = ['#ff0000', '#f2ef00', '#00ff00', '#00ffff', '#0000ff', '#ff00ff', '#0080ff', '#ff0080',
                '#00ff80', '#ff8000', '#8000ff'],
            currentPolygon = null,
            positions = [];

        /********************************
         * Functionality to draw hotspots
         ********************************/

        function calculateSelectionCoordinates(posX, posY) {
            var coordinates = {
                X : [],
                Y : []
            };

            coordinates.X.push(posX);
            coordinates.X.push(posX + 7);
            coordinates.X.push(posX);
            coordinates.X.push(posX - 7);

            coordinates.Y.push(posY - 7);
            coordinates.Y.push(posY);
            coordinates.Y.push(posY + 7);
            coordinates.Y.push(posY);

            return coordinates;
        }

        function redrawPolygon(question_id, colorId, baseAlpha, borderColor) {
            $('.polygon_fill_' + currentPolygon, $('#hotspot_image_' + question_id)).remove();
            $('.polygon_line_' + currentPolygon, $('#hotspot_image_' + question_id)).remove();

            $('#hotspot_image_' + question_id).fillPolygon(
                positions[currentPolygon].X, positions[currentPolygon].Y,
                {clss: 'polygon_fill_' + currentPolygon, color: colours[colorId], alpha: baseAlpha}
            );

            if(!borderColor)
            {
                borderColor = colours[colorId];
            }

            $('#hotspot_image_' + question_id).drawPolygon(
                positions[currentPolygon].X, positions[currentPolygon].Y,
                {clss: 'polygon_line_' + currentPolygon, color: borderColor, stroke: 1, alpha: 0.9}
            );
        }

        function resetPolygonObject(question_id, id) {
            currentPolygon = id;

            positions[currentPolygon] = {};
            positions[currentPolygon].X = [];
            positions[currentPolygon].Y = [];

            $('.polygon_fill_' + currentPolygon, $('#hotspot_image_' + question_id)).remove();
            $('.polygon_line_' + currentPolygon, $('#hotspot_image_' + question_id)).remove();
        }

        function initializePolygons() {
            var polygonId = 0;

            $('input[name*="coordinates_"]').each(
                function (i) {
                    var name = $(this).attr('name');
                    var name_split = name.split('_');
                    var question_id = name_split[1];
                    var id = name_split[2];

                    var fieldValue = $(this).val();

                    if (fieldValue !== '') {
                        fieldValue = unserialize(fieldValue);

                        currentPolygon = polygonId;
                        resetPolygonObject(question_id, polygonId);

                        $.each(
                            fieldValue, function (index, item) {
                                positions[polygonId].X.push(item[0]);
                                positions[polygonId].Y.push(item[1]);
                            }
                        );

                        redrawPolygon(question_id, id, 0.3);
                        polygonId++;
                    }
                }
            );

            $('input[name*="hotspot_user_answers_"]').each(
                function (i) {
                    var name = $(this).attr('name');
                    var name_split = name.split('_');
                    var question_id = name_split[3];
                    var id = name_split[4];

                    var fieldValue = $(this).val();

                    if (fieldValue !== '') {
                        fieldValue = unserialize(fieldValue);

                        currentPolygon = polygonId;
                        resetPolygonObject(question_id, polygonId);

                        positions[polygonId] = calculateSelectionCoordinates(fieldValue[0], fieldValue[1]);

                        redrawPolygon(question_id, id, 0.9, 'black');
                        polygonId++;
                    }
                }
            );
        }

        $(document).ready(
            function () {
                // Initialize possible existing polygons
                initializePolygons();
            }
        );

    }
);