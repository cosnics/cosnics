(function () {
    var rssFeedRendererApp = angular.module('rssFeedRendererApp', ['ngSanitize']);

    rssFeedRendererApp.directive('rssFeedRenderer', function() {
        return {
            restrict: 'E',
            transclude: true,
            scope: {
                'rssFeedUrl': '@',
                'numberOfEntries': '@'
            },
            template: '<ng-transclude></ng-transclude>',
            controller: 'MainController',
            controllerAs: 'main',
            link: function(scope, element, attrs, ctrl, transclude) {
                transclude(scope, function(clone, scope) {
                    element.append(clone);
                });
            }
        }
    });

    rssFeedRendererApp.controller(
        'MainController',
        ['$scope', '$http', function ($scope, $http) {

            this.feedEntries = [];

            $http.post(
                'index.php?application=Chamilo\\Libraries\\Ajax&go=FetchRssEntries',
                $.param({'rss_feed_url': $scope.rssFeedUrl, 'number_of_entries': $scope.numberOfEntries}),
                {headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}}
            ).then(
                angular.bind(this, function (result) {
                    if(result && result.data.properties) {
                        this.feedEntries = result.data.properties;
                    }
                })
            );

        }]
    );

})();