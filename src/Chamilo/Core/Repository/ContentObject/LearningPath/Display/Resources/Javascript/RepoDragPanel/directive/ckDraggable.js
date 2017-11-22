(function () {
    var repoDragPanelApp = angular.module('repoDragPanelApp');

    repoDragPanelApp
        .directive('ckDraggable', ['$document', function ($document) {
            return {
                link: function (scope, element, attr) {
                    element.on('dragstart', function (event) {
                        event.originalEvent.dataTransfer.setData("text/html", event.originalEvent.target.textContent);
                        event.originalEvent.dataTransfer.setData("data-co-id", scope.co.id);
                        event.originalEvent.dataTransfer.setData("data-security-code", scope.co.securityCode);
                        event.originalEvent.dataTransfer.setData("data-type", scope.co.type);
                    });
                }
            };
        }]);
})();