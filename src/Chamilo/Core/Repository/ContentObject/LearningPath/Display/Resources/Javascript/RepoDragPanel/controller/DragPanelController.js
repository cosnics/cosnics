(function () {
    var repoDragPanelApp = angular.module('repoDragPanelApp');

    repoDragPanelApp.controller(
        'dragPanelCtrl',
        ['$scope', '$filter', 'RepositoryService', function ($scope, $filter, RepositoryService) {

            $scope.contentObjects = [];
            $scope.filteredContentObjects = [];
            $scope.categories = [];
            $scope.selectedCategory = null;
            $scope.searchModus = false;


            /**
             * pagination
             */
            $scope.totalItems = 0;
            $scope.currentPage = 0;

            $scope.$watch("panelIsVisible", function () {
                if ($scope.panelIsVisible) {
                    $scope.fetchCategories();
                }
            });

            $scope.$watch("currentPage", function () {
                var begin = (($scope.currentPage - 1) * 10)
                    , end = begin + 10;

                $scope.filteredContentObjects = $scope.contentObjects.slice(begin, end);
            });

            $scope.$watch("selectedCategory", function () {
                if ($scope.selectedCategory) {
                    $scope.fetchContentObjects($scope.selectedCategory.id);
                }
            });

            $scope.$watch("searchQuery", function () {
                if ($scope.searchModus) {
                    if ($scope.searchQuery === '') {
                        $scope.fetchContentObjects(0);
                        $scope.searchModus = false;
                    } else {
                        $scope.fetchContentObjects($scope.selectedCategory.id, $scope.searchQuery);
                    }
                } else {
                    if ($scope.searchQuery && $scope.searchQuery !== '') {
                        $scope.fetchContentObjects($scope.selectedCategory.id, $scope.searchQuery);
                        $scope.searchModus = true;
                    }
                }

            });

            $scope.fetchContentObjects = function (categoryId, searchQuery) {
                RepositoryService.fetchContentObjects(categoryId, searchQuery, function(data) {
                    $scope.contentObjects = data;
                    $scope.filteredContentObjects = $scope.contentObjects.slice(0, 10);
                    $scope.totalItems = data.length;
                    $scope.currentPage = 1;
                });
            };

            $scope.fetchCategories = function () {
                RepositoryService.fetchCategories(null, function(categories) {
                    $scope.categories = categories;
                    $scope.selectedCategory = $scope.categories[0];
                });
            };

            $scope.refresh = function () {
                $scope.fetchContentObjects($scope.selectedCategory.id);
            }
        }]
    );
})();
