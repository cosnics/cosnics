(function(){
    var toolbar = angular.module('toolbar', []);

    toolbar.directive('toolbar', function() {
        return {
            restrict: 'E',
            template: '<div class="toolbar"><ul class="toolbar_horizontal" ng-transclude></ul><div class="clear"></div></div>',
            replace: true,
            transclude: true
        };
    });

    toolbar.directive('toolbarItem', function() {
        return {
            restrict: 'E',
            scope: {
                "title": '@',
                "iconClass": '@',
                "href": '@',
                "action": '&',
                "target": '@'
            },
            template: '<li>' +
                '<a title="{{ title }}" href="{{ href }}" ng-click="action()" target="{{ target }}">' +
                '<div class="toolbar-item-icon {{ iconClass }}" title="{{ title }}"></div>' +
                '</a></li>',
            replace: true,
            transclude: true
        };
    });
})();
