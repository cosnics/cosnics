$(function()
{
    function keyboardPressed(e, ui) {
        var element = e.target.nodeName.toLowerCase();
        if (element == 'input' || element == 'textarea') {
            return true;
        }

        if(e.keyCode == 37) {
            var learningPathNavigateLeft = $('#learning-path-navigate-left');

            if(learningPathNavigateLeft.length > 0) {
                learningPathNavigateLeft[0].click();
            }
        }

        if(e.keyCode == 39) {
            var learningPathNavigateRight = $('#learning-path-navigate-right');
            if(learningPathNavigateRight.length > 0) {
                learningPathNavigateRight[0].click();
            }
        }
    }

    $(document).on('keydown', keyboardPressed);
});
