(function () {
    var rssFeedRendererApp = angular.module('rssFeedRendererApp');

    rssFeedRendererApp.controller(
        'MainController',
        ['$scope', '$http', 'rssFeedUrl', 'numberOfEntries', function ($scope, $http, rssFeedUrl, numberOfEntries) {

            this.feedEntries = [];

            $http.post(
                'index.php?application=Chamilo\\Libraries\\Ajax&go=FetchRssEntries',
                $.param({'rss_feed_url': rssFeedUrl, 'number_of_entries': numberOfEntries}),
                {headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}}
            ).success(
                angular.bind(this, function (result) {
                    this.feedEntries = result.properties;
                })
            );

        }]
    );

})();