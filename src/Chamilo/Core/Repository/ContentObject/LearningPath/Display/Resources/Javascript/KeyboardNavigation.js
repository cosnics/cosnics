(function() {
    function keyboardPressed(e) {
        const element = e.target.nodeName.toLowerCase();
        if (element === 'input' || element === 'textarea') {
            return true;
        }

        if (e.keyCode === 37) {
            const learningPathNavigateLeft = document.querySelector('#learning-path-navigate-left');

            if (learningPathNavigateLeft) {
                learningPathNavigateLeft.click();
            }
        }

        if (e.keyCode === 39) {
            const learningPathNavigateRight = document.querySelector('#learning-path-navigate-right');

            if (learningPathNavigateRight) {
                learningPathNavigateRight.click();
            }
        }
    }

    document.addEventListener('keydown', keyboardPressed);
})();
